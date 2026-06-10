<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->role === 'Team Leader') {
            $query = $user->campaigns()->with(['employees', 'users']);
        } else {
            $query = Campaign::with(['employees', 'users']);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $campaigns = $query->paginate(10);

        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        if (in_array(auth()->user()->role, ['Team Leader', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        $users = \App\Models\User::all();
        return view('campaigns.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (in_array(auth()->user()->role, ['Team Leader', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hours_of_work' => 'nullable|numeric|min:0',
            'attendance_method' => ['required', Rule::in(array_keys(Campaign::attendanceMethodOptions()))],
            'minimum_call_time' => 'required_if:attendance_method,' . Campaign::ATTENDANCE_METHOD_CALL_TIME . '|nullable|numeric|min:0',
            'daily_salary' => 'required_with:attendance_method|nullable|numeric|min:0',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $campaign = Campaign::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'hours_of_work' => $validated['hours_of_work'] ?? null,
            'attendance_method' => $validated['attendance_method'],
            'minimum_call_time' => $validated['minimum_call_time'] ?? null,
            'daily_salary' => $validated['daily_salary'] ?? null,
        ]);

        if (!empty($validated['user_ids'])) {
            $campaign->users()->sync($validated['user_ids']);
        }

        return redirect()->route('campaigns.index')->with('success', 'Campaign created successfully.');
    }

    public function show(Campaign $campaign)
    {
        $campaign->load(['employees', 'users']);
        return view('campaigns.show', compact('campaign'));
    }

    public function edit(Campaign $campaign)
    {
        if (in_array(auth()->user()->role, ['Team Leader', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        $users = \App\Models\User::all();
        return view('campaigns.edit', compact('campaign', 'users'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        if (in_array(auth()->user()->role, ['Team Leader', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }
        if (auth()->user()->role === 'Team Leader') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hours_of_work' => 'nullable|numeric|min:0',
            'attendance_method' => ['required', Rule::in(array_keys(Campaign::attendanceMethodOptions()))],
            'minimum_call_time' => 'required_if:attendance_method,' . Campaign::ATTENDANCE_METHOD_CALL_TIME . '|nullable|numeric|min:0',
            'daily_salary' => 'required_with:attendance_method|nullable|numeric|min:0',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $campaign->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'hours_of_work' => $validated['hours_of_work'] ?? null,
            'attendance_method' => $validated['attendance_method'],
            'minimum_call_time' => $validated['minimum_call_time'] ?? null,
            'daily_salary' => $validated['daily_salary'] ?? null,
        ]);

        if (isset($validated['user_ids'])) {
            $campaign->users()->sync($validated['user_ids']);
        } else {
            $campaign->users()->detach();
        }

        return redirect()->route('campaigns.index')->with('success', 'Campaign updated successfully.');
    }

    public function destroy(Campaign $campaign)
    {
        if (in_array(auth()->user()->role, ['Team Leader', 'CEO'])) {
            abort(403, 'Unauthorized action.');
        }

        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted successfully.');
    }
}
