<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $campaign->name }}
            </h2>

            @if(Auth::user()->role !== 'Team Leader')
            <a href="{{ route('campaigns.edit', $campaign) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Edit Campaign</a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-3">
                    <p><strong>Description:</strong> {{ $campaign->description ?: 'No description provided.' }}</p>
                    <p><strong>Agents (Employees):</strong> {{ $campaign->employees->count() }}</p>
                    <p><strong>Assigned Leaders & HR:</strong> {{ $campaign->users->count() }}</p>
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
