<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $campaign->name }}
            </h2>

            <div class="flex items-center space-x-2">
                @php
                    $user = Auth::user();
                    $canCreateAttendance = false;
                    if ($user->role === 'HR Manager' || $user->role === 'Super Admin') {
                        $canCreateAttendance = true;
                    }
                    if ($user->role === 'Team Leader' && $campaign->users->contains('id', $user->id)) {
                        $canCreateAttendance = true;
                    }
                @endphp

                @if($canCreateAttendance)
                    <a href="{{ route('campaigns.attendance.create', $campaign) }}" class="inline-flex items-center p-2.5 bg-indigo-50 border border-indigo-200 text-indigo-600 rounded-lg hover:bg-indigo-100 transition shadow-sm" title="Create Attendance">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        Create Attendance
                    </a>
                @endif

                @if(Auth::user()->role !== 'Team Leader')
                <a href="{{ route('campaigns.edit', $campaign) }}" class="inline-flex items-center p-2.5 bg-amber-50 border border-amber-200 text-amber-600 rounded-lg hover:bg-amber-100 transition shadow-sm" title="Edit Campaign">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                Edit Campaign
                </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    <p><strong>Description:</strong> {{ $campaign->description ?: 'No description provided.' }}</p>
                    <p><strong>Hours of Work:</strong> {{ $campaign->hours_of_work ?? 'Not set' }}</p>
                    <p><strong>Attendance Method:</strong> {{ $campaign->attendanceMethodLabel() }}</p>
                    <p><strong>Agents (Employees):</strong> {{ $campaign->employees->count() }}</p>
                    
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 font-semibold text-lg">Assigned Team Leaders & HR</h3>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($campaign->users as $user)
                            <div class="rounded-lg border border-gray-200 p-4">
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                <div class="mt-2 text-xs uppercase tracking-wide text-gray-500">{{ $user->role }}</div>
                            </div>
                        @empty
                            <p class="text-gray-500">No leaders or HR assigned yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="mb-4 font-semibold text-lg">Call Center Agents</h3>
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($campaign->employees as $employee)
                            <div class="rounded-lg border border-gray-200 p-4 bg-gray-50">
                                <div class="font-medium text-gray-900">{{ $employee->name }}</div>
                            </div>
                        @empty
                            <p class="text-gray-500">No agents added yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
