<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->role === 'Super Admin') {
            $data['totalUsers'] = User::count();
            $data['totalCampaigns'] = Campaign::count();
            $data['hrManagers'] = User::where('role', 'HR Manager')->count();
            $data['teamLeaders'] = User::where('role', 'Team Leader')->count();
        } elseif ($user->role === 'HR Manager') {
            $data['totalUsers'] = User::count();
            $data['recentUsers'] = User::latest()->take(5)->get();
        } elseif ($user->role === 'Team Leader') {
            $data['myCampaigns'] = $user->campaigns()->with('employees')->get();
        } elseif ($user->role === 'CEO') {
            $data['totalUsers'] = User::count();
            $data['totalCampaigns'] = Campaign::count();
            $data['roleDistribution'] = User::selectRaw('role, count(*) as count')->groupBy('role')->get();
        }

        return view('dashboard', compact('data'));
    }
}
