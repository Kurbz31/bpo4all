<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('campaigns') }}
            </h2>

            @php $canCreateCampaign = in_array(Auth::user()->role, ['Super Admin', 'HR Manager']); @endphp

            @if($canCreateCampaign)
                <a href="{{ route('campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-900 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="{{ route('campaigns.show', $campaign) }}" class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 hover:bg-gray-200 transition">View</a>
                                            @php
                                                $user = Auth::user();
                                                $canCreateAttendance = false;
                                                if (in_array($user->role, ['HR Manager', 'Super Admin'])) {
                                                    $canCreateAttendance = true;
                                                }
                                                if ($user->role === 'Team Leader' && $campaign->users->contains('id', $user->id)) {
                                                    $canCreateAttendance = true;
                                                }
                                            @endphp

                                            @if($canCreateAttendance)
                                                <a href="{{ route('campaigns.attendance.index', $campaign) }}" class="px-3 py-1 rounded-full bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition">Attendance</a>
                                            @endif

                                            @if(Auth::user()->role !== 'Team Leader')
                                                <a href="{{ route('campaigns.edit', $campaign) }}" class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition">Edit</a>
                                                <form action="{{ route('campaigns.destroy', $campaign) }}" method="POST" class="inline-block" onsubmit="return confirm('Delete this campaign?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1 rounded-full bg-red-50 text-red-700 hover:bg-red-100 transition">Delete</button>
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
