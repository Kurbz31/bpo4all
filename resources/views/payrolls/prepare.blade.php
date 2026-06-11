<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Prepare Payroll: ') }} {{ $campaign->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <p class="text-gray-600"><strong>Period:</strong> {{ $periodLabel }} ({{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }})</p>
                <p class="text-sm text-gray-500 mt-1">Please enter the Total Sales and Commission for each agent. The base salary is computed from the attendance records.</p>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 overflow-x-auto">
                    <form method="POST" action="{{ route('payrolls.store') }}">
                        @csrf
                        <input type="hidden" name="campaign_id" value="{{ $campaign->id }}">
                        <input type="hidden" name="year" value="{{ $year }}">
                        <input type="hidden" name="month" value="{{ $month }}">
                        <input type="hidden" name="period" value="{{ $period }}">

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendance Stats</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Salary (₱)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sale</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission (₱)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($payrollData as $empId => $data)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $data['employee']->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($campaign->attendance_method === 'call_time')
                                                Mins: {{ $data['total_call_time'] }}
                                            @else
                                                P:{{ $data['days_present'] }} A:{{ $data['days_absent'] }} L:{{ $data['days_late'] }}
                                            @endif
                                            
                                            <!-- Hidden inputs to carry over stats -->
                                            <input type="hidden" name="details[{{ $empId }}][total_call_time]" value="{{ $data['total_call_time'] }}">
                                            <input type="hidden" name="details[{{ $empId }}][days_present]" value="{{ $data['days_present'] }}">
                                            <input type="hidden" name="details[{{ $empId }}][days_absent]" value="{{ $data['days_absent'] }}">
                                            <input type="hidden" name="details[{{ $empId }}][days_late]" value="{{ $data['days_late'] }}">
                                            <input type="hidden" name="details[{{ $empId }}][days_leave]" value="{{ $data['days_leave'] }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                            ₱ {{ number_format($data['base_salary'], 2) }}
                                            <input type="hidden" name="details[{{ $empId }}][base_salary]" value="{{ $data['base_salary'] }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <input type="number" step="1" min="0" name="details[{{ $empId }}][total_sale]" value="0" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full" required>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <input type="number" step="0.01" min="0" name="details[{{ $empId }}][commission]" value="0" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block w-full" required>
                                        </td>
                                    </tr>
                                @endforeach
                                @if(count($payrollData) === 0)
                                    <tr>
                                        <td colspan="100%" class="px-6 py-4 text-center text-sm text-gray-500">
                                            No employees found for this campaign.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        
                        <div class="mt-6 flex justify-end">
                            <a href="{{ route('payrolls.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit" class="ml-4 inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Save Payroll
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
