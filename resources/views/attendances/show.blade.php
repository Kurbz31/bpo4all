<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                View Attendance - {{ $campaign->name }}
            </h2>
            <a href="{{ route('campaigns.attendance.index', $campaign) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Attendance Details</h3>
                    <div class="mt-2 text-sm text-gray-600">
                        <p><span class="font-semibold">Date:</span> {{ $attendance->date?->toDateString() }}</p>
                        <p><span class="font-semibold">Created By:</span> {{ $attendance->creator?->name ?? 'N/A' }}</p>
                        <p><span class="font-semibold">Notes:</span> {{ $attendance->notes ?: 'None' }}</p>
                    </div>
                </div>

                <div class="mt-8 border-t border-gray-200 pt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @if($attendance->targets && isset($attendance->targets['employees']) && is_array($attendance->targets['employees']))
                        @foreach($attendance->targets['employees'] as $empId => $statusData)
                            @php 
                                $employee = $employees->firstWhere('id', $empId); 
                                $statusValue = is_array($statusData) ? ($statusData['status'] ?? '') : $statusData;
                                $callTime = is_array($statusData) ? ($statusData['call_time'] ?? null) : null;
                                $dailySalary = is_array($statusData) ? ($statusData['daily_salary'] ?? null) : null;
                            @endphp
                            <div class="p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="font-medium text-sm text-gray-900 border-b pb-2 mb-2">{{ $employee?->name ?? "Employee #{$empId}" }}</div>
                                <div class="space-y-1">
                                    <div class="text-xs text-gray-600">
                                        <span class="font-semibold text-gray-500">Status:</span> 
                                        <span class="uppercase font-bold {{ $statusValue === 'present' ? 'text-green-600' : ($statusValue === 'absent' ? 'text-red-500' : 'text-gray-800') }}">{{ $statusValue ?: '—' }}</span>
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
                    @else
                        <p class="text-gray-500 text-sm">No agent attendance recorded for this day.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>