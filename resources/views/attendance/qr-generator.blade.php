@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">QR Code Generator</h1>
                    <p class="text-gray-600 mt-2">Generate QR code for attendance tracking</p>
                </div>
                <a href="{{ route('attendance.index') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-semibold transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Date Selector -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Select Date</h2>

                <form method="GET" action="{{ route('attendance.generate-qr') }}" class="space-y-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-2">Attendance Date</label>
                        <input type="date"
                               name="date"
                               id="date"
                               value="{{ $date }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <button type="submit"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold transition-colors">
                        Generate QR Code
                    </button>
                </form>

                <!-- QR Code Info -->
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-medium text-blue-900 mb-2">QR Code Information</h3>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</p>
                        <p><strong>URL:</strong> <span class="font-mono text-xs break-all">{{ $attendanceUrl }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Generated QR Code -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Generated QR Code</h2>

                <div class="text-center">
                    <!-- Method 1: Using QR.js Library (Recommended) -->
                    <div class="inline-block p-6 bg-white border-2 border-gray-200 rounded-lg">
                        <div id="qr-code-container">
                            <!-- QR Code will be generated here -->
                        </div>
                    </div>

                    <!-- Fallback: Google Charts API QR Code -->
                    <div class="mt-4" id="fallback-qr" style="display: none;">
                        <img src="{{ $qrCodeUrl ?? '' }}" alt="QR Code" class="mx-auto border rounded-lg">
                        <p class="text-xs text-gray-500 mt-2">Fallback QR Code (requires internet)</p>
                    </div>

                    <div class="mt-6 space-y-3">
                        <p class="text-gray-600">Scan this QR code to mark attendance for</p>
                        <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</p>

                        <!-- URL for Manual Access -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm text-gray-600 mb-2">Or visit directly:</p>
                            <a href="{{ $attendanceUrl }}"
                               class="text-blue-600 hover:text-blue-800 font-mono text-xs break-all underline"
                               target="_blank">{{ $attendanceUrl }}</a>
                        </div>

                        <!-- Download/Print Options -->
                        <div class="flex justify-center space-x-3 pt-4">
                            <button onclick="printQRCode()"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print QR Code
                            </button>

                            <button onclick="shareQRCode()"
                                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                </svg>
                                Share Link
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-2">Instructions</h3>
                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                        <li>Print and display this QR code at your entrance/office</li>
                        <li>Employees can scan with any QR code scanner app</li>
                        <li>Late arrivals (after 8:15 AM) are automatically marked as "Late"</li>
                        <li>Early arrivals (before 7:30 AM) are marked as "Early"</li>
                        <li>Each employee can only mark attendance once per day</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Print Styles -->
<style>
@media print {
    body * { visibility: hidden; }
    .qr-print, .qr-print * { visibility: visible; }
    .qr-print {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        text-align: center;
        padding: 50px;
    }
}
</style>

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>

<script>
// Generate QR Code using QR.js library
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Create QR code using QRious library
        const qr = new QRious({
            element: document.createElement('canvas'),
            value: '{{ $attendanceUrl }}',
            size: 300,
            background: 'white',
            foreground: 'black',
            level: 'M'
        });

        // Add the canvas to container
        const container = document.getElementById('qr-code-container');
        container.appendChild(qr.canvas);

    } catch (error) {
        console.error('QR Code generation failed:', error);
        // Show fallback QR code
        document.getElementById('fallback-qr').style.display = 'block';
        document.getElementById('qr-code-container').innerHTML = '<p class="text-red-500">QR generation failed. Using fallback below.</p>';
    }
});

function printQRCode() {
    const qrElement = document.getElementById('qr-code-container');
    const date = '{{ \Carbon\Carbon::parse($date)->format("F j, Y") }}';
    const url = '{{ $attendanceUrl }}';

    let qrContent = '';
    const canvas = qrElement.querySelector('canvas');
    if (canvas) {
        qrContent = `<img src="${canvas.toDataURL()}" style="max-width: 300px;">`;
    } else {
        qrContent = `<div style="width: 300px; height: 300px; border: 2px solid #ddd; display: flex; align-items: center; justify-content: center; flex-direction: column;">
            <p>QR Code</p>
            <small>${url}</small>
        </div>`;
    }

    const printContent = `
        <div class="qr-print">
            <h1 style="font-size: 24px; margin-bottom: 20px; color: #333;">Attendance QR Code</h1>
            <p style="font-size: 18px; margin-bottom: 30px; color: #666;">Date: ${date}</p>
            <div style="display: inline-block; padding: 20px; border: 2px solid #ddd;">
                ${qrContent}
            </div>
            <p style="margin-top: 30px; font-size: 14px; color: #888;">Scan this QR code to mark your attendance</p>
            <p style="margin-top: 10px; font-size: 12px; color: #aaa;">URL: ${url}</p>
        </div>
    `;

    const printWindow = window.open('', '', 'width=800,height=600');
    printWindow.document.write(`
        <html>
            <head><title>Attendance QR Code</title></head>
            <body>${printContent}</body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}

function shareQRCode() {
    const url = '{{ $attendanceUrl }}';
    const date = '{{ \Carbon\Carbon::parse($date)->format("F j, Y") }}';
    const text = `Attendance QR Code for ${date}`;

    if (navigator.share) {
        navigator.share({
            title: text,
            text: text,
            url: url
        });
    } else {
        // Fallback - copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Attendance URL copied to clipboard!');
        }).catch(() => {
            // Manual copy fallback
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Attendance URL copied to clipboard!');
        });
    }
}
</script>
@endsection
