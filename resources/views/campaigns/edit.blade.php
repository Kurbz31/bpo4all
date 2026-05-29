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
                            <select id="attendance_method" name="attendance_method" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required onchange="toggleCallTimeFields()">
                                <option value="" disabled {{ old('attendance_method', $campaign->attendance_method) ? '' : 'selected' }}>Select an attendance method...</option>
                                @foreach($attendanceMethodOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(old('attendance_method', $campaign->attendance_method) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('attendance_method')" class="mt-2" />
                        </div>

                        <div id="call_time_fields" style="display: {{ old('attendance_method', $campaign->attendance_method) === \App\Models\Campaign::ATTENDANCE_METHOD_CALL_TIME ? 'block' : 'none' }};" class="p-4 border rounded-md bg-gray-50 space-y-4">
                            <div>
                                <x-input-label for="minimum_call_time" :value="__('Minimum Call Time (hours)')" />
                                <x-text-input id="minimum_call_time" class="block mt-1 w-full" type="number" name="minimum_call_time" step="0.01" min="0" :value="old('minimum_call_time', $campaign->minimum_call_time)" />
                                <x-input-error :messages="$errors->get('minimum_call_time')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="daily_salary" :value="__('Daily Salary')" />
                                <x-text-input id="daily_salary" class="block mt-1 w-full" type="number" name="daily_salary" step="0.01" min="0" :value="old('daily_salary', $campaign->daily_salary)" />
                                <x-input-error :messages="$errors->get('daily_salary')" class="mt-2" />
                            </div>
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

    <script>
        function toggleCallTimeFields() {
            const method = document.getElementById('attendance_method').value;
            const fields = document.getElementById('call_time_fields');
            if (method === '{{ \App\Models\Campaign::ATTENDANCE_METHOD_CALL_TIME }}') {
                fields.style.display = 'block';
                document.getElementById('minimum_call_time').required = true;
                document.getElementById('daily_salary').required = true;
            } else {
                fields.style.display = 'none';
                document.getElementById('minimum_call_time').required = false;
                document.getElementById('daily_salary').required = false;
                
                // Optional: you can un-comment these if you want to clear values 
                // when switching away from Call Time during edit:
                // document.getElementById('minimum_call_time').value = '';
                // document.getElementById('daily_salary').value = '';
            }
        }
        
        // Initialize on load
        document.addEventListener('DOMContentLoaded', toggleCallTimeFields);
    </script>
</x-app-layout>
