<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('campaigns') }}
            </h2>

            @php $canCreateCampaign = in_array(Auth::user()->role, ['Super Admin', 'HR Manager']); @endphp

            @if($canCreateCampaign)
                <a href="{{ route('campaigns.create') }}" class="inline-flex items-center p-2.5 bg-indigo-600 border border-transparent rounded-lg text-white hover:bg-indigo-700 transition shadow-sm" title="Create Campaign">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Campaign
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('success'))
                        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-sm text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-6 flex justify-between items-center">
                        <form method="GET" action="{{ route('campaigns.index') }}" class="flex text-sm w-full max-w-sm">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by campaign name..." class="w-full rounded-l-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" />
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-r-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Search
                            </button>
                            @if(request('search'))
                                <a href="{{ route('campaigns.index') }}" class="inline-flex items-center ml-2 px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Clear
                                </a>
                            @endif
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded-lg overflow-hidden">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Assigned Lead</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Members</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($campaigns as $campaign)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $campaign->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php $assignedLeads = $campaign->users; @endphp
                                            @if($assignedLeads->isNotEmpty())
                                                @foreach($assignedLeads as $leader)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mr-2 mb-1">{{ $leader->name }} <span class="ml-1 opacity-75">({{ $leader->role }})</span></span>
                                                @endforeach
                                            @else
                                                <span class="text-gray-400">None</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-gray-700">{{ $campaign->employees->count() }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex items-center space-x-2">
                                            <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex p-2 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-100 transition shadow-sm" title="View Campaign">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            @php
                                                $user = Auth::user();
                                                $canViewAttendance = false;
                                                if (in_array($user->role, ['HR Manager', 'Super Admin', 'CEO'])) {
                                                    $canViewAttendance = true;
                                                }
                                                if ($user->role === 'Team Leader' && $campaign->users->contains('id', $user->id)) {
                                                    $canViewAttendance = true;
                                                }
                                            @endphp

                                            @if($canViewAttendance)
                                                <a href="{{ route('campaigns.attendance.index', $campaign) }}" class="inline-flex p-2 bg-emerald-50 border border-emerald-100 text-emerald-600 rounded-lg hover:bg-emerald-100 transition shadow-sm" title="Attendance Records">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                                    </svg>
                                                </a>
                                            @endif

                                            @if(!in_array(Auth::user()->role, ['Team Leader', 'CEO']))
                                                <a href="{{ route('campaigns.edit', $campaign) }}" class="inline-flex p-2 bg-amber-50 border border-amber-100 text-amber-600 rounded-lg hover:bg-amber-100 transition shadow-sm" title="Edit Campaign">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this campaign?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex p-2 bg-rose-50 border border-rose-100 text-rose-600 rounded-lg hover:bg-rose-100 transition shadow-sm" title="Delete Campaign">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No campaigns created yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
