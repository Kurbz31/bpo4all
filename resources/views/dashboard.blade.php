<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }} - {{ Auth::user()->role }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 font-medium">
                    Hello {{ Auth::user()->name }}, welcome to your {{ Auth::user()->role }} control panel!
                </div>
            </div>

            <!-- Role Specific Content -->
            @if(Auth::user()->role === 'Super Admin')
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="bg-indigo-50 border border-indigo-200 rounded-xl p-6 text-center shadow-sm hover:shadow-lg transform hover:-translate-y-1 transition">
                        <div class="text-indigo-600 text-sm font-bold uppercase tracking-wide">Total Users</div>
                        <div class="text-3xl font-extrabold text-indigo-900 mt-2">{{ $data['totalUsers'] }}</div>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-xl p-6 text-center shadow-sm hover:shadow-lg transform hover:-translate-y-1 transition">
                        <div class="text-green-600 text-sm font-bold uppercase tracking-wide">Total Campaigns</div>
                        <div class="text-3xl font-extrabold text-green-900 mt-2">{{ $data['totalCampaigns'] }}</div>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 text-center shadow-sm hover:shadow-lg transform hover:-translate-y-1 transition">
                        <div class="text-blue-600 text-sm font-bold uppercase tracking-wide">HR Managers</div>
                        <div class="text-3xl font-extrabold text-blue-900 mt-2">{{ $data['hrManagers'] }}</div>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-xl p-6 text-center shadow-sm hover:shadow-lg transform hover:-translate-y-1 transition">
                        <div class="text-orange-600 text-sm font-bold uppercase tracking-wide">Team Leaders</div>
                        <div class="text-3xl font-extrabold text-orange-900 mt-2">{{ $data['teamLeaders'] }}</div>
                    </div>
                </div>
            @endif

            @if(Auth::user()->role === 'HR Manager')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-white shadow-sm border border-gray-100 sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">Company Size</h3>
                        <p class="text-4xl font-extrabold text-indigo-600">{{ $data['totalUsers'] }} <span class="text-sm font-medium text-gray-500">Employees</span></p>
                        <div class="mt-4">
                            <a href="{{ route('users.create') }}" class="text-sm text-indigo-600 hover:underline">Quick Add Employee &rarr;</a>
                        </div>
                    </div>

                    <div class="bg-white shadow-sm border border-gray-100 sm:rounded-lg p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Recently Added Employees</h3>
                        <ul class="space-y-3">
                            @foreach($data['recentUsers'] as $user)
                                <li class="flex justify-between items-center text-sm">
                                    <span class="font-medium text-gray-700">{{ $user->name }}</span>
                                    <span class="bg-gray-100 text-gray-600 px-2 py-1 rounded text-xs">{{ $user->role }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(Auth::user()->role === 'Team Leader')
                <div class="bg-white shadow-sm border border-gray-100 sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">My Campaigns</h3>
                    @if($data['myCampaigns']->isEmpty())
                        <p class="text-gray-500">You are not leading any campaigns yet.</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($data['myCampaigns'] as $campaign)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <h4 class="font-bold text-lg text-indigo-600">{{ $campaign->name }}</h4>
                                    <p class="text-sm text-gray-600 mt-1 mb-3">{{ $campaign->description ?: 'No description' }}</p>
                                    <div class="flex justify-between items-center mt-4 border-t pt-3">
                                        <span class="text-sm text-gray-500">{{ $campaign->employees->count() }} Employees</span>
                                        <a href="{{ route('campaigns.show', $campaign) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View Campaign</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

            @if(Auth::user()->role === 'CEO')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 sm:rounded-lg p-6 border border-blue-100">
                        <h3 class="text-lg font-bold text-blue-900 mb-2">Total Workforce</h3>
                        <p class="text-4xl font-extrabold text-blue-700">{{ $data['totalUsers'] }}</p>
                    </div>

                    <div class="bg-green-50 sm:rounded-lg p-6 border border-green-100">
                        <h3 class="text-lg font-bold text-green-900 mb-2">Total Campaigns</h3>
                        <p class="text-4xl font-extrabold text-green-700">{{ $data['totalCampaigns'] }}</p>
                    </div>
                </div>

                <div class="bg-white shadow-sm border border-gray-100 sm:rounded-lg p-6 mt-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Role Distribution</h3>
                    <div class="space-y-4">
                        @foreach($data['roleDistribution'] as $dist)
                            <div>
                                <div class="flex justify-between text-sm font-medium mb-1">
                                    <span class="text-gray-700">{{ $dist->role }}</span>
                                    <span class="text-gray-900">{{ $dist->count }}</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ ($dist->count / $data['totalUsers']) * 100 }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
