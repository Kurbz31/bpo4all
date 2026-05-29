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
                                            <div class="text-xs text-gray-500 mt-1">Created: {{ $attendance->created_at->diffForHumans() }}</div>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('campaigns.attendance.show', [$campaign, $attendance]) }}" class="px-3 py-1 bg-indigo-50 text-indigo-700 text-sm rounded-full hover:bg-indigo-100 transition">View</a>
                                            <a href="{{ route('campaigns.attendance.edit', [$campaign, $attendance]) }}" class="px-3 py-1 bg-amber-50 text-yellow-700 text-sm rounded-full hover:bg-yellow-100 transition">Edit</a>
                                        </div>
                                    </div>
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
