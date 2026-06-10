<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Campaign;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function create(Campaign $campaign)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'Team Leader' && ! $campaign->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $employees = $campaign->employees;

        return view('attendances.create', compact('campaign', 'employees'));
    }

    public function index(Campaign $campaign)
    {
        $user = auth()->user();

        // Allow if team leader assigned to campaign or HR/Super Admin/CEO
        if ($user->role === 'Team Leader' && ! $campaign->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'Unauthorized action.');
        }

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        $attendances = Attendance::where('campaign_id', $campaign->id)->with('creator')->orderBy('date', 'desc')->paginate(20);

        return view('attendances.index', compact('campaign', 'attendances'));
    }

    public function store(Request $request, Campaign $campaign)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'date' => [
                'required',
                'date',
                \Illuminate\Validation\Rule::unique('attendances')->where(fn ($query) => $query->where('campaign_id', $campaign->id))
            ],
            'notes' => 'nullable|string',
            'targets' => 'nullable|array',
        ];

        $rules['targets.*.status'] = 'nullable|in:present,absent,late,leave';
        $rules['targets.*.call_time'] = 'nullable|numeric|min:0';
        $rules['targets.*.daily_salary'] = 'nullable|numeric|min:0';

        $validated = $request->validate($rules, [
            'date.unique' => 'An attendance record for this date already exists for this campaign.'
        ]);

        // For team leaders, ensure they are assigned to the campaign
        if ($user->role === 'Team Leader') {
            if (! $campaign->users()->where('users.id', $user->id)->exists()) {
                abort(403, 'Unauthorized action.');
            }
        }

        $filteredTargets = [];
        if (! empty($validated['targets'])) {
            foreach ($validated['targets'] as $id => $data) {
                // Backward compatibility if an old form submits a string
                if (!is_array($data)) {
                    if (!empty($data)) {
                        $filteredTargets[$id] = ['status' => $data];
                    }
                    continue;
                }
                
                if (!empty($data['status']) || is_numeric($data['call_time'] ?? null) || is_numeric($data['daily_salary'] ?? null)) {
                    $filteredTargets[$id] = [
                        'status' => $data['status'] ?? null,
                        'call_time' => $data['call_time'] ?? null,
                        'daily_salary' => $data['daily_salary'] ?? null,
                    ];
                }
            }
        }

        $attendance = Attendance::create([
            'campaign_id' => $campaign->id,
            'created_by' => $user->id,
            'targets' => ! empty($filteredTargets) ? ['employees' => $filteredTargets] : null,
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('campaigns.show', $campaign)->with('success', 'Attendance record created.');
    }

    public function show(Campaign $campaign, Attendance $attendance)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'Team Leader' && ! $campaign->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $employees = $campaign->employees;
        return view('attendances.show', compact('campaign', 'attendance', 'employees'));
    }

    public function edit(Campaign $campaign, Attendance $attendance)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'Team Leader' && ! $campaign->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $employees = $campaign->employees;
        return view('attendances.edit', compact('campaign', 'attendance', 'employees'));
    }

    public function update(Request $request, Campaign $campaign, Attendance $attendance)
    {
        $user = auth()->user();

        if (! in_array($user->role, ['Team Leader', 'HR Manager', 'Super Admin'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($user->role === 'Team Leader' && ! $campaign->users()->where('users.id', $user->id)->exists()) {
            abort(403, 'Unauthorized action.');
        }

        $rules = [
            'date' => [
                'required',
                'date',
                \Illuminate\Validation\Rule::unique('attendances')->where(fn ($query) => $query->where('campaign_id', $campaign->id))->ignore($attendance->id)
            ],
            'notes' => 'nullable|string',
            'targets' => 'nullable|array',
        ];

        $rules['targets.*.status'] = 'nullable|in:present,absent,late,leave';
        $rules['targets.*.call_time'] = 'nullable|numeric|min:0';
        $rules['targets.*.daily_salary'] = 'nullable|numeric|min:0';

        $validated = $request->validate($rules, [
            'date.unique' => 'An attendance record for this date already exists for this campaign.'
        ]);

        $filteredTargets = [];
        if (! empty($validated['targets'])) {
            foreach ($validated['targets'] as $id => $data) {
                // Backward compatibility if an old form submits a string
                if (!is_array($data)) {
                    if (!empty($data)) {
                        $filteredTargets[$id] = ['status' => $data];
                    }
                    continue;
                }

                if (!empty($data['status']) || is_numeric($data['call_time'] ?? null) || is_numeric($data['daily_salary'] ?? null)) {
                    $filteredTargets[$id] = [
                        'status' => $data['status'] ?? null,
                        'call_time' => $data['call_time'] ?? null,
                        'daily_salary' => $data['daily_salary'] ?? null,
                    ];
                }
            }
        }

        $attendance->update([
            'targets' => ! empty($filteredTargets) ? ['employees' => $filteredTargets] : null,
            'date' => $validated['date'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('campaigns.attendance.index', $campaign)->with('success', 'Attendance record updated.');
    }
}
