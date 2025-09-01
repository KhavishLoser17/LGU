@extends('layouts.app')

@section('title', 'Status and Publishing')
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush
@section('content')

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Meeting Publications</h1>
        <p class="text-gray-600">Manage and publish approved meetings to the public landing page</p>
    </div>

    <!-- Approved Agendas -->
    <div id="approved-content" class="tab-content">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Approved Agendas - Ready for Landing Page</h2>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">{{ $approvedMeetings->count() }} approved meetings</span>
                        <a href="{{ route('landing.page') }}" target="_blank" 
                           class="inline-flex items-center px-3 py-2 text-sm bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-external-link-alt mr-2"></i>
                            View Landing Page
                        </a>
                    </div>
                </div>
            </div>

            @if($approvedMeetings->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                    @foreach($approvedMeetings as $meeting)
                        <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                            <!-- Meeting Image -->
                            <div class="relative mb-3">
                                @if($meeting['image_url'])
                                    <img class="w-full h-32 object-cover rounded" src="{{ $meeting['image_url'] }}" alt="{{ $meeting['title'] }}">
                                @else
                                    <div class="w-full h-32 bg-gray-100 rounded flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                                
                                <!-- Published Badge -->
                                @if($meeting['is_published'])
                                    <div class="absolute top-2 right-2">
                                        <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-globe mr-1"></i>
                                            Published
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Meeting Info -->
                            <h3 class="font-semibold text-gray-900 mb-2">{{ $meeting['title'] }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ $meeting['description'] }}</p>
                            
                            <!-- Meeting Details -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt w-4 mr-2"></i>
                                    <span>{{ $meeting['district'] }}</span>
                                </div>
                                @if($meeting['meeting_date'])
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-calendar w-4 mr-2"></i>
                                        <span>{{ $meeting['meeting_date'] }}</span>
                                    </div>
                                @endif
                                @if($meeting['meeting_time'])
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-clock w-4 mr-2"></i>
                                        <span>{{ $meeting['meeting_time'] }}</span>
                                    </div>
                                @endif
                                @if($meeting['agenda_leader'])
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user w-4 mr-2"></i>
                                        <span>{{ $meeting['agenda_leader'] }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Status and Actions -->
                            <div class="flex items-center justify-between pt-4 border-t">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                                <div class="flex space-x-2">
                                    <button onclick="printMeeting({{ $meeting['id'] }})" 
                                            class="flex items-center px-3 py-1 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded transition-colors"
                                            title="Print Meeting">
                                        <i class="fas fa-print mr-1"></i>
                                        Print
                                    </button>
                                    
                                    @if($meeting['is_published'])
                                        <button onclick="unpublishMeeting({{ $meeting['id'] }})" 
                                                class="flex items-center px-3 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded transition-colors"
                                                title="Unpublish from Landing Page">
                                            <i class="fas fa-eye-slash mr-1"></i>
                                            Unpublish
                                        </button>
                                    @else
                                        <button onclick="showPublishModal({{ $meeting['id'] }}, '{{ addslashes($meeting['title']) }}')" 
                                                class="flex items-center px-3 py-1 text-sm text-green-600 hover:text-green-800 hover:bg-green-50 rounded transition-colors"
                                                title="Publish to Landing Page">
                                            <i class="fas fa-globe mr-1"></i>
                                            Publish
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No approved meetings</h3>
                    <p class="text-gray-500 mb-4">There are no approved meetings ready for publishing yet.</p>
                    <a href="{{ route('meetings.manage') }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Manage Meetings
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Publish Confirmation Modal -->
<div id="publishModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex items-center mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-globe text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">Publish Meeting</h3>
            </div>
            <p class="text-gray-600 mb-6">
                Are you sure you want to publish "<span id="meetingTitleModal" class="font-medium"></span>" to the public landing page?
            </p>
            <div class="flex items-center justify-end space-x-3">
                <button onclick="closePublishModal()" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                    Cancel
                </button>
                <button id="confirmPublishBtn" 
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-globe mr-2"></i>
                    Publish Now
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentMeetingId = null;

// Ensure CSRF token is available
const csrfToken = document.querySelector('meta[name="csrf-token"]');
if (!csrfToken) {
    console.error('CSRF token not found');
}

function showPublishModal(meetingId, meetingTitle) {
    currentMeetingId = meetingId;
    document.getElementById('meetingTitleModal').textContent = meetingTitle;
    document.getElementById('publishModal').classList.remove('hidden');
    document.getElementById('publishModal').classList.add('flex');
}

function closePublishModal() {
    document.getElementById('publishModal').classList.add('hidden');
    document.getElementById('publishModal').classList.remove('flex');
    currentMeetingId = null;
}

function publishMeeting(meetingId) {
    const btn = document.getElementById('confirmPublishBtn');
    const originalText = btn.innerHTML;
    
    // Show loading state
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Publishing...';
    btn.disabled = true;
    
    // Make AJAX request with proper headers
    fetch(`{{ url('/') }}/${meetingId}/publish`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '{{ csrf_token() }}'
        },
        body: JSON.stringify({})
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closePublishModal();
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showNotification(data.message || 'Failed to publish meeting', 'error');
        }
    })
    .catch(error => {
        console.error('Publish Error:', error);
        showNotification('Error publishing meeting. Please check the console for details.', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

function unpublishMeeting(meetingId) {
    if (confirm('Are you sure you want to unpublish this meeting from the landing page?')) {
        fetch(`{{ url('/') }}/${meetingId}/unpublish`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.content : '{{ csrf_token() }}'
            },
            body: JSON.stringify({})
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showNotification(data.message || 'Failed to unpublish meeting', 'error');
            }
        })
        .catch(error => {
            console.error('Unpublish Error:', error);
            showNotification('Error unpublishing meeting. Please check the console for details.', 'error');
        });
    }
}

function printMeeting(meetingId) {
    window.open(`{{ url('/') }}/${meetingId}/print`, '_blank');
}

// Event listener for modal confirm button
document.addEventListener('DOMContentLoaded', function() {
    const confirmBtn = document.getElementById('confirmPublishBtn');
    if (confirmBtn) {
        confirmBtn.addEventListener('click', function() {
            if (currentMeetingId) {
                publishMeeting(currentMeetingId);
            }
        });
    }

    // Close modal when clicking outside
    const modal = document.getElementById('publishModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closePublishModal();
            }
        });
    }
});

// Notification function
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelectorAll('.notification-toast');
    existing.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 max-w-sm ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Remove notification after 4 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 4000);
}

// Debug function to check routes
function debugRoutes() {
    console.log('Available routes:');
    console.log('Publish: {{ url("/") }}/[ID]/publish');
    console.log('Unpublish: {{ url("/") }}/[ID]/unpublish');
    console.log('CSRF Token:', csrfToken ? csrfToken.content : 'Not found');
}

// Call debug on page load for troubleshooting
debugRoutes();
</script>
@endpush
@endsection