@extends('layouts.app')

@section('title', 'Dashboard - Meeting Agenda Management System')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Meeting Agenda Management System</h1>
                    <p class="text-gray-600 mt-1">Minutes Section - Quezon City Hall</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Welcome back,</p>
                        <p class="font-medium text-gray-900">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white font-medium">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Today's Meetings</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $todayMeetings ?? '3' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Completed Agendas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $completedAgendas ?? '12' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Pending Reviews</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $pendingReviews ?? '5' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center">
                    <div class="p-2 bg-purple-100 rounded-lg">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-500">Total Participants</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalParticipants ?? '45' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="" class="group bg-blue-50 hover:bg-blue-100 border-2 border-dashed border-blue-300 rounded-lg p-4 transition-colors duration-200">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span class="text-sm font-medium text-blue-700 group-hover:text-blue-800">Create New Agenda</span>
                        </div>
                    </a>

                    <a href="" class="group bg-green-50 hover:bg-green-100 border-2 border-dashed border-green-300 rounded-lg p-4 transition-colors duration-200">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-green-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-green-700 group-hover:text-green-800">Schedule Meeting</span>
                        </div>
                    </a>

                    <a href="" class="group bg-purple-50 hover:bg-purple-100 border-2 border-dashed border-purple-300 rounded-lg p-4 transition-colors duration-200">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-purple-700 group-hover:text-purple-800">Manage Templates</span>
                        </div>
                    </a>

                    <a href="" class="group bg-orange-50 hover:bg-orange-100 border-2 border-dashed border-orange-300 rounded-lg p-4 transition-colors duration-200">
                        <div class="text-center">
                            <svg class="w-8 h-8 text-orange-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <span class="text-sm font-medium text-orange-700 group-hover:text-orange-800">Generate Reports</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Activities and Upcoming Meetings -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Activities</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-blue-600 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">New agenda created for <strong>City Council Meeting</strong></p>
                                <p class="text-xs text-gray-500">2 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-green-600 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Meeting minutes approved for <strong>Budget Committee</strong></p>
                                <p class="text-xs text-gray-500">4 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-yellow-600 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">Agenda template updated for <strong>Planning Committee</strong></p>
                                <p class="text-xs text-gray-500">6 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="w-2 h-2 bg-purple-600 rounded-full mt-2"></div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900">New participant added to <strong>Emergency Committee</strong></p>
                                <p class="text-xs text-gray-500">1 day ago</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View all activities →</a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Meetings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Upcoming Meetings</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="border-l-4 border-blue-600 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">City Council Regular Meeting</h3>
                                    <p class="text-xs text-gray-500 mt-1">Conference Room A</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-blue-600">Today</p>
                                    <p class="text-xs text-gray-500">2:00 PM</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-l-4 border-green-600 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Budget Review Committee</h3>
                                    <p class="text-xs text-gray-500 mt-1">Conference Room B</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-green-600">Tomorrow</p>
                                    <p class="text-xs text-gray-500">10:00 AM</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-l-4 border-yellow-600 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Urban Planning Session</h3>
                                    <p class="text-xs text-gray-500 mt-1">Main Hall</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-yellow-600">Dec 28</p>
                                    <p class="text-xs text-gray-500">9:00 AM</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-l-4 border-purple-600 pl-4 py-2">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h3 class="text-sm font-medium text-gray-900">Emergency Preparedness</h3>
                                    <p class="text-xs text-gray-500 mt-1">Conference Room C</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-purple-600">Dec 30</p>
                                    <p class="text-xs text-gray-500">3:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="" class="text-blue-600 hover:text-blue-800 text-sm font-medium">View full calendar →</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Status -->
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">System Status</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">System Online</h3>
                        <p class="text-xs text-gray-500 mt-1">All services operational</p>
                    </div>

                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Database</h3>
                        <p class="text-xs text-gray-500 mt-1">Last backup: {{ date('M d, Y H:i') }}</p>
                    </div>

                    <div class="text-center">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <h3 class="text-sm font-medium text-gray-900">Maintenance</h3>
                        <p class="text-xs text-gray-500 mt-1">Scheduled: Jan 15, 2025</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
