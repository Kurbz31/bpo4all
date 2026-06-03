<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Attendances — {{ $campaign->name }}</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center p-2 bg-gray-100 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-200 transition shadow-sm" title="Back to Campaign">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                @if(Auth::user()->role === 'HR Manager' || Auth::user()->role === 'Super Admin' || (Auth::user()->role === 'Team Leader' && $campaign->users->contains('id', Auth::user()->id)))
                    <a href="{{ route('campaigns.attendance.create', $campaign) }}" class="inline-flex items-center p-2 bg-indigo-600 border border-transparent rounded-lg text-white hover:bg-indigo-700 transition shadow-sm" title="Create Attendance">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </a>
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
                                            <a href="{{ route('campaigns.attendance.show', [$campaign, $attendance]) }}" class="inline-flex p-1.5 bg-indigo-50 border border-indigo-100 text-indigo-600 rounded-lg hover:bg-indigo-100 transition shadow-sm" title="View Attendance">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>
                                            <a href="{{ route('campaigns.attendance.edit', [$campaign, $attendance]) }}" class="inline-flex p-1.5 bg-amber-50 border border-amber-100 text-amber-600 rounded-lg hover:bg-amber-100 transition shadow-sm" title="Edit Attendance">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
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
