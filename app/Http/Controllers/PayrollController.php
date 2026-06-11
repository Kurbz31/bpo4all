<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'Team Leader') {
            $campaigns = $user->campaigns;
            $payrolls = \App\Models\Payroll::whereIn('campaign_id', $campaigns->pluck('id'))->with('campaign')->orderBy('created_at', 'desc')->paginate(10);
        } else {
            $campaigns = Campaign::all();
            $payrolls = \App\Models\Payroll::with('campaign')->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('payrolls.index', compact('campaigns', 'payrolls'));
    }

    public function prepare(Request $request)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'year' => 'required|numeric',
            'month' => 'required|numeric|min:1|max:12',
            'period' => 'required|in:1,2',
        ]);

        $campaign = Campaign::with('employees')->findOrFail($validated['campaign_id']);

        if ($user->role === 'Team Leader' && ! $campaign->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if payroll already exists
        $existing = \App\Models\Payroll::where('campaign_id', $campaign->id)
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->where('period', $validated['period'])
            ->first();

        if ($existing) {
            return redirect()->route('payrolls.index')->with('error', 'A payroll for this period already exists. Please edit it instead.');
        }

        $year = $validated['year'];
        $month = $validated['month'];
        $period = $validated['period'];

        if ($period == 1) {
            $startDate = Carbon::create($year, $month, 1)->startOfDay();
            $endDate = Carbon::create($year, $month, 15)->endOfDay();
        } else {
            $startDate = Carbon::create($year, $month, 16)->startOfDay();
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();
        }

        $attendances = Attendance::where('campaign_id', $campaign->id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        $payrollData = [];
        foreach ($campaign->employees as $employee) {
            $payrollData[$employee->id] = [
                'employee' => $employee,
                'base_salary' => 0,
                'total_call_time' => 0,
                'days_present' => 0,
                'days_absent' => 0,
                'days_late' => 0,
                'days_leave' => 0,
            ];
        }

        foreach ($attendances as $attendance) {
            if (!empty($attendance->targets['employees'])) {
                foreach ($attendance->targets['employees'] as $empId => $data) {
                    if (isset($payrollData[$empId])) {
                        if (isset($data['daily_salary']) && is_numeric($data['daily_salary'])) {
                            $payrollData[$empId]['base_salary'] += $data['daily_salary'];
                        }
                        
                        if (isset($data['call_time']) && is_numeric($data['call_time'])) {
                            $payrollData[$empId]['total_call_time'] += $data['call_time'];
                        }

                        if (isset($data['status'])) {
                            if ($data['status'] === 'present') {
                                $payrollData[$empId]['days_present']++;
                            } elseif ($data['status'] === 'absent') {
                                $payrollData[$empId]['days_absent']++;
                            } elseif ($data['status'] === 'late') {
                                $payrollData[$empId]['days_late']++;
                            } elseif ($data['status'] === 'leave') {
                                $payrollData[$empId]['days_leave']++;
                            }
                        }
                    }
                }
            }
        }

        $periodLabel = ($period == 1 ? '1st Half' : '2nd Half') . ' of ' . Carbon::create($year, $month, 1)->format('F Y');

        return view('payrolls.prepare', compact('campaign', 'payrollData', 'year', 'month', 'period', 'periodLabel', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'period' => 'required|in:1,2',
            'details' => 'required|array',
            'details.*.base_salary' => 'required|numeric|min:0',
            'details.*.total_sale' => 'required|numeric|min:0',
            'details.*.commission' => 'required|numeric|min:0',
            'details.*.total_call_time' => 'required|numeric|min:0',
            'details.*.days_present' => 'required|numeric|min:0',
            'details.*.days_absent' => 'required|numeric|min:0',
            'details.*.days_late' => 'required|numeric|min:0',
            'details.*.days_leave' => 'required|numeric|min:0',
        ]);

        $existing = \App\Models\Payroll::where('campaign_id', $validated['campaign_id'])
            ->where('year', $validated['year'])
            ->where('month', $validated['month'])
            ->where('period', $validated['period'])
            ->first();

        if ($existing) {
            return redirect()->route('payrolls.index')->with('error', 'A payroll for this period already exists.');
        }

        $payroll = \App\Models\Payroll::create([
            'campaign_id' => $validated['campaign_id'],
            'year' => $validated['year'],
            'month' => $validated['month'],
            'period' => $validated['period'],
        ]);

        foreach ($validated['details'] as $empId => $data) {
            $base_salary = $data['base_salary'];
            $commission = $data['commission'];
            $total_salary = $base_salary + $commission;

            \App\Models\PayrollDetail::create([
                'payroll_id' => $payroll->id,
                'employee_id' => $empId,
                'base_salary' => $base_salary,
                'total_sale' => $data['total_sale'],
                'commission' => $commission,
                'total_salary' => $total_salary,
                'total_call_time' => $data['total_call_time'],
                'days_present' => $data['days_present'],
                'days_absent' => $data['days_absent'],
                'days_late' => $data['days_late'],
                'days_leave' => $data['days_leave'],
            ]);
        }

        return redirect()->route('payrolls.show', $payroll)->with('success', 'Payroll successfully generated.');
    }

    public function show(\App\Models\Payroll $payroll)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        $campaign = $payroll->campaign;
        
        if ($user->role === 'Team Leader' && ! $campaign->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $payroll->load('details.employee');

        if ($payroll->period == 1) {
            $startDate = Carbon::create($payroll->year, $payroll->month, 1)->startOfDay();
            $endDate = Carbon::create($payroll->year, $payroll->month, 15)->endOfDay();
        } else {
            $startDate = Carbon::create($payroll->year, $payroll->month, 16)->startOfDay();
            $endDate = Carbon::create($payroll->year, $payroll->month, 1)->endOfMonth()->endOfDay();
        }
        $periodLabel = ($payroll->period == 1 ? '1st Half' : '2nd Half') . ' of ' . Carbon::create($payroll->year, $payroll->month, 1)->format('F Y');

        return view('payrolls.show', compact('payroll', 'campaign', 'periodLabel', 'startDate', 'endDate'));
    }

    public function edit(\App\Models\Payroll $payroll)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $campaign = $payroll->campaign;

        if ($user->role === 'Team Leader' && ! $campaign->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $payroll->load('details.employee');

        if ($payroll->period == 1) {
            $startDate = Carbon::create($payroll->year, $payroll->month, 1)->startOfDay();
            $endDate = Carbon::create($payroll->year, $payroll->month, 15)->endOfDay();
        } else {
            $startDate = Carbon::create($payroll->year, $payroll->month, 16)->startOfDay();
            $endDate = Carbon::create($payroll->year, $payroll->month, 1)->endOfMonth()->endOfDay();
        }
        $periodLabel = ($payroll->period == 1 ? '1st Half' : '2nd Half') . ' of ' . Carbon::create($payroll->year, $payroll->month, 1)->format('F Y');

        return view('payrolls.edit', compact('payroll', 'campaign', 'periodLabel', 'startDate', 'endDate'));
    }

    public function update(Request $request, \App\Models\Payroll $payroll)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'details' => 'required|array',
            'details.*.total_sale' => 'required|numeric|min:0',
            'details.*.commission' => 'required|numeric|min:0',
        ]);

        foreach ($validated['details'] as $empId => $data) {
            $detail = \App\Models\PayrollDetail::where('payroll_id', $payroll->id)->where('employee_id', $empId)->first();
            if ($detail) {
                $detail->update([
                    'total_sale' => $data['total_sale'],
                    'commission' => $data['commission'],
                    'total_salary' => $detail->base_salary + $data['commission'],
                ]);
            }
        }

        return redirect()->route('payrolls.show', $payroll)->with('success', 'Payroll updated successfully.');
    }
}
