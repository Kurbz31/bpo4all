<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Campaign') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('campaigns.update', $campaign) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div>
                            <x-input-label for="name" :value="__('Campaign Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $campaign->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $campaign->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="hours_of_work" :value="__('Hours of Work')" />
                            <x-text-input id="hours_of_work" class="block mt-1 w-full" type="number" name="hours_of_work" step="0.01" min="0" :value="old('hours_of_work', $campaign->hours_of_work)" />
                            <x-input-error :messages="$errors->get('hours_of_work')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="attendance_method" :value="__('Attendance Method')" />
                            @php($attendanceMethodOptions = \App\Models\Campaign::attendanceMethodOptions())
                            <select id="attendance_method" name="attendance_method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled {{ old('attendance_method', $campaign->attendance_method) ? '' : 'selected' }}>Select an attendance method...</option>
                                @foreach($attendanceMethodOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('attendance_method', $campaign->attendance_method) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('attendance_method')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label :value="__('Assigned Team Leaders & HR')" />
                            <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 border border-gray-300 rounded-md p-4 max-h-64 overflow-y-auto bg-gray-50">
                                @php($selectedMembers = collect(old('user_ids', $campaign->users->pluck('id')->all())))
                                @foreach ($users as $user)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            @checked($selectedMembers->contains($user->id))>
                                        <span class="ml-2 text-sm text-gray-700">{{ $user->name }} <span class="text-xs text-gray-500">({{ $user->role }})</span></span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('user_ids')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('campaigns.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Cancel</a>
                            <x-primary-button>{{ __('Update Campaign') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
