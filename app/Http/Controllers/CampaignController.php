<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampaignController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->role === 'Team Leader') {
            $campaigns = $user->campaigns()->with(['employees', 'users'])->paginate(10);
        } else {
            $campaigns = Campaign::with(['employees', 'users'])->paginate(10);
        }

        return view('campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        if (auth()->user()->role === 'Team Leader') {
            abort(403, 'Unauthorized action.');
        }

        $users = \App\Models\User::all();
        return view('campaigns.create', compact('users'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role === 'Team Leader') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hours_of_work' => 'nullable|numeric|min:0',
            'attendance_method' => ['required', Rule::in(array_keys(Campaign::attendanceMethodOptions()))],
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $campaign = Campaign::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'hours_of_work' => $validated['hours_of_work'] ?? null,
            'attendance_method' => $validated['attendance_method'],
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
        if (auth()->user()->role === 'Team Leader') {
            abort(403, 'Unauthorized action.');
        }

        $users = \App\Models\User::all();
        return view('campaigns.edit', compact('campaign', 'users'));
    }

    public function update(Request $request, Campaign $campaign)
    {
        if (auth()->user()->role === 'Team Leader') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'hours_of_work' => 'nullable|numeric|min:0',
            'attendance_method' => ['required', Rule::in(array_keys(Campaign::attendanceMethodOptions()))],
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $campaign->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'hours_of_work' => $validated['hours_of_work'] ?? null,
            'attendance_method' => $validated['attendance_method'],
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
        if (auth()->user()->role === 'Team Leader') {
            abort(403, 'Unauthorized action.');
        }

        $campaign->delete();
        return redirect()->route('campaigns.index')->with('success', 'Campaign deleted successfully.');
    }
}
