@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Attendance Records</h1>
                    <p class="text-gray-600 mt-2">View and manage attendance data</p>
                </div>
                <a href="{{ route('attendance.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to QR Generator
                </a>
            </div>
        </div>

        <!-- Date Filter & Stats -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Date Filter -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter by Date</h3>
                <form method="GET" action="{{ route('attendance.records') }}" class="space-y-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                        <input type="date"
                               name="date"
                               id="date"
                               value="{{ $date }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                        Filter Records
                    </button>
                </form>
            </div>

            <!-- Quick Stats -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    Statistics for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                        <div class="text-sm text-gray-600">Total Present</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">{{ $stats['on_time'] }}</div>
                        <div class="text-sm text-gray-600">On Time</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-red-600">{{ $stats['late'] }}</div>
                        <div class="text-sm text-gray-600">Late</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600">{{ $stats['early'] }}</div>
                        <div class="text-sm text-gray-600">Early</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Records Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">
                    Attendance Records - {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                </h2>
                @if($attendances->count() > 0)
                    <div class="flex space-x-3">
                        <button onclick="exportToCsv()"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export CSV
                        </button>
                        <button onclick="printRecords()"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print
                        </button>
                    </div>
                @endif
            </div>

            <div class="overflow-x-auto">
                @if($attendances->count() > 0)
                    <table class="min-w-full divide-y divide-gray-200" id="attendance-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Check-in Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Difference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($attendances as $index => $attendance)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $attendance->employee_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $attendance->employee_id }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($attendance->expected_time)->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($attendance->status === 'on_time')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">On Time</span>
                                    @elseif($attendance->status === 'late')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Late</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Early</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $checkIn = \Carbon\Carbon::parse($attendance->check_in_time);
                                        $expected = \Carbon\Carbon::parse($attendance->expected_time);
                                        $diff = $checkIn->diff($expected);
                                        $diffText = $diff->format('%H:%I:%S');
                                        $isLate = $checkIn->greaterThan($expected);
                                        $prefix = $isLate ? '+' : '-';
                                    @endphp
                                    <div class="text-sm {{ $isLate ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $prefix }}{{ $diffText }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-500 max-w-xs truncate" title="{{ $attendance->notes }}">
                                        {{ $attendance->notes ?: '-' }}
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">No attendance records found</p>
                        <p class="text-gray-400 text-sm mt-2">for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</p>
                        <div class="mt-6">
                            <a href="{{ route('attendance.generate-qr', ['date' => $date]) }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                Generate QR Code for This Date
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
function exportToCsv() {
    const table = document.getElementById('attendance-table');
    const rows = table.querySelectorAll('tr');
    let csv = [];

    // Add header row
    const headers = Array.from(rows[0].querySelectorAll('th')).map(th => th.textContent.trim());
    csv.push(headers.join(','));

    // Add data rows
    for (let i = 1; i < rows.length; i++) {
        const cols = Array.from(rows[i].querySelectorAll('td')).map(td => {
            // Clean up the text content
            let text = td.textContent.trim();
            // Remove extra whitespace and line breaks
            text = text.replace(/\s+/g, ' ');
            // Escape quotes and wrap in quotes if necessary
            if (text.includes(',') || text.includes('"') || text.includes('\n')) {
                text = '"' + text.replace(/"/g, '""') + '"';
            }
            return text;
        });
        csv.push(cols.join(','));
    }

    // Download CSV
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `attendance_{{ $date }}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function printRecords() {
    const date = '{{ \Carbon\Carbon::parse($date)->format("F j, Y") }}';
    const stats = {
        total: {{ $stats['total'] }},
        on_time: {{ $stats['on_time'] }},
        late: {{ $stats['late'] }},
        early: {{ $stats['early'] }}
    };

    const table = document.getElementById('attendance-table').outerHTML;

    const printContent = `
        <html>
            <head>
                <title>Attendance Records - ${date}</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    h1 { color: #333; text-align: center; margin-bottom: 10px; }
                    .date { text-align: center; color: #666; margin-bottom: 20px; }
                    .stats { display: flex; justify-content: center; margin-bottom: 30px; }
                    .stat-item { margin: 0 15px; text-align: center; }
                    .stat-number { font-size: 24px; font-weight: bold; }
                    .stat-label { font-size: 12px; color: #666; }
                    table { width: 100%; border-collapse: collapse; font-size: 12px; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f5f5f5; font-weight: bold; }
                    tr:nth-child(even) { background-color: #f9f9f9; }
                    .status-on-time { color: #22c55e; }
                    .status-late { color: #ef4444; }
                    .status-early { color: #f59e0b; }
                    @media print {
                        body { margin: 0; }
                        .no-print { display: none; }
                    }
                </style>
            </head>
            <body>
                <h1>Attendance Records</h1>
                <div class="date">${date}</div>
                <div class="stats">
                    <div class="stat-item">
                        <div class="stat-number">${stats.total}</div>
                        <div class="stat-label">Total Present</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number status-on-time">${stats.on_time}</div>
                        <div class="stat-label">On Time</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number status-late">${stats.late}</div>
                        <div class="stat-label">Late</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number status-early">${stats.early}</div>
                        <div class="stat-label">Early</div>
                    </div>
                </div>
                ${table}
            </body>
        </html>
    `;

    const printWindow = window.open('', '', 'width=1200,height=800');
    printWindow.document.write(printContent);
    printWindow.document.close();
    printWindow.print();
}

// Auto-refresh current time if viewing today's records
@if($date === \Carbon\Carbon::today()->format('Y-m-d'))
    setInterval(() => {
        // Optionally auto-refresh the page every 30 seconds for live updates
        // window.location.reload();
    }, 30000);
@endif
</script>
@endsection
