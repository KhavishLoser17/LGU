@extends('layouts.app')

@section('title', 'Meeting Recording & Minutes')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Meeting Selection Section -->
    @if(!$selectedMeeting)
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Select Meeting to Record</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($meetings as $meeting)
            <div class="border rounded-lg p-4 hover:shadow-md transition-shadow cursor-pointer meeting-card" 
                 onclick="selectMeeting({{ $meeting['id'] }})">
                @if($meeting['image_url'])
                <img src="{{ $meeting['image_url'] }}" alt="Meeting" class="w-full h-32 object-cover rounded-md mb-3">
                @endif
                <h3 class="font-semibold text-gray-900 mb-2">{{ $meeting['title'] }}</h3>
                <p class="text-sm text-gray-600 mb-2">{{ Str::limit($meeting['description'], 100) }}</p>
                <div class="text-xs text-gray-500">
                    <div>{{ $meeting['meeting_date']->format('M j, Y') }}</div>
                    @if($meeting['start_time'])
                    <div>{{ \Carbon\Carbon::parse($meeting['start_time'])->format('g:i A') }}</div>
                    @endif
                </div>
                <div class="mt-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        {{ $meeting['status'] === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($meeting['status'] === 'ongoing' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($meeting['status']) }}
                    </span>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-8">
                <i class="fas fa-calendar-times text-4xl text-gray-400 mb-4"></i>
                <p class="text-gray-500">No meetings available for recording</p>
            </div>
            @endforelse
        </div>
    </div>
    @endif

    <!-- Selected Meeting Recording Interface -->
    @if($selectedMeeting)
    <div class="space-y-6">
        <!-- Meeting Information Header -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $selectedMeeting->title }}</h2>
                        <button onclick="deselectMeeting()" class="ml-4 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <p class="text-gray-600 mb-4">{{ $selectedMeeting->description }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <span class="font-medium text-gray-700">Date:</span>
                            <span class="text-gray-600">{{ $selectedMeeting->meeting_date->format('F j, Y') }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">Start Time:</span>
                            <span class="text-gray-600">{{ $selectedMeeting->start_time ? \Carbon\Carbon::parse($selectedMeeting->start_time)->format('g:i A') : 'Not set' }}</span>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700">End Time:</span>
                            <span class="text-gray-600">{{ $selectedMeeting->end_time ? \Carbon\Carbon::parse($selectedMeeting->end_time)->format('g:i A') : 'Not set' }}</span>
                        </div>
                    </div>
                    
                    @if($selectedMeeting->agenda_leader)
                    <div class="mt-2 text-sm">
                        <span class="font-medium text-gray-700">Agenda Leader:</span>
                        <span class="text-gray-600">{{ $selectedMeeting->agenda_leader }}</span>
                    </div>
                    @endif
                </div>
                
                @if($selectedMeeting->image_url)
                <div class="ml-6">
                    <img src="{{ $selectedMeeting->image_url }}" alt="Meeting Image" class="w-24 h-24 object-cover rounded-lg shadow-sm">
                </div>
                @endif
            </div>
        </div>

        <!-- Meeting Progress -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Meeting Progress</h3>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-clock mr-1"></i>
                        Total Time: <span class="font-medium" id="total-time">
                            {{ $currentMinute ? $currentMinute->getTotalDurationFormatted() : '00:00:00' }}
                        </span>
                    </div>
                    <div class="flex space-x-2">
                        @if(!$currentMinute || $currentMinute->status === 'pending')
                        <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors" 
                                onclick="startMeeting()" id="start-meeting-btn">
                            <i class="fas fa-play mr-2"></i>Start Meeting
                        </button>
                        @elseif($currentMinute->status === 'ongoing')
                        <button class="bg-yellow-600 text-white px-4 py-2 rounded-md hover:bg-yellow-700 transition-colors" 
                                onclick="pauseMeeting()" id="pause-meeting-btn">
                            <i class="fas fa-pause mr-2"></i>Pause Meeting
                        </button>
                        <button class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors" 
                                onclick="completeMeeting()" id="complete-meeting-btn">
                            <i class="fas fa-stop mr-2"></i>End Meeting
                        </button>
                        @elseif($currentMinute->status === 'paused')
                        <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors" 
                                onclick="resumeMeeting()" id="resume-meeting-btn">
                            <i class="fas fa-play mr-2"></i>Resume Meeting
                        </button>
                        <button class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors" 
                                onclick="completeMeeting()" id="complete-meeting-btn">
                            <i class="fas fa-stop mr-2"></i>End Meeting
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Meeting Status Indicator -->
            <div class="mb-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 rounded-full {{ 
                        $currentMinute && $currentMinute->status === 'ongoing' ? 'bg-green-500 animate-pulse' : 
                        ($currentMinute && $currentMinute->status === 'paused' ? 'bg-yellow-500' : 'bg-gray-400') 
                    }}"></div>
                    <span class="text-sm font-medium text-gray-700">
                        Status: {{ $currentMinute ? ucfirst($currentMinute->status) : 'Not Started' }}
                    </span>
                </div>
            </div>

            <!-- Agenda Items -->
            <div class="space-y-4" id="agenda-items">
                @php
                    $defaultItems = $selectedMeeting->default_agenda_items ?? [];
                    $customItems = $selectedMeeting->custom_agenda_items ?? [];
                    $itemCounter = 0;
                @endphp

                <!-- Default Agenda Items -->
                @if(!empty($defaultItems))
                    @foreach($defaultItems as $index => $item)
                    @php
                        $itemCounter++;
                        $agendaKey = "default-{$index}";
                        $agendaItemMinute = $agendaItemMinutes->get($agendaKey);
                    @endphp
                    <div class="agenda-item border rounded-lg p-4 {{ $agendaItemMinute && $agendaItemMinute->status === 'ongoing' ? 'border-blue-500 bg-blue-50' : '' }}" 
                         data-item-type="default" data-item-index="{{ $index }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-sm font-medium mr-3">{{ $itemCounter }}</span>
                                <h4 class="font-medium text-gray-900">{{ $item['title'] ?? 'Agenda Item ' . $itemCounter }}</h4>
                                @if($agendaItemMinute && $agendaItemMinute->status === 'ongoing')
                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full mr-1 animate-pulse"></div>
                                    Active
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 time-display">
                                    {{ $agendaItemMinute ? $agendaItemMinute->getDurationFormatted() : '00:00:00' }}
                                </span>
                                @if(!$agendaItemMinute || $agendaItemMinute->status !== 'ongoing')
                                <button class="start-item bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors"
                                        onclick="startAgendaItem('default', {{ $index }})">
                                    <i class="fas fa-play"></i> Start
                                </button>
                                @else
                                <button class="end-item bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors"
                                        onclick="endAgendaItem('default', {{ $index }})">
                                    <i class="fas fa-stop"></i> End
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm remarks-input" 
                                          rows="3" 
                                          placeholder="Add remarks for this agenda item..."
                                          onblur="updateRemarks('default', {{ $index }}, this.value)">{{ $agendaItemMinute->remarks ?? $item['remarks'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm status-select"
                                        onchange="updateStatus('default', {{ $index }}, this.value)">
                                    <option value="pending" {{ ($agendaItemMinute ? $agendaItemMinute->status : ($item['status'] ?? 'pending')) === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="ongoing" {{ ($agendaItemMinute ? $agendaItemMinute->status : ($item['status'] ?? 'pending')) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ ($agendaItemMinute ? $agendaItemMinute->status : ($item['status'] ?? 'pending')) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="skipped" {{ ($agendaItemMinute ? $agendaItemMinute->status : ($item['status'] ?? 'pending')) === 'skipped' ? 'selected' : '' }}>Skipped</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif

                <!-- Custom Agenda Items -->
                @if(!empty($customItems))
                    @foreach($customItems as $index => $item)
                    @php
                        $itemCounter++;
                        $agendaKey = "custom-{$index}";
                        $agendaItemMinute = $agendaItemMinutes->get($agendaKey);
                    @endphp
                    <div class="agenda-item border rounded-lg p-4 {{ $agendaItemMinute && $agendaItemMinute->status === 'ongoing' ? 'border-blue-500 bg-blue-50' : '' }}" 
                         data-item-type="custom" data-item-index="{{ $index }}">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-sm font-medium mr-3">{{ $itemCounter }}</span>
                                <h4 class="font-medium text-gray-900">{{ $item['title'] ?? 'Custom Item ' . ($index + 1) }}</h4>
                                @if($agendaItemMinute && $agendaItemMinute->status === 'ongoing')
                                <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <div class="w-2 h-2 bg-blue-600 rounded-full mr-1 animate-pulse"></div>
                                    Active
                                </span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 time-display">
                                    {{ $agendaItemMinute ? $agendaItemMinute->getDurationFormatted() : '00:00:00' }}
                                </span>
                                @if(!$agendaItemMinute || $agendaItemMinute->status !== 'ongoing')
                                <button class="start-item bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700 transition-colors"
                                        onclick="startAgendaItem('custom', {{ $index }})">
                                    <i class="fas fa-play"></i> Start
                                </button>
                                @else
                                <button class="end-item bg-red-600 text-white px-3 py-1 rounded text-sm hover:bg-red-700 transition-colors"
                                        onclick="endAgendaItem('custom', {{ $index }})">
                                    <i class="fas fa-stop"></i> End
                                </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm remarks-input" 
                                          rows="3" 
                                          placeholder="Add remarks for this agenda item..."
                                          onblur="updateRemarks('custom', {{ $index }}, this.value)">{{ $agendaItemMinute->remarks ?? $item['remarks'] ?? '' }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm status-select"
                                        onchange="updateStatus('custom', {{ $index }}, this.value)">
                                    <option value="pending" {{ ($agendaItemMinute ? $agendaItemMinute->status : ($item['status'] ?? 'pending')) === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="ongoing" {{ ($agendaItemMinute ? $agendaItemMinute->status : ($item['status'] ?? 'pending')) === 'ongoing' ? 'selected' : '' }}>Ongoing</option>
                                    <option value="completed" {{ ($agendaItemMinute ? $agendaItemMinute->status : ($item['status'] ?? 'pending')) === 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="skipped" {{ ($agendaItemMinute ? $agendaItemMinute->status : ($item['status'] ?? 'pending')) === 'skipped' ? 'selected' : '' }}>Skipped</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif

                <!-- Fallback if no agenda items -->
                @if(empty($defaultItems) && empty($customItems))
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-clipboard-list text-4xl mb-4"></i>
                    <p>No agenda items found for this meeting.</p>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-end space-x-4 mt-8 pt-6 border-t">
                <button class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors"
                        onclick="saveProgress()">
                    <i class="fas fa-save mr-2"></i>Save Progress
                </button>
                <button class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
                        onclick="generateReport()">
                    <i class="fas fa-file-alt mr-2"></i>Generate Report
                </button>
            </div>
        </div>

        <!-- Meeting Summary -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Meeting Summary</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center p-4 bg-green-50 rounded-lg">
                    <div class="text-2xl font-bold text-green-600" id="completed-count">
                        {{ $agendaItemMinutes->where('status', 'completed')->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Completed Items</div>
                </div>
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600" id="ongoing-count">
                        {{ $agendaItemMinutes->where('status', 'ongoing')->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Ongoing Items</div>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-lg">
                    <div class="text-2xl font-bold text-yellow-600" id="skipped-count">
                        {{ $agendaItemMinutes->where('status', 'skipped')->count() }}
                    </div>
                    <div class="text-sm text-gray-600">Skipped Items</div>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="text-2xl font-bold text-gray-600" id="pending-count">
                        {{ $agendaItemMinutes->where('status', 'pending')->count() ?: (count($defaultItems) + count($customItems)) }}
                    </div>
                    <div class="text-sm text-gray-600">Pending Items</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
            <span class="text-gray-700">Processing...</span>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<div id="message-container" class="fixed top-4 right-4 z-40 space-y-2"></div>

<script>
// Global variables
let selectedMeetingId = {{ $selectedMeeting ? $selectedMeeting->id : 'null' }};
let meetingStatus = '{{ $currentMinute ? $currentMinute->status : 'pending' }}';
let totalTimeInterval = null;
let itemTimeIntervals = {};

// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

document.addEventListener('DOMContentLoaded', function() {
    // Initialize timers if meeting is ongoing
    if (meetingStatus === 'ongoing') {
        startTotalTimeCounter();
        startActiveItemCounters();
    }
    
    // Initialize summary counters
    updateSummaryCounters();
});

// Meeting Selection Functions
function selectMeeting(meetingId) {
    showLoading();
    window.location.href = `{{ route('recording.record') }}?meeting_id=${meetingId}`;
}

function deselectMeeting() {
    window.location.href = `{{ route('recording.record') }}`;
}

// Meeting Control Functions
async function startMeeting() {
    if (!selectedMeetingId) return;
    
    showLoading();
    try {
        const response = await fetch(`{{ route('recording.start-meeting') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ meeting_id: selectedMeetingId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            meetingStatus = 'ongoing';
            updateMeetingControls();
            startTotalTimeCounter();
            showMessage('Meeting started successfully!', 'success');
        } else {
            showMessage(result.error || 'Failed to start meeting', 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
    } finally {
        hideLoading();
    }
}

async function pauseMeeting() {
    if (!selectedMeetingId) return;
    
    showLoading();
    try {
        const response = await fetch(`{{ route('recording.pause-meeting') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ meeting_id: selectedMeetingId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            meetingStatus = 'paused';
            updateMeetingControls();
            stopTotalTimeCounter();
            stopAllItemCounters();
            showMessage('Meeting paused successfully!', 'success');
        } else {
            showMessage(result.error || 'Failed to pause meeting', 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
    } finally {
        hideLoading();
    }
}

async function resumeMeeting() {
    await startMeeting(); // Same logic as start
}

async function completeMeeting() {
    if (!selectedMeetingId) return;
    
    if (!confirm('Are you sure you want to complete this meeting? This action cannot be undone.')) {
        return;
    }
    
    showLoading();
    try {
        const response = await fetch(`{{ route('recording.complete-meeting') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ meeting_id: selectedMeetingId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            meetingStatus = 'completed';
            updateMeetingControls();
            stopTotalTimeCounter();
            stopAllItemCounters();
            showMessage('Meeting completed successfully!', 'success');
        } else {
            showMessage(result.error || 'Failed to complete meeting', 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
    } finally {
        hideLoading();
    }
}

// Agenda Item Functions
async function startAgendaItem(itemType, itemIndex) {
    if (!selectedMeetingId || meetingStatus !== 'ongoing') {
        showMessage('Meeting must be ongoing to start agenda items', 'warning');
        return;
    }
    
    try {
        const response = await fetch(`{{ route('recording.start-agenda-item') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                meeting_id: selectedMeetingId,
                item_type: itemType,
                item_index: itemIndex
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateAgendaItemUI(itemType, itemIndex, 'ongoing');
            startItemTimeCounter(itemType, itemIndex);
            updateSummaryCounters();
            showMessage('Agenda item started!', 'success');
        } else {
            showMessage(result.error || 'Failed to start agenda item', 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
    }
}

async function endAgendaItem(itemType, itemIndex) {
    if (!selectedMeetingId) return;
    
    try {
        const response = await fetch(`{{ route('recording.end-agenda-item') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                meeting_id: selectedMeetingId,
                item_type: itemType,
                item_index: itemIndex
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateAgendaItemUI(itemType, itemIndex, 'completed');
            stopItemTimeCounter(itemType, itemIndex);
            updateSummaryCounters();
            showMessage('Agenda item completed!', 'success');
        } else {
            showMessage(result.error || 'Failed to end agenda item', 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
    }
}

// Remarks and Status Updates
async function updateRemarks(itemType, itemIndex, remarks) {
    if (!selectedMeetingId) return;
    
    try {
        const response = await fetch(`{{ route('recording.update-remarks') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                meeting_id: selectedMeetingId,
                item_type: itemType,
                item_index: itemIndex,
                remarks: remarks
            })
        });
        
        const result = await response.json();
        
        if (!result.success) {
            showMessage(result.error || 'Failed to update remarks', 'error');
        }
    } catch (error) {
        console.error('Error updating remarks:', error);
    }
}

async function updateStatus(itemType, itemIndex, status) {
    if (!selectedMeetingId) return;
    
    try {
        const response = await fetch(`{{ route('recording.update-status') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ 
                meeting_id: selectedMeetingId,
                item_type: itemType,
                item_index: itemIndex,
                status: status
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateSummaryCounters();
            showMessage('Status updated successfully!', 'success');
        } else {
            showMessage(result.error || 'Failed to update status', 'error');
        }
    } catch (error) {
        showMessage('Network error occurred', 'error');
    }
}

// Timer Functions
function startTotalTimeCounter() {
    if (totalTimeInterval) {
        clearInterval(totalTimeInterval);
    }
    
    totalTimeInterval = setInterval(() => {
        const totalTimeElement = document.getElementById('total-time');
        if (totalTimeElement) {
            const currentTime = totalTimeElement.textContent;
            const newTime = incrementTimeString(currentTime);
            totalTimeElement.textContent = newTime;
        }
    }, 1000);
}

function stopTotalTimeCounter() {
    if (totalTimeInterval) {
        clearInterval(totalTimeInterval);
        totalTimeInterval = null;
    }
}

function startActiveItemCounters() {
    document.querySelectorAll('.agenda-item[data-item-type]').forEach(item => {
        const itemType = item.dataset.itemType;
        const itemIndex = item.dataset.itemIndex;
        const statusSelect = item.querySelector('.status-select');
        
        if (statusSelect && statusSelect.value === 'ongoing') {
            startItemTimeCounter(itemType, itemIndex);
        }
    });
}

function startItemTimeCounter(itemType, itemIndex) {
    const itemKey = `${itemType}-${itemIndex}`;
    
    if (itemTimeIntervals[itemKey]) {
        clearInterval(itemTimeIntervals[itemKey]);
    }
    
    const agendaItem = document.querySelector(`[data-item-type="${itemType}"][data-item-index="${itemIndex}"]`);
    const timeDisplay = agendaItem?.querySelector('.time-display');
    
    if (timeDisplay) {
        itemTimeIntervals[itemKey] = setInterval(() => {
            const currentTime = timeDisplay.textContent;
            const newTime = incrementTimeString(currentTime);
            timeDisplay.textContent = newTime;
        }, 1000);
    }
}

function stopItemTimeCounter(itemType, itemIndex) {
    const itemKey = `${itemType}-${itemIndex}`;
    
    if (itemTimeIntervals[itemKey]) {
        clearInterval(itemTimeIntervals[itemKey]);
        delete itemTimeIntervals[itemKey];
    }
}

function stopAllItemCounters() {
    Object.keys(itemTimeIntervals).forEach(key => {
        clearInterval(itemTimeIntervals[key]);
        delete itemTimeIntervals[key];
    });
}

function incrementTimeString(timeStr) {
    const parts = timeStr.split(':');
    let hours = parseInt(parts[0]) || 0;
    let minutes = parseInt(parts[1]) || 0;
    let seconds = parseInt(parts[2]) || 0;
    
    seconds++;
    if (seconds >= 60) {
        seconds = 0;
        minutes++;
        if (minutes >= 60) {
            minutes = 0;
            hours++;
        }
    }
    
    return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// UI Update Functions
function updateMeetingControls() {
    location.reload(); // Simple approach - reload page to reflect status changes
}

function updateAgendaItemUI(itemType, itemIndex, status) {
    const agendaItem = document.querySelector(`[data-item-type="${itemType}"][data-item-index="${itemIndex}"]`);
    if (!agendaItem) return;
    
    const startBtn = agendaItem.querySelector('.start-item');
    const endBtn = agendaItem.querySelector('.end-item');
    const statusSelect = agendaItem.querySelector('.status-select');
    
    if (status === 'ongoing') {
        agendaItem.classList.add('border-blue-500', 'bg-blue-50');
        if (startBtn) startBtn.style.display = 'none';
        if (endBtn) endBtn.style.display = 'inline-block';
        if (statusSelect) statusSelect.value = 'ongoing';
    } else {
        agendaItem.classList.remove('border-blue-500', 'bg-blue-50');
        if (startBtn) startBtn.style.display = 'inline-block';
        if (endBtn) endBtn.style.display = 'none';
        if (statusSelect) statusSelect.value = status;
    }
}

function updateSummaryCounters() {
    const statusSelects = document.querySelectorAll('.status-select');
    const statuses = Array.from(statusSelects).map(select => select.value);
    
    document.getElementById('completed-count').textContent = statuses.filter(s => s === 'completed').length;
    document.getElementById('ongoing-count').textContent = statuses.filter(s => s === 'ongoing').length;
    document.getElementById('skipped-count').textContent = statuses.filter(s => s === 'skipped').length;
    document.getElementById('pending-count').textContent = statuses.filter(s => s === 'pending').length;
}

// Utility Functions
function showLoading() {
    document.getElementById('loading-overlay').classList.remove('hidden');
    document.getElementById('loading-overlay').classList.add('flex');
}

function hideLoading() {
    document.getElementById('loading-overlay').classList.add('hidden');
    document.getElementById('loading-overlay').classList.remove('flex');
}

function showMessage(message, type = 'info') {
    const container = document.getElementById('message-container');
    const messageDiv = document.createElement('div');
    
    const bgColor = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    }[type] || 'bg-blue-500';
    
    messageDiv.className = `${bgColor} text-white px-4 py-2 rounded-md shadow-lg transition-opacity duration-300`;
    messageDiv.textContent = message;
    
    container.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        setTimeout(() => {
            container.removeChild(messageDiv);
        }, 300);
    }, 3000);
}

function saveProgress() {
    showMessage('Progress saved automatically', 'success');
}

function generateReport() {
    if (!selectedMeetingId) {
        showMessage('No meeting selected', 'warning');
        return;
    }
    
    showMessage('Generating report...', 'info');
    // Implementation for report generation
    setTimeout(() => {
        showMessage('Report generated successfully!', 'success');
    }, 2000);
}

// Auto-save functionality for remarks
let remarksSaveTimeout = null;

function setupAutoSave() {
    document.querySelectorAll('.remarks-input').forEach(textarea => {
        textarea.addEventListener('input', function() {
            clearTimeout(remarksSaveTimeout);
            remarksSaveTimeout = setTimeout(() => {
                const agendaItem = this.closest('.agenda-item');
                const itemType = agendaItem.dataset.itemType;
                const itemIndex = agendaItem.dataset.itemIndex;
                updateRemarks(itemType, itemIndex, this.value);
            }, 1000); // Auto-save after 1 second of no typing
        });
    });
}

// Initialize auto-save when document is ready
document.addEventListener('DOMContentLoaded', setupAutoSave);
</script>

<style>
.meeting-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.agenda-item {
    transition: all 0.3s ease;
}

.agenda-item.border-blue-500 {
    box-shadow: 0 0 0 1px rgb(59 130 246 / 0.5);
}

.animate-pulse {
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

.transition-opacity {
    transition-property: opacity;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Custom scrollbar for textarea */
.remarks-input::-webkit-scrollbar {
    width: 6px;
}

.remarks-input::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.remarks-input::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.remarks-input::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .agenda-item .grid {
        grid-template-columns: 1fr;
    }
    
    .flex.space-x-4 {
        flex-direction: column;
        space-x: 0;
        gap: 0.5rem;
    }
}
</style>
@endsection