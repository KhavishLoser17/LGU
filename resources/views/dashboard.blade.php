@extends('layouts.app')

@section('title', 'Dashboard - Meeting Agenda Management System')

@push('styles')
<style>
    /* Fix scrolling issues */
    html {
        scroll-behavior: smooth;
        overflow-x: hidden;
    }
    
    body {
        overflow-x: hidden;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    
    /* Prevent horizontal overflow */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        max-width: 100%;
        overflow: hidden;
    }
    
    /* Animation for cards */
    .stat-card {
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }
    
    /* Gradient backgrounds */
    .gradient-blue {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .gradient-green {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
    }
    
    .gradient-yellow {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .gradient-red {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    /* Timeline improvements */
    .timeline-item {
        position: relative;
    }
    
    .timeline-line {
        position: absolute;
        left: 16px;
        top: 32px;
        bottom: -8px;
        width: 2px;
        background: linear-gradient(to bottom, #e2e8f0, transparent);
    }
    
    .timeline-item:last-child .timeline-line {
        display: none;
    }
    
    /* Status color classes */
    .status-pending { @apply bg-yellow-100 text-yellow-800; }
    .status-approved { @apply bg-blue-100 text-blue-800; }
    .status-ongoing { @apply bg-indigo-100 text-indigo-800; }
    .status-completed { @apply bg-green-100 text-green-800; }
    .status-rejected { @apply bg-red-100 text-red-800; }
    .status-on_time { @apply bg-green-100 text-green-800; }
    .status-late { @apply bg-red-100 text-red-800; }
    .status-early { @apply bg-blue-100 text-blue-800; }
    
    /* Responsive grid fixes */
    @media (max-width: 640px) {
        .chart-container {
            height: 250px;
        }
    }
</style>
@endpush

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Enhanced Header -->
    <header class="bg-white shadow-lg border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-full mx-auto py-4 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        Dashboard
                    </h1>
                    <p class="text-sm text-gray-600 mt-1">Meeting Agenda Management System</p>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Enhanced Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Meetings Card -->
            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 gradient-blue rounded-lg p-3">
                            <i class="fas fa-calendar-alt text-white text-xl"></i>
                        </div>
                        <div class="ml-5 flex-1">
                            <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Meetings</div>
                            <div class="text-3xl font-bold text-gray-900 mt-1">{{ $meetingsCount }}</div>
                            <div class="text-sm text-green-600 font-medium mt-1">
                                <i class="fas fa-arrow-up w-3 h-3 mr-1"></i>
                                Active sessions
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Attendance Card -->
            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 gradient-green rounded-lg p-3">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div class="ml-5 flex-1">
                            <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Today's Attendance</div>
                            <div class="text-3xl font-bold text-gray-900 mt-1">{{ $todayAttendanceCount }}</div>
                            <div class="text-sm text-blue-600 font-medium mt-1">
                                <i class="fas fa-check-circle w-3 h-3 mr-1"></i>
                                Present today
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Documents Card -->
            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 gradient-yellow rounded-lg p-3">
                            <i class="fas fa-file-alt text-white text-xl"></i>
                        </div>
                        <div class="ml-5 flex-1">
                            <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Total Documents</div>
                            <div class="text-3xl font-bold text-gray-900 mt-1">{{ $documentsCount }}</div>
                            <div class="text-sm text-purple-600 font-medium mt-1">
                                <i class="fas fa-download w-3 h-3 mr-1"></i>
                                Available files
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Meetings Card -->
            <div class="stat-card bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 gradient-red rounded-lg p-3">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div class="ml-5 flex-1">
                            <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">Upcoming Meetings</div>
                            <div class="text-3xl font-bold text-gray-900 mt-1">{{ $upcomingMeetingsCount }}</div>
                            <div class="text-sm text-orange-600 font-medium mt-1">
                                <i class="fas fa-hourglass-half w-3 h-3 mr-1"></i>
                                Scheduled
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Meeting Status Chart -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Meeting Status Distribution</h2>
                        <div class="flex space-x-2">
                            <span class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded-full">Live Data</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="chart-container">
                        <canvas id="meetingStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Attendance Trend Chart -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900">Attendance Trends (Last 7 Days)</h2>
                        <div class="flex space-x-2">
                            <span class="px-3 py-1 text-xs font-medium text-green-600 bg-green-50 rounded-full">Weekly View</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="chart-container">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Recent Activities Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Recent Meetings -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Meetings</h2>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        @forelse($recentMeetings as $meeting)
                        <div class="timeline-item pb-6">
                            <div class="timeline-line"></div>
                            <div class="relative flex space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                        <i class="fas fa-calendar text-white text-sm"></i>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $meeting->title }}</p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                <i class="far fa-calendar-alt mr-1"></i>
                                                {{ \Carbon\Carbon::parse($meeting->meeting_date)->format('M d, Y') }}
                                                <i class="far fa-clock ml-3 mr-1"></i>
                                                {{ \Carbon\Carbon::parse($meeting->start_time)->format('h:i A') }}
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium status-{{ $meeting->status }}">
                                            {{ ucfirst($meeting->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">No recent meetings found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Recent Attendance -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Attendance</h2>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        @forelse($recentAttendance as $attendance)
                        <div class="timeline-item pb-6">
                            <div class="timeline-line"></div>
                            <div class="relative flex space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-4 ring-white shadow-lg">
                                        <i class="fas fa-user-check text-white text-sm"></i>
                                    </span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900">{{ $attendance->employee_name }}</p>
                                            <p class="text-sm text-gray-500 mt-1">
                                                <i class="fas fa-building mr-1"></i>
                                                {{ $attendance->department }}
                                                @if($attendance->check_in_time)
                                                <i class="far fa-clock ml-3 mr-1"></i>
                                                {{ \Carbon\Carbon::parse($attendance->check_in_time)->format('h:i A') }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium status-{{ $attendance->status }}">
                                            {{ str_replace('_', ' ', ucfirst($attendance->status)) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <i class="fas fa-user-times text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500">No recent attendance found</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Meeting Status Chart
    const meetingStatusCtx = document.getElementById('meetingStatusChart');
    if (meetingStatusCtx) {
        const meetingStatusChart = new Chart(meetingStatusCtx.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Approved', 'Ongoing', 'Completed', 'Rejected'],
                datasets: [{
                    data: [
                        {{ $meetingStatusCounts['pending'] ?? 0 }},
                        {{ $meetingStatusCounts['approved'] ?? 0 }},
                        {{ $meetingStatusCounts['ongoing'] ?? 0 }},
                        {{ $meetingStatusCounts['completed'] ?? 0 }},
                        {{ $meetingStatusCounts['rejected'] ?? 0 }}
                    ],
                    backgroundColor: [
                        '#FEF3C7', // yellow for pending
                        '#DBEAFE', // blue for approved
                        '#EDE9FE', // indigo for ongoing
                        '#D1FAE5', // green for completed
                        '#FEE2E2'  // red for rejected
                    ],
                    borderColor: [
                        '#F59E0B',
                        '#3B82F6',
                        '#6366F1',
                        '#10B981',
                        '#EF4444'
                    ],
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                family: 'Montserrat',
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            family: 'Montserrat'
                        },
                        bodyFont: {
                            family: 'Montserrat'
                        }
                    }
                },
                cutout: '60%',
                elements: {
                    arc: {
                        borderJoinStyle: 'round'
                    }
                }
            }
        });
    }

    // Attendance Trend Chart
    const attendanceTrendCtx = document.getElementById('attendanceTrendChart');
    if (attendanceTrendCtx) {
        const attendanceTrendChart = new Chart(attendanceTrendCtx.getContext('2d'), {
            type: 'line',
            data: {
                labels: {!! json_encode($attendanceTrends['dates'] ?? []) !!},
                datasets: [
                    {
                        label: 'On Time',
                        data: {!! json_encode($attendanceTrends['on_time'] ?? []) !!},
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#10B981',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Late',
                        data: {!! json_encode($attendanceTrends['late'] ?? []) !!},
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#EF4444',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    },
                    {
                        label: 'Early',
                        data: {!! json_encode($attendanceTrends['early'] ?? []) !!},
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointBackgroundColor: '#3B82F6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                family: 'Montserrat',
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: {
                            family: 'Montserrat'
                        },
                        bodyFont: {
                            family: 'Montserrat'
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            font: {
                                family: 'Montserrat'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                family: 'Montserrat'
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    }
                },
                elements: {
                    line: {
                        borderJoinStyle: 'round'
                    }
                }
            }
        });
    }
});
</script>
@endpush