<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Attendances — {{ $campaign->name }}</h2>
            <div class="flex items-center gap-4">
                <a href="{{ route('campaigns.show', $campaign) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Back</a>
                @if(Auth::user()->role === 'HR Manager' || Auth::user()->role === 'Super Admin' || (Auth::user()->role === 'Team Leader' && $campaign->users->contains('id', Auth::user()->id)))
                    <a href="{{ route('campaigns.attendance.create', $campaign) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Create Attendance</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($attendances->isEmpty())
                        <p class="text-gray-500">No attendance records yet for this campaign.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($attendances as $attendance)
                                <div class="border rounded p-4 bg-gray-50">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <div class="text-sm font-medium">Date: {{ $attendance->date?->toDateString() }}</div>
                                            <div class="text-xs text-gray-600">Created by: {{ $attendance->creator?->name ?? 'N/A' }}</div>
                                            @if($attendance->target_role)
                                                <div class="text-xs text-gray-600">Target Role: {{ $attendance->target_role }}</div>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-600">Created: {{ $attendance->created_at->diffForHumans() }}</div>
                                    </div>

                                    @if($attendance->targets)
                                        <div class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                            @if(isset($attendance->targets['employees']) && is_array($attendance->targets['employees']))
                                                @foreach($attendance->targets['employees'] as $empId => $statusData)
                                                    @php 
                                                        $employee = $campaign->employees->firstWhere('id', $empId); 
                                                        $statusValue = is_array($statusData) ? ($statusData['status'] ?? '') : $statusData;
                                                        $callTime = is_array($statusData) ? ($statusData['call_time'] ?? null) : null;
                                                        $dailySalary = is_array($statusData) ? ($statusData['daily_salary'] ?? null) : null;
                                                    @endphp
                                                    <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                                        <div class="font-medium text-sm text-gray-900 border-b pb-2 mb-2">{{ $employee?->name ?? "Employee #{$empId}" }}</div>
                                                        <div class="space-y-1">
                                                            <div class="text-xs text-gray-600">
                                                                <span class="font-semibold text-gray-500">Status:</span> 
                                                                <span class="uppercase {{ $statusValue === 'present' ? 'text-green-600' : ($statusValue === 'absent' ? 'text-red-500' : 'text-gray-800') }}">{{ $statusValue ?: '—' }}</span>
                                                            </div>
                                                            @if($callTime !== null)
                                                                <div class="text-xs text-gray-600">
                                                                    <span class="font-semibold text-gray-500">Call Time:</span> {{ $callTime }} hrs
                                                                </div>
                                                            @endif
                                                            @if($dailySalary !== null)
                                                                <div class="text-xs text-gray-600">
                                                                    <span class="font-semibold text-gray-500">Daily Salary:</span> ₱{{ number_format((float)$dailySalary, 2) }}
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif

                                            @if(isset($attendance->targets['users']) && is_array($attendance->targets['users']))
                                                @foreach($attendance->targets['users'] as $uId)
                                                    @php $user = \App\Models\User::find($uId); @endphp
                                                    <div class="p-2 bg-white border rounded">
                                                        <div class="font-medium text-sm">{{ $user?->name ?? "User #{$uId}" }}</div>
                                                        <div class="text-xs text-gray-600">Role: {{ $user?->role ?? '—' }}</div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach

                            <div>
                                {{ $attendances->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
