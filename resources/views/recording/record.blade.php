@extends('layouts.app')

@section('title', 'Meeting Time Management & Recording')

@push('styles')
<style>
    .agenda-item.active {
        border-left: 4px solid #3b82f6;
        background-color: #eff6ff;
    }
    .agenda-item.completed {
        border-left: 4px solid #10b981;
        background-color: #ecfdf5;
    }
    .agenda-item.skipped {
        border-left: 4px solid #f59e0b;
        background-color: #fefbeb;
    }
    .time-display {
        font-family: 'Courier New', monospace;
        font-weight: bold;
    }
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .pulsing {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: .5;
        }
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Meeting Time Management</h1>
                <p class="mt-2 text-gray-600">Track and manage meeting agenda items in real-time</p>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-calendar mr-1"></i>
                        <span id="current-date">{{ now()->format('F j, Y') }}</span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <i class="fas fa-clock mr-1"></i>
                        <span id="current-time">{{ now()->format('g:i A') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Meeting Search & Selection -->
    <div class="bg-white rounded-lg shadow-sm border mb-6">
        <div class="p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Select Meeting</h2>
            
            <!-- Search Form -->
            <form id="meeting-search-form" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search by Title</label>
                    <input type="text" id="search-title" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           placeholder="Enter meeting title...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Date</label>
                    <input type="date" id="search-date" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range From</label>
                    <input type="date" id="search-date-from" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date Range To</label>
                    <input type="date" id="search-date-to" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </form>

            <div class="flex flex-wrap gap-2 mb-4">
                <button type="button" id="search-meetings" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Search Meetings
                </button>
                <button type="button" id="clear-search" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>Clear
                </button>
                <button type="button" id="load-today" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                    <i class="fas fa-calendar-day mr-2"></i>Today's Meetings
                </button>
            </div>

            <!-- Meeting Selection Dropdown -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Available Meetings</label>
                <select id="meeting-select" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Choose a meeting...</option>
                    @foreach($todaysMeetings as $meeting)
                        <option value="{{ $meeting->id }}" data-meeting='@json($meeting)'>
                            {{ $meeting->title }} - {{ $meeting->meeting_date->format('M j, Y') }}
                            @if($meeting->start_time) at {{ $meeting->start_time->format('g:i A') }} @endif
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Meeting Details & Controls -->
    <div id="meeting-details" class="hidden">
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
                    <div>
                        <h3 id="meeting-title" class="text-xl font-semibold text-gray-900"></h3>
                        <div class="flex items-center space-x-4 mt-2 text-sm text-gray-600">
                            <span id="meeting-date"></span>
                            <span id="meeting-time"></span>
                            <span id="meeting-district"></span>
                        </div>
                    </div>
                    <div class="mt-4 lg:mt-0">
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Meeting Status</div>
                                <div id="meeting-status" class="status-badge bg-gray-100 text-gray-800"></div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Meeting Duration</div>
                                <div id="total-duration" class="time-display text-lg text-gray-900">00:00:00</div>
                                <div id="meeting-minutes" class="text-sm text-gray-600">0 minutes</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Meeting Controls -->
                <div class="flex flex-wrap gap-3">
                    <button id="start-meeting" 
                            class="bg-green-600 text-white px-6 py-2 rounded-md hover:bg-green-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>Start Meeting
                    </button>
                    <button id="pause-meeting" 
                            class="bg-yellow-600 text-white px-6 py-2 rounded-md hover:bg-yellow-700 transition-colors hidden">
                        <i class="fas fa-pause mr-2"></i>Pause Meeting
                    </button>
                    <button id="complete-meeting" 
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors hidden">
                        <i class="fas fa-check mr-2"></i>Complete Meeting
                    </button>
                    <button id="save-progress" 
                            class="bg-gray-600 text-white px-6 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Progress
                    </button>
                    <button id="generate-report" 
                            class="bg-purple-600 text-white px-6 py-2 rounded-md hover:bg-purple-700 transition-colors">
                        <i class="fas fa-file-alt mr-2"></i>Generate Report
                    </button>
                    <button id="reset-timer" 
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                        <i class="fas fa-redo mr-2"></i>Reset Timer
                    </button>
                    <button id="lap-timer" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors">
                        <i class="fas fa-flag mr-2"></i>Lap Time
                    </button>
                </div>
            </div>
        </div>

        <!-- Agenda Items -->
        <div class="bg-white rounded-lg shadow-sm border mb-6">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Agenda Items</h3>
                <div id="agenda-items" class="space-y-4">
                    <!-- Agenda items will be loaded here dynamically -->
                </div>
            </div>
        </div>

        <!-- Meeting Summary -->
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-gray-900 mb-6">Meeting Summary</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div id="pending-count" class="text-2xl font-bold text-blue-600">0</div>
                        <div class="text-sm text-gray-600">Pending</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div id="ongoing-count" class="text-2xl font-bold text-yellow-600">0</div>
                        <div class="text-sm text-gray-600">Ongoing</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div id="completed-count" class="text-2xl font-bold text-green-600">0</div>
                        <div class="text-sm text-gray-600">Completed</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div id="skipped-count" class="text-2xl font-bold text-orange-600">0</div>
                        <div class="text-sm text-gray-600">Skipped</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Meeting Report Modal -->
<div id="report-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full mx-4 max-h-screen overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Meeting Report</h3>
                <button id="close-report" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="report-content">
                <!-- Report content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
class MeetingTimeManager {
    constructor() {
        this.currentMeeting = null;
        this.meetingStartTime = null;
        this.totalDuration = 0;
        this.intervalId = null;
        this.agendaItems = [];
        this.activeItemId = null;
        this.activeItemStartTime = null;
        
        this.initEventListeners();
        this.updateCurrentTime();
        this.loadTodaysMeetings();
    }

    initEventListeners() {
        // Search and selection
        document.getElementById('search-meetings').addEventListener('click', () => this.searchMeetings());
        document.getElementById('clear-search').addEventListener('click', () => this.clearSearch());
        document.getElementById('load-today').addEventListener('click', () => this.loadTodaysMeetings());
        document.getElementById('meeting-select').addEventListener('change', (e) => this.selectMeeting(e.target.value));
        
        // Meeting controls
        document.getElementById('start-meeting').addEventListener('click', () => this.startMeeting());
        document.getElementById('pause-meeting').addEventListener('click', () => this.pauseMeeting());
        document.getElementById('complete-meeting').addEventListener('click', () => this.completeMeeting());
        document.getElementById('save-progress').addEventListener('click', () => this.saveProgress());
        document.getElementById('generate-report').addEventListener('click', () => this.generateReport());
        
        // Report modal
        document.getElementById('close-report').addEventListener('click', () => this.closeReportModal());
        
        // Update time every second
        setInterval(() => this.updateCurrentTime(), 1000);
        setInterval(() => this.updateDurations(), 1000);
    }

    updateCurrentTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour: 'numeric',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    document.getElementById('current-time').textContent = timeString;
    
    // Also update the date if it changed
    const dateString = now.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
    document.getElementById('current-date').textContent = dateString;
}

    async searchMeetings() {
    const title = document.getElementById('search-title').value;
    const date = document.getElementById('search-date').value;
    const dateFrom = document.getElementById('search-date-from').value;
    const dateTo = document.getElementById('search-date-to').value;

    const params = new URLSearchParams();
    if (title) params.append('title', title);
    if (date) params.append('date', date);
    if (dateFrom) params.append('date_from', dateFrom);
    if (dateTo) params.append('date_to', dateTo);

    try {
        const response = await fetch(`/search?${params}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        // Check if the response is ok (status 200-299)
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Server returned non-JSON response');
        }

        const meetings = await response.json();
        this.populateMeetingSelect(meetings);
        
        if (meetings.length === 0) {
            this.showNotification('No meetings found matching your criteria', 'info');
        } else {
            this.showNotification(`Found ${meetings.length} meeting(s)`, 'success');
        }
    } catch (error) {
        console.error('Error searching meetings:', error);
        
        if (error.message.includes('404')) {
            this.showNotification('Search endpoint not found. Please check your routes.', 'error');
        } else if (error.message.includes('HTTP error')) {
            this.showNotification(`Server error: ${error.message}`, 'error');
        } else {
            this.showNotification('Error searching meetings. Please try again.', 'error');
        }
    }
}

    clearSearch() {
        document.getElementById('search-title').value = '';
        document.getElementById('search-date').value = '';
        document.getElementById('search-date-from').value = '';
        document.getElementById('search-date-to').value = '';
        this.loadTodaysMeetings();
    }

    loadTodaysMeetings() {
        // Load today's meetings (already loaded from server)
        const select = document.getElementById('meeting-select');
    const options = Array.from(select.querySelectorAll('option')).filter(option => option.value !== '');

        if (options.length === 0) {
            this.showNotification('No meetings found for today', 'info');
        }
    }

    populateMeetingSelect(meetings) {
        const select = document.getElementById('meeting-select');
        select.innerHTML = '<option value="">Choose a meeting...</option>';
        
        meetings.forEach(meeting => {
            const option = document.createElement('option');
            option.value = meeting.id;
            option.textContent = `${meeting.title} - ${new Date(meeting.meeting_date).toLocaleDateString()} ${meeting.start_time ? 'at ' + meeting.start_time : ''}`;
            option.dataset.meeting = JSON.stringify(meeting);
            select.appendChild(option);
        });
    }

async selectMeeting(meetingId) {
    if (!meetingId) {
        document.getElementById('meeting-details').classList.add('hidden');
        return;
    }

    try {
        const response = await fetch(`/meeting/${meetingId}`, {  // Changed from /recording/meeting/
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Server returned non-JSON response');
        }

        const data = await response.json();
        
        if (data.error) {
            throw new Error(data.message || 'Unknown server error');
        }
        
        this.currentMeeting = data.meeting;
        this.agendaItems = data.agenda_items;
        
        this.displayMeetingDetails();
        this.displayAgendaItems();
        this.updateSummary();
        
        document.getElementById('meeting-details').classList.remove('hidden');
        this.showNotification('Meeting loaded successfully', 'success');
        
    } catch (error) {
        console.error('Error loading meeting details:', error);
        this.showNotification('Error loading meeting details. Please try again.', 'error');
        document.getElementById('meeting-details').classList.add('hidden');
    }
}

    displayMeetingDetails() {
        document.getElementById('meeting-title').textContent = this.currentMeeting.title;
        document.getElementById('meeting-date').innerHTML = `<i class="fas fa-calendar mr-1"></i>${new Date(this.currentMeeting.meeting_date).toLocaleDateString()}`;
        document.getElementById('meeting-time').innerHTML = `<i class="fas fa-clock mr-1"></i>${this.currentMeeting.start_time || 'Not set'}`;
        document.getElementById('meeting-district').innerHTML = `<i class="fas fa-map-marker-alt mr-1"></i>${this.currentMeeting.district_selection || 'Not specified'}`;
        
        this.updateMeetingStatus();
    }

    updateMeetingStatus() {
        const statusElement = document.getElementById('meeting-status');
        const status = this.currentMeeting.status;
        
        statusElement.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        statusElement.className = `status-badge bg-${this.getStatusColor(status)}-100 text-${this.getStatusColor(status)}-800`;
        
        // Update control buttons visibility
        const startBtn = document.getElementById('start-meeting');
        const pauseBtn = document.getElementById('pause-meeting');
        const completeBtn = document.getElementById('complete-meeting');
        
        startBtn.classList.toggle('hidden', status === 'ongoing' || status === 'completed');
        pauseBtn.classList.toggle('hidden', status !== 'ongoing');
        completeBtn.classList.toggle('hidden', status === 'completed');
    }

    getStatusColor(status) {
        const colors = {
            'pending': 'gray',
            'approved': 'blue',
            'ongoing': 'yellow',
            'completed': 'green',
            'rejected': 'red'
        };
        return colors[status] || 'gray';
    }

    displayAgendaItems() {
        const container = document.getElementById('agenda-items');
        container.innerHTML = '';

        this.agendaItems.forEach((item, index) => {
            const itemElement = this.createAgendaItemElement(item, index + 1);
            container.appendChild(itemElement);
        });
    }

    createAgendaItemElement(item, order) {
        const div = document.createElement('div');
        div.className = `agenda-item border rounded-lg p-4 ${this.getItemStatusClass(item.status)}`;
        div.dataset.itemId = item.id;

        div.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center">
                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm font-medium mr-3">${order}</span>
                    <h4 class="font-medium text-gray-900">${item.title}</h4>
                    <span class="status-badge ml-3 bg-${this.getStatusColor(item.status)}-100 text-${this.getStatusColor(item.status)}-800">${item.status}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 time-display item-duration" data-duration="${item.duration || 0}">00:00:00</span>
                    <button class="start-item bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 ${item.status !== 'pending' ? 'hidden' : ''}">
                        <i class="fas fa-play"></i> Start
                    </button>
                    <button class="end-item bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 ${item.status !== 'ongoing' ? 'hidden' : ''}">
                        <i class="fas fa-stop"></i> End
                    </button>
                    <button class="skip-item bg-yellow-600 text-white px-3 py-1 rounded text-sm hover:bg-yellow-700 ${item.status === 'completed' || item.status === 'skipped' ? 'hidden' : ''}">
                        <i class="fas fa-forward"></i> Skip
                    </button>
                </div>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                    <textarea class="item-remarks w-full px-3 py-2 border border-gray-300 rounded-md text-sm" rows="3" placeholder="Add remarks for this agenda item...">${item.remarks || ''}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select class="item-status w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="pending" ${item.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="ongoing" ${item.status === 'ongoing' ? 'selected' : ''}>Ongoing</option>
                        <option value="completed" ${item.status === 'completed' ? 'selected' : ''}>Completed</option>
                        <option value="skipped" ${item.status === 'skipped' ? 'selected' : ''}>Skipped</option>
                    </select>
                </div>
            </div>
        `;

        // Add event listeners for this item
        this.addItemEventListeners(div, item);

        return div;
    }

    addItemEventListeners(element, item) {
        const startBtn = element.querySelector('.start-item');
        const endBtn = element.querySelector('.end-item');
        const skipBtn = element.querySelector('.skip-item');
        const statusSelect = element.querySelector('.item-status');
        const remarksTextarea = element.querySelector('.item-remarks');

        startBtn?.addEventListener('click', () => this.startAgendaItem(item.id));
        endBtn?.addEventListener('click', () => this.endAgendaItem(item.id));
        skipBtn?.addEventListener('click', () => this.skipAgendaItem(item.id));
        statusSelect?.addEventListener('change', (e) => this.updateItemStatus(item.id, e.target.value));
        remarksTextarea?.addEventListener('blur', (e) => this.updateItemRemarks(item.id, e.target.value));
    }

    getItemStatusClass(status) {
        const classes = {
            'pending': '',
            'ongoing': 'active',
            'completed': 'completed',
            'skipped': 'skipped'
        };
        return classes[status] || '';
    }

    startAgendaItem(itemId) {
        // End any currently active item
        if (this.activeItemId && this.activeItemId !== itemId) {
            this.endAgendaItem(this.activeItemId);
        }

        const item = this.agendaItems.find(i => i.id === itemId);
        if (item) {
            item.status = 'ongoing';
            item.start_time = new Date();
            this.activeItemId = itemId;
            this.activeItemStartTime = new Date();
            
            this.updateItemDisplay(itemId);
            this.updateSummary();
        }
    }

    endAgendaItem(itemId) {
        const item = this.agendaItems.find(i => i.id === itemId);
        if (item && item.status === 'ongoing') {
            item.status = 'completed';
            item.end_time = new Date();
            
            if (item.start_time) {
                item.duration = Math.floor((item.end_time - item.start_time) / 1000);
            }
            
            if (this.activeItemId === itemId) {
                this.activeItemId = null;
                this.activeItemStartTime = null;
            }
            
            this.updateItemDisplay(itemId);
            this.updateSummary();
        }
    }

    skipAgendaItem(itemId) {
        const item = this.agendaItems.find(i => i.id === itemId);
        if (item) {
            item.status = 'skipped';
            item.end_time = new Date();
            
            if (this.activeItemId === itemId) {
                this.activeItemId = null;
                this.activeItemStartTime = null;
            }
            
            this.updateItemDisplay(itemId);
            this.updateSummary();
        }
    }

    updateItemStatus(itemId, status) {
        const item = this.agendaItems.find(i => i.id === itemId);
        if (item) {
            const oldStatus = item.status;
            item.status = status;
            
            if (status === 'ongoing' && oldStatus !== 'ongoing') {
                this.startAgendaItem(itemId);
            } else if (status === 'completed' && oldStatus === 'ongoing') {
                this.endAgendaItem(itemId);
            } else if (status === 'skipped') {
                this.skipAgendaItem(itemId);
            } else {
                this.updateItemDisplay(itemId);
                this.updateSummary();
            }
        }
    }

    updateItemRemarks(itemId, remarks) {
        const item = this.agendaItems.find(i => i.id === itemId);
        if (item) {
            item.remarks = remarks;
        }
    }

    updateItemDisplay(itemId) {
        const element = document.querySelector(`[data-item-id="${itemId}"]`);
        if (element) {
            const item = this.agendaItems.find(i => i.id === itemId);
            
            // Update status badge
            const statusBadge = element.querySelector('.status-badge');
            statusBadge.textContent = item.status;
            statusBadge.className = `status-badge ml-3 bg-${this.getStatusColor(item.status)}-100 text-${this.getStatusColor(item.status)}-800`;
            
            // Update buttons visibility
            const startBtn = element.querySelector('.start-item');
            const endBtn = element.querySelector('.end-item');
            const skipBtn = element.querySelector('.skip-item');
            
            startBtn.classList.toggle('hidden', item.status !== 'pending');
            endBtn.classList.toggle('hidden', item.status !== 'ongoing');
            skipBtn.classList.toggle('hidden', item.status === 'completed' || item.status === 'skipped');
            
            // Update container class
            element.className = `agenda-item border rounded-lg p-4 ${this.getItemStatusClass(item.status)}`;
            
            // Update status select
            const statusSelect = element.querySelector('.item-status');
            statusSelect.value = item.status;
        }
    }

    updateDurations() {
    // Update total meeting duration
    if (this.meetingStartTime) {
        const now = new Date();
        const totalSeconds = Math.floor((now - this.meetingStartTime) / 1000);
        const totalMinutes = Math.floor(totalSeconds / 60);
        
        // Update the time display
        document.getElementById('total-duration').textContent = this.formatDuration(totalSeconds);
        
        // Update the minutes display
        const minutesDisplay = document.getElementById('meeting-minutes');
        if (minutesDisplay) {
            minutesDisplay.textContent = `${totalMinutes} minute${totalMinutes !== 1 ? 's' : ''}`;
        }
    }

    // Update individual item durations
    this.agendaItems.forEach(item => {
        const element = document.querySelector(`[data-item-id="${item.id}"] .item-duration`);
        if (element) {
            let duration = item.duration || 0;
            
            // If item is currently ongoing, calculate current duration
            if (item.status === 'ongoing' && item.start_time) {
                const now = new Date();
                duration = Math.floor((now - item.start_time) / 1000);
            }
            
            element.textContent = this.formatDuration(duration);
            element.dataset.duration = duration;
            
            // Add minutes display for each item if needed
            const minutesElement = document.querySelector(`[data-item-id="${item.id}"] .item-minutes`);
            if (!minutesElement && duration > 0) {
                const minutes = Math.floor(duration / 60);
                const minutesSpan = document.createElement('span');
                minutesSpan.className = 'item-minutes text-xs text-gray-500 ml-2';
                minutesSpan.textContent = `(${minutes} min${minutes !== 1 ? 's' : ''})`;
                element.parentNode.appendChild(minutesSpan);
            }
        }
    });
}

    formatDuration(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    }

    updateSummary() {
        const summary = {
            pending: this.agendaItems.filter(item => item.status === 'pending').length,
            ongoing: this.agendaItems.filter(item => item.status === 'ongoing').length,
            completed: this.agendaItems.filter(item => item.status === 'completed').length,
            skipped: this.agendaItems.filter(item => item.status === 'skipped').length
        };

        document.getElementById('pending-count').textContent = summary.pending;
        document.getElementById('ongoing-count').textContent = summary.ongoing;
        document.getElementById('completed-count').textContent = summary.completed;
        document.getElementById('skipped-count').textContent = summary.skipped;
    }

        async startMeeting() {
        if (!this.currentMeeting) return;

        try {
            const response = await fetch(`/meeting/${this.currentMeeting.id}/start`, {  // Changed from /recording/meeting/
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();
            
            if (data.success) {
                this.currentMeeting.status = 'ongoing';
                this.meetingStartTime = new Date(data.start_time);
                this.updateMeetingStatus();
                this.showNotification('Meeting started successfully', 'success');
            }
        } catch (error) {
            console.error('Error starting meeting:', error);
            this.showNotification('Error starting meeting', 'error');
        }
    }

    pauseMeeting() {
        // Pause all ongoing agenda items
        this.agendaItems.forEach(item => {
            if (item.status === 'ongoing') {
                this.endAgendaItem(item.id);
            }
        });
        
        this.showNotification('Meeting paused', 'info');
    }

   async completeMeeting() {
    if (!this.currentMeeting) return;

    // End any ongoing items
    this.agendaItems.forEach(item => {
        if (item.status === 'ongoing') {
            this.endAgendaItem(item.id);
        }
    });

    try {
        const meetingMinutes = {
            agenda_items: this.agendaItems,
            total_duration: this.getTotalDuration(),
            completed_at: new Date().toISOString()
        };

        const response = await fetch(`/meeting/${this.currentMeeting.id}/complete`, {  // Changed from /recording/meeting/
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ meeting_minutes: meetingMinutes })
        });

        const data = await response.json();
        
        if (data.success) {
            this.currentMeeting.status = 'completed';
            this.updateMeetingStatus();
            this.showNotification('Meeting completed successfully', 'success');
        }
    } catch (error) {
        console.error('Error completing meeting:', error);
        this.showNotification('Error completing meeting', 'error');
    }
}


   async saveProgress() {
    if (!this.currentMeeting) return;

    try {
        const progress = {
            agenda_items: this.agendaItems,
            total_duration: this.getTotalDuration(),
            saved_at: new Date().toISOString()
        };

        const response = await fetch(`/meeting/${this.currentMeeting.id}/save-progress`, {  // Changed from /recording/meeting/
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ progress })
        });

        const data = await response.json();
        
        if (data.success) {
            this.showNotification('Progress saved successfully', 'success');
        }
    } catch (error) {
        console.error('Error saving progress:', error);
        this.showNotification('Error saving progress', 'error');
    }
}
    async generateReport() {
    if (!this.currentMeeting) return;

    try {
        const response = await fetch(`/meeting/${this.currentMeeting.id}/report`);  // Changed from /recording/meeting/
        const report = await response.json();
        
        this.displayReport(report);
    } catch (error) {
        console.error('Error generating report:', error);
        this.showNotification('Error generating report', 'error');
    }
}

    displayReport(report) {
    const content = document.getElementById('report-content');
    
    content.innerHTML = `
        <div class="space-y-6">
            <div class="border-b pb-4">
                <h4 class="text-xl font-semibold">${report.meeting.title}</h4>
                <div class="text-gray-600 mt-2">
                    <p><strong>Date:</strong> ${new Date(report.meeting.meeting_date).toLocaleDateString()}</p>
                    <p><strong>Duration:</strong> ${this.formatDuration(report.total_duration * 60)}</p>
                    <p><strong>Status:</strong> ${report.meeting.status}</p>
                    <p><strong>Generated:</strong> ${report.generated_at}</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded">
                    <div class="text-lg font-bold text-blue-600">${report.agenda_summary.total}</div>
                    <div class="text-sm text-gray-600">Total Items</div>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded">
                    <div class="text-lg font-bold text-gray-600">${report.agenda_summary.pending}</div>
                    <div class="text-sm text-gray-600">Pending</div>
                </div>
                <div class="text-center p-3 bg-yellow-50 rounded">
                    <div class="text-lg font-bold text-yellow-600">${report.agenda_summary.ongoing}</div>
                    <div class="text-sm text-gray-600">Ongoing</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded">
                    <div class="text-lg font-bold text-green-600">${report.agenda_summary.completed}</div>
                    <div class="text-sm text-gray-600">Completed</div>
                </div>
                <div class="text-center p-3 bg-orange-50 rounded">
                    <div class="text-lg font-bold text-orange-600">${report.agenda_summary.skipped}</div>
                    <div class="text-sm text-gray-600">Skipped</div>
                </div>
            </div>
            
            <div>
                <h5 class="font-semibold mb-3 text-lg">Agenda Items Details</h5>
                <div class="space-y-4">
                    ${report.agenda_items.map((item, index) => `
                        <div class="border rounded-lg p-4 bg-white shadow-sm">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm font-medium mr-2">${index + 1}</span>
                                    <span class="font-medium text-gray-900">${item.title}</span>
                                </div>
                                <span class="status-badge bg-${this.getStatusColor(item.status)}-100 text-${this.getStatusColor(item.status)}-800">
                                    ${item.status}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <strong>Duration:</strong> ${this.formatDuration(item.duration || 0)}
                                </div>
                                <div>
                                    <strong>Type:</strong> ${item.type || 'N/A'}
                                </div>
                            </div>
                            
                            ${item.remarks ? `
                                <div class="mt-3 p-3 bg-gray-50 rounded">
                                    <strong class="block text-sm font-medium text-gray-700 mb-1">Remarks:</strong>
                                    <p class="text-gray-700 text-sm">${item.remarks}</p>
                                </div>
                            ` : `
                                <div class="mt-3 text-sm text-gray-500">
                                    <i>No remarks added</i>
                                </div>
                            `}
                        </div>
                    `).join('')}
                </div>
            </div>
            
            ${report.meeting.closing_remarks ? `
                <div class="border-t pt-4">
                    <h5 class="font-semibold mb-3 text-lg">Closing Remarks</h5>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-gray-700">${report.meeting.closing_remarks}</p>
                    </div>
                </div>
            ` : ''}
            
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Report
                </button>
                <button onclick="this.exportReport()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>Export PDF
                </button>
            </div>
        </div>
    `;
    
    document.getElementById('report-modal').classList.remove('hidden');
}

    closeReportModal() {
        document.getElementById('report-modal').classList.add('hidden');
    }
    
    exportReport() {
    if (!this.currentMeeting) return;

    // Create a printable version of the report
    const printContent = document.getElementById('report-content').innerHTML;
    const originalContent = document.body.innerHTML;
    
    document.body.innerHTML = `
        <div class="container mx-auto p-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold">Meeting Report</h1>
                <h2 class="text-xl">${this.currentMeeting.title}</h2>
                <p>Generated on ${new Date().toLocaleDateString()}</p>
            </div>
            ${printContent}
        </div>
    `;
    
    window.print();
    
    // Restore original content
    document.body.innerHTML = originalContent;
    
    // Re-initialize event listeners
    this.initEventListeners();
    
    this.showNotification('Report ready for printing', 'success');
}

    getTotalDuration() {
        if (this.meetingStartTime) {
            const now = new Date();
            return Math.floor((now - this.meetingStartTime) / 1000);
        }
        return 0;
    }

    showNotification(message, type = 'info') {
        // Create a simple notification system
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${this.getNotificationClass(type)}`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${this.getNotificationIcon(type)} mr-2"></i>
                <span>${message}</span>
                <button class="ml-4 text-lg">&times;</button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
        
        // Add click to close
        notification.querySelector('button').addEventListener('click', () => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        });
    }

    getNotificationClass(type) {
        const classes = {
            'success': 'bg-green-100 text-green-800 border border-green-200',
            'error': 'bg-red-100 text-red-800 border border-red-200',
            'warning': 'bg-yellow-100 text-yellow-800 border border-yellow-200',
            'info': 'bg-blue-100 text-blue-800 border border-blue-200'
        };
        return classes[type] || classes.info;
    }

    getNotificationIcon(type) {
        const icons = {
            'success': 'check-circle',
            'error': 'exclamation-circle',
            'warning': 'exclamation-triangle',
            'info': 'info-circle'
        };
        return icons[type] || icons.info;
    }
}

// Initialize the meeting time manager when the page loads
document.addEventListener('DOMContentLoaded', () => {
    new MeetingTimeManager();
});
</script>
@endpush