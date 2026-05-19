<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Employee') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{ employeeType: '{{ auth()->user()->role === 'Team Leader' ? 'agent' : old('employeeType', 'admin') }}' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 border-b border-gray-200">

                    <!-- Toggle Buttons -->
                    @if(auth()->user()->role !== 'Team Leader')
                    <div class="flex space-x-4 mb-6">
                        <button type="button"
                                @click="employeeType = 'admin'"
                                :class="{'bg-indigo-600 text-white': employeeType === 'admin', 'bg-gray-200 text-gray-700': employeeType !== 'admin'}"
                                class="px-4 py-2 font-semibold rounded-md shadow-sm transition">
                            Admin / HR / Leader
                        </button>
                        <button type="button"
                                @click="employeeType = 'agent'"
                                :class="{'bg-indigo-600 text-white': employeeType === 'agent', 'bg-gray-200 text-gray-700': employeeType !== 'agent'}"
                                class="px-4 py-2 font-semibold rounded-md shadow-sm transition">
                            Call Center Agent
                        </button>
                    </div>
                    @endif

                    <!-- Admin Form -->
                    @if(auth()->user()->role !== 'Team Leader')
                    <form method="POST" action="{{ route('users.store') }}" x-show="employeeType === 'admin'">
                        @csrf
                        <input type="hidden" name="employeeType" value="admin">

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="role" :value="__('Role')" />
                            <select id="role" name="role" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="" disabled selected>Select a role</option>
                                <option value="Super Admin" {{ old('role') == 'Super Admin' ? 'selected' : '' }}>Super Admin</option>
                                <option value="Team Leader" {{ old('role') == 'Team Leader' ? 'selected' : '' }}>Team Leader</option>
                                <option value="CEO" {{ old('role') == 'CEO' ? 'selected' : '' }}>CEO</option>
                                <option value="HR Manager" {{ old('role') == 'HR Manager' ? 'selected' : '' }}>HR Manager</option>
                            </select>
                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none mr-4" href="{{ route('users.index') }}">Cancel</a>
                            <x-primary-button>{{ __('Save Admin User') }}</x-primary-button>
                        </div>
                    </form>
                    @endif

                    <!-- Agent Form -->
                    <form method="POST" action="{{ route('employees.store') }}" x-show="employeeType === 'agent'" x-cloak>
                        @csrf
                        <input type="hidden" name="employeeType" value="agent">

                        <div>
                            <x-input-label for="agent_name" :value="__('Agent Name')" />
                            <x-text-input id="agent_name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="campaign_id" :value="__('Campaign')" />
                            <select id="campaign_id" name="campaign_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="" disabled selected>Select a campaign...</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}" {{ old('campaign_id') == $campaign->id ? 'selected' : '' }}>
                                        {{ $campaign->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('campaign_id')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none mr-4" href="{{ route('employees.index') }}">Cancel</a>
                            <x-primary-button>{{ __('Save Agent') }}</x-primary-button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
