<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Create Attendance - {{ $campaign->name }}
            </h2>
            <a href="{{ route('campaigns.show', $campaign) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('campaigns.attendance.store', $campaign) }}">
                    @csrf

                    <div class="grid gap-4 md:grid-cols-2 mb-6">
                        <div>
                            <x-input-label for="date" :value="__('Date')" />
                            <x-text-input id="date" type="date" name="date" class="mt-1 block w-full" value="{{ old('date', now()->toDateString()) }}" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea name="notes" id="notes" class="mt-1 block w-full border-gray-300 rounded-md" rows="3">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    @if(isset($employees))
                        <div class="mb-6 rounded-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-700">Agents in {{ $campaign->name }}</p>
                                <p class="text-xs text-gray-500">Use the status column to mark each agent without opening another form.</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-white">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 w-16">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Agent</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                            @if($campaign->attendance_method === \App\Models\Campaign::ATTENDANCE_METHOD_CALL_TIME)
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Call Time (Hours)</th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Daily Salary</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($employees as $employee)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-900">{{ $employee->name }}</div>
                                                    <div class="text-xs text-gray-500">Employee ID: {{ $employee->id }}</div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if($campaign->attendance_method === \App\Models\Campaign::ATTENDANCE_METHOD_CALL_TIME)
                                                        <select name="targets[{{ $employee->id }}][status]" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <option value="" {{ old("targets.{$employee->id}.status") == '' ? 'selected' : '' }}>-- No mark --</option>
                                                            <option value="present" {{ old("targets.{$employee->id}.status") == 'present' ? 'selected' : '' }}>Present</option>
                                                            <option value="absent" {{ old("targets.{$employee->id}.status") == 'absent' ? 'selected' : '' }}>Absent</option>
                                                            <option value="late" {{ old("targets.{$employee->id}.status") == 'late' ? 'selected' : '' }}>Late</option>
                                                            <option value="leave" {{ old("targets.{$employee->id}.status") == 'leave' ? 'selected' : '' }}>On Leave</option>
                                                        </select>
                                                    @else
                                                        <select name="targets[{{ $employee->id }}]" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <option value="" {{ old("targets.{$employee->id}") == '' ? 'selected' : '' }}>-- No mark --</option>
                                                            <option value="present" {{ old("targets.{$employee->id}") == 'present' ? 'selected' : '' }}>Present</option>
                                                            <option value="absent" {{ old("targets.{$employee->id}") == 'absent' ? 'selected' : '' }}>Absent</option>
                                                            <option value="late" {{ old("targets.{$employee->id}") == 'late' ? 'selected' : '' }}>Late</option>
                                                            <option value="leave" {{ old("targets.{$employee->id}") == 'leave' ? 'selected' : '' }}>On Leave</option>
                                                        </select>
                                                    @endif
                                                </td>
                                                @if($campaign->attendance_method === \App\Models\Campaign::ATTENDANCE_METHOD_CALL_TIME)
                                                    <td class="px-4 py-3">
                                                        <x-text-input type="number" step="0.01" min="0" name="targets[{{ $employee->id }}][call_time]" value="{{ old('targets.'.$employee->id.'.call_time') }}" class="w-full call-time-input" data-emp-id="{{ $employee->id }}" placeholder="7.5" />
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <x-text-input type="number" step="0.01" min="0" name="targets[{{ $employee->id }}][daily_salary]" value="{{ old('targets.'.$employee->id.'.daily_salary') }}" class="w-full daily-salary-input" data-emp-id="{{ $employee->id }}" placeholder="500" />
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end">
                        <x-primary-button>{{ __('Create Attendance') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @if($campaign->attendance_method === \App\Models\Campaign::ATTENDANCE_METHOD_CALL_TIME)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const minCallTime = {{ $campaign->minimum_call_time ?? 'null' }};
            const defaultSalary = {{ $campaign->daily_salary ?? 'null' }};

            if (minCallTime !== null && defaultSalary !== null) {
                const callTimeInputs = document.querySelectorAll('.call-time-input');
                
                callTimeInputs.forEach(input => {
                    input.addEventListener('input', function() {
                        const empId = this.getAttribute('data-emp-id');
                        const salaryInput = document.querySelector(`.daily-salary-input[data-emp-id="${empId}"]`);
                        
                        if (salaryInput) {
                            const val = parseFloat(this.value);
                            // Set the default salary if their call time is equal or greater than the minimum
                            if (!isNaN(val) && val >= parseFloat(minCallTime)) {
                                salaryInput.value = defaultSalary;
                            } else if (!isNaN(val) && val < parseFloat(minCallTime)) {
                                // Optional: Clear it if they drop below the minimum
                                salaryInput.value = '';
                            }
                        }
                    });
                });
            }
        });
    </script>
    @endif
</x-app-layout>
