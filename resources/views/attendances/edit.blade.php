<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Attendance - {{ $campaign->name }}
            </h2>
            <a href="{{ route('campaigns.attendance.index', $campaign) }}" class="text-sm text-indigo-600 hover:text-indigo-900">Back</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('campaigns.attendance.update', [$campaign, $attendance]) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid gap-4 md:grid-cols-2 mb-6">
                        <div>
                            <x-input-label for="date" :value="__('Date')" />
                            <x-text-input id="date" type="date" name="date" class="mt-1 block w-full" value="{{ old('date', $attendance->date?->toDateString()) }}" required />
                            <x-input-error :messages="$errors->get('date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes" :value="__('Notes')" />
                            <textarea name="notes" id="notes" class="mt-1 block w-full border-gray-300 rounded-md" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
                        </div>
                    </div>

                    @if(isset($employees))
                        <div class="mb-6 rounded-lg border border-gray-200 overflow-hidden">
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-700">Agents in {{ $campaign->name }}</p>
                                <p class="text-xs text-gray-500">Update agent attendance.</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-white">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 w-16">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Agent</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                                {{ $campaign->attendance_method === \App\Models\Campaign::ATTENDANCE_METHOD_CALL_TIME ? 'Call Time (Hours)' : 'Hours of Work' }}
                                            </th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Daily Salary</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white">
                                        @foreach($employees as $employee)
                                            @php
                                                $savedTarget = $attendance->targets['employees'] ?? [];
                                                $savedData = $savedTarget[$employee->id] ?? null;
                                                $savedStatus = is_array($savedData) ? ($savedData['status'] ?? '') : $savedData;
                                                $savedCallTime = is_array($savedData) ? ($savedData['call_time'] ?? '') : '';
                                                $savedSalary = is_array($savedData) ? ($savedData['daily_salary'] ?? '') : '';
                                            @endphp
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $loop->iteration }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="font-medium text-gray-900">{{ $employee->name }}</div>
                                                    <div class="text-xs text-gray-500">Employee ID: {{ $employee->id }}</div>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <select name="targets[{{ $employee->id }}][status]" class="status-select block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" data-emp-id="{{ $employee->id }}">
                                                        <option value="" {{ old("targets.{$employee->id}.status", $savedStatus) == '' ? 'selected' : '' }}>-- No mark --</option>
                                                        <option value="present" {{ old("targets.{$employee->id}.status", $savedStatus) == 'present' ? 'selected' : '' }}>Present</option>
                                                        <option value="absent" {{ old("targets.{$employee->id}.status", $savedStatus) == 'absent' ? 'selected' : '' }}>Absent</option>
                                                        <option value="late" {{ old("targets.{$employee->id}.status", $savedStatus) == 'late' ? 'selected' : '' }}>Late</option>
                                                        <option value="leave" {{ old("targets.{$employee->id}.status", $savedStatus) == 'leave' ? 'selected' : '' }}>On Leave</option>
                                                    </select>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-text-input type="number" step="0.01" min="0" name="targets[{{ $employee->id }}][call_time]" value="{{ old('targets.'.$employee->id.'.call_time', $savedCallTime) }}" class="w-full call-time-input" data-emp-id="{{ $employee->id }}" placeholder="{{ $campaign->attendance_method === \App\Models\Campaign::ATTENDANCE_METHOD_PRESENT_ABSENT ? ($campaign->hours_of_work ?? '8') : ($campaign->minimum_call_time ?? '7.5') }}" />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <x-text-input type="number" step="0.01" min="0" name="targets[{{ $employee->id }}][daily_salary]" value="{{ old('targets.'.$employee->id.'.daily_salary', $savedSalary) }}" class="w-full daily-salary-input" data-emp-id="{{ $employee->id }}" placeholder="{{ $campaign->daily_salary ?? '500' }}" />
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-2">
                        <a href="{{ route('campaigns.attendance.index', $campaign) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                        <x-primary-button>{{ __('Update Attendance') }}</x-primary-button>
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

            const statusSelects = document.querySelectorAll('.status-select');
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value === 'absent') {
                        const empId = this.getAttribute('data-emp-id');
                        const hoursInput = document.querySelector(`.call-time-input[data-emp-id="${empId}"]`);
                        const salaryInput = document.querySelector(`.daily-salary-input[data-emp-id="${empId}"]`);
                        
                        if (hoursInput) hoursInput.value = 0;
                        if (salaryInput) salaryInput.value = 0;
                    }
                });
            });
        });
    </script>
    @elseif($campaign->attendance_method === \App\Models\Campaign::ATTENDANCE_METHOD_PRESENT_ABSENT)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const campaignHours = {{ $campaign->hours_of_work ?? 'null' }};
            const defaultSalary = {{ $campaign->daily_salary ?? 'null' }};

            const statusSelects = document.querySelectorAll('.status-select');
            
            statusSelects.forEach(select => {
                select.addEventListener('change', function() {
                    const empId = this.getAttribute('data-emp-id');
                    const hoursInput = document.querySelector(`.call-time-input[data-emp-id="${empId}"]`);
                    const salaryInput = document.querySelector(`.daily-salary-input[data-emp-id="${empId}"]`);
                    
                    if (this.value === 'present') {
                        if (hoursInput && campaignHours !== null) hoursInput.value = campaignHours;
                        if (salaryInput && defaultSalary !== null) salaryInput.value = defaultSalary;
                    } else if (this.value === 'absent') {
                        if (hoursInput) hoursInput.value = 0;
                        if (salaryInput) salaryInput.value = 0;
                    }
                });
            });
        });
    </script>
    @endif
</x-app-layout>