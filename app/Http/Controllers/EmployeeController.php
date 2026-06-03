<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Campaign;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('campaign')
            ->where('status', '!=', 'terminated');

        if (auth()->user()->role === 'Team Leader') {
            $campaignIds = auth()->user()->campaigns()->pluck('campaigns.id');
            $query->whereIn('campaign_id', $campaignIds);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('campaign', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $employees = $query->get();

        return view('employees.index', compact('employees'));
    }

    public function terminated()
    {
        if (auth()->user()->role === 'Team Leader') {
            $campaignIds = auth()->user()->campaigns()->pluck('campaigns.id');
            $employees = Employee::with('campaign')
                ->whereIn('campaign_id', $campaignIds)
                ->where('status', 'terminated')
                ->get();
        } else {
            $employees = Employee::with('campaign')
                ->where('status', 'terminated')
                ->get();
        }

        return view('employees.terminated', compact('employees'));
    }

    public function create()
    {
        if (auth()->user()->role === 'CEO') {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->user()->role === 'Team Leader') {
            $campaigns = auth()->user()->campaigns;
        } else {
            $campaigns = \App\Models\Campaign::all();
        }

        return view('users.create', compact('campaigns'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'CEO') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'campaign_id' => 'required|exists:campaigns,id',
            'status' => 'nullable|in:active,inactive,terminated',
        ]);

        if (auth()->user()->role === 'Team Leader') {
            if (!auth()->user()->campaigns()->where('campaigns.id', $validated['campaign_id'])->exists()) {
                abort(403, 'You can only assign agents to your own campaigns.');
            }
        }

        if (empty($validated['status'])) {
            $validated['status'] = 'active';
        }

        Employee::create($validated);

        return redirect()->route('employees.index')->with('success', 'Call center agent created successfully.');
    }

    public function edit(Employee $employee)
    {
        if (auth()->user()->role === 'CEO') {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->user()->role === 'Team Leader') {
            if (!auth()->user()->campaigns()->where('campaigns.id', $employee->campaign_id)->exists()) {
                abort(403, 'Unauthorized. Agent belongs to a different campaign.');
            }
            $campaigns = auth()->user()->campaigns;
        } else {
            $campaigns = Campaign::all();
        }

        return view('employees.edit', compact('employee', 'campaigns'));
    }

    public function update(Request $request, Employee $employee)
    {
        if (auth()->user()->role === 'CEO') {
            abort(403, 'Unauthorized action.');
        }
        if (auth()->user()->role === 'Team Leader') {
            if (!auth()->user()->campaigns()->where('campaigns.id', $employee->campaign_id)->exists()) {
                abort(403, 'Unauthorized. Agent belongs to a different campaign.');
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'campaign_id' => 'required|exists:campaigns,id',
            'status' => 'required|in:active,inactive,terminated',
        ]);

        if (auth()->user()->role === 'Team Leader') {
            if (!auth()->user()->campaigns()->where('campaigns.id', $validated['campaign_id'])->exists()) {
                abort(403, 'You can only assign agents to your own campaigns.');
            }
        }

        $employee->update($validated);

        if ($validated['status'] === 'terminated') {
            return redirect()->route('employees.terminated')->with('success', 'Call center agent status updated to terminated.');
        }

        return redirect()->route('employees.index')->with('success', 'Call center agent updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        if (auth()->user()->role === 'CEO') {
            abort(403, 'Unauthorized action.');
        }

        if (auth()->user()->role === 'Team Leader') {
            if (!auth()->user()->campaigns()->where('campaigns.id', $employee->campaign_id)->exists()) {
                abort(403, 'Unauthorized. Agent belongs to a different campaign.');
            }
        }

        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'Call center agent deleted successfully.');
    }
}
