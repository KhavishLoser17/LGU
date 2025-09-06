@extends('layouts.app')

@section('title', 'Agenda Management')

@section('content')
<nav class="bg-white shadow-sm border-b">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <i class="fas fa-calendar-alt text-blue-500 text-2xl mr-3"></i>
                <h1 class="text-xl font-bold text-gray-900">Agenda Management System</h1>
            </div>
            <div class="flex items-center space-x-4">
                <button id="openModalBtn" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>New Agenda
                </button>
            </div>
        </div>
    </div>
</nav>

<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Tabs -->
    <div class="mb-8">
        <nav class="flex space-x-8 border-b border-gray-200">
            <button id="manage-tab" class="py-2 px-1 border-b-2 border-blue-500 text-blue-600 font-medium tab-btn">
                Manage Agendas
            </button>
        </nav>
    </div>

    <!-- Modal -->
     <div id="agendaModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center hidden z-50">
                <div class="bg-white rounded-lg shadow-xl w-11/12 md:w-3/4 lg:w-2/3 max-h-screen overflow-y-auto">
                    <div class="p-6">
                        <div class="flex justify-between items-center border-b pb-4">
                            <h2 class="text-xl font-semibold text-gray-800">Create Meeting Agenda</h2>
                            <button id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>

                        <!-- Meeting Form -->
                        <form id="meetingForm" action="{{ route('store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-6">
                            @csrf

                            <!-- Basic Information Tab -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Meeting Title *</label>
                                    <input type="text" id="title" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                    <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>
                                <div>
                                    <label for="meeting_date" class="block text-sm font-medium text-gray-700 mb-1">Meeting Date *</label>
                                    <input type="date" id="meeting_date" name="meeting_date" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-1">Start Time *</label>
                                    <input type="time" id="start_time" name="start_time" required class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-1">End Time</label>
                                    <input type="time" id="end_time" name="end_time" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="district_selection" class="block text-sm font-medium text-gray-700 mb-1">District</label>
                                    <select id="district_selection" name="district_selection" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="" disabled selected>Select District</option>
                                        <option value="District 1">District 1</option>
                                        <option value="District 2">District 2</option>
                                        <option value="District 3">District 3</option>
                                        <option value="District 4">District 4</option>
                                        <option value="District 5">District 5</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="agenda_leader" class="block text-sm font-medium text-gray-700 mb-1">Agenda Leader</label>
                                    <select id="agenda_leader" name="agenda_leader" 
                                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                        <option value="" disabled selected>Select Agenda Leader</option>
                                        <option value="Juan Dela Cruz">Juan Dela Cruz</option>
                                        <option value="Maria Santos">Maria Santos</option>
                                        <option value="Jose Marquez">Jose Marquez</option>
                                        <option value="Wendy Agoncillo">Wendy Agoncillo</option>
                                        <option value="Gabriela Silang">Gabriela Silang</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Image Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Meeting Image</label>
                                <div id="uploadArea" class="border-2 border-dashed border-gray-300 rounded-md p-6 text-center cursor-pointer hover:border-blue-400 transition-colors">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="text-sm text-gray-600">Drag & drop your image here or click to browse</p>
                                    <p class="text-xs text-gray-500 mt-1">Supports JPG, PNG, GIF up to 5MB</p>
                                </div>
                                <input type="file" id="imageInput" name="image" accept="image/*" class="hidden">

                                <div id="imagePreview" class="mt-4 hidden">
                                    <p class="text-sm font-medium text-gray-700 mb-1">Preview</p>
                                    <div class="relative inline-block">
                                        <img id="previewImg" class="h-32 rounded-md shadow-sm border">
                                        <span id="fileName" class="block text-sm text-gray-600 mt-1"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Document Upload -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Supporting Documents</label>
                                <div class="flex items-center">
                                    <button type="button" id="browseDocuments" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-md flex items-center">
                                        <i class="fas fa-file-upload mr-2"></i> Browse Files
                                    </button>
                                    <span class="ml-3 text-sm text-gray-500">PDF, DOC, DOCX up to 5MB each</span>
                                </div>
                                <input type="file" id="documentInput" name="documents[]" multiple accept=".pdf,.doc,.docx" class="hidden">

                                <div id="documentsList" class="mt-3 space-y-2 max-h-40 overflow-y-auto"></div>
                            </div>

                            <!-- Agenda Items -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Agenda Items</label>

                                <!-- Default Agenda Items -->
                                <div class="mb-4">
                                    <div id="default-agenda-toggle" class="flex justify-between items-center p-3 bg-gray-100 rounded-t-md cursor-pointer">
                                        <span class="font-medium">Default Agenda Items</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div id="default-agenda-content" class="p-3 border border-t-0 rounded-b-md">
                                        <div id="agenda-items-container" class="space-y-2">
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded border agenda-item">
                                                <span>1. Introduction and Welcome</span>
                                            </div>
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded border agenda-item">
                                                <span>2. Review of Previous Meeting Minutes</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom Agenda Items -->
                                <div class="mb-4">
                                    <div id="custom-agenda-toggle" class="flex justify-between items-center p-3 bg-gray-100 rounded-t-md cursor-pointer">
                                        <span class="font-medium">Custom Agenda Items</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div id="custom-agenda-content" class="p-3 border border-t-0 rounded-b-md">
                                        <div class="flex mb-3">
                                            <input type="text" id="new-agenda-item" placeholder="Add new agenda item" class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500">
                                            <button type="button" id="add-agenda-item" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-md">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div id="custom-items-container" class="space-y-2"></div>
                                    </div>
                                </div>

                                <!-- Closing Remarks -->
                                <div>
                                    <div id="closing-remarks-toggle" class="flex justify-between items-center p-3 bg-gray-100 rounded-t-md cursor-pointer">
                                        <span class="font-medium">Closing Remarks</span>
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                    <div id="closing-remarks-content" class="p-3 border border-t-0 rounded-b-md">
                                        <div id="closing-items-container" class="space-y-2">
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded border agenda-item">
                                                <span>1. Summary of Decisions</span>
                                            </div>
                                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded border agenda-item">
                                                <span>2. Next Steps and Action Items</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Hidden fields for agenda data -->
                                <input type="hidden" id="defaultAgendaData" name="default_agenda_items">
                                <input type="hidden" id="customAgendaData" name="custom_agenda_items">
                                <input type="hidden" id="closingRemarksData" name="closing_remarks">
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 pt-4 border-t">
                                <button type="button" id="cancelModalBtn" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">Cancel</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Save Meeting</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Manage Agendas -->
      <div class="mb-6 flex flex-wrap gap-4">
        <select id="status-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        <select id="district-filter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <option value="">All Districts</option>
            <option value="District 1">District 1</option>
            <option value="District 2">District 2</option>
            <option value="District 3">District 3</option>
            <option value="All Districts">All Districts</option>
        </select>
    </div>

    <!-- Meeting Cards Grid -->
    @if($meetings->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="meetings-grid">
            @foreach($meetings as $meeting)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-shadow duration-300" 
                     data-status="{{ $meeting['status'] }}" 
                     data-district="{{ $meeting['district'] }}">
                    <div class="p-6">
                        <!-- Header with Image and Status -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                    @if($meeting['image_url'])
                                        <img src="{{ $meeting['image_url'] }}" alt="Meeting" class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <i class="fas fa-clipboard-list text-white text-lg"></i>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900 text-lg">{{ $meeting['title'] }}</h3>
                                    @if($meeting['description'])
                                        <p class="text-sm text-gray-500">{{ Str::limit($meeting['description'], 50) }}</p>
                                    @endif
                                </div>
                            </div>
                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full {{ $meeting['status_color']['bg'] }} {{ $meeting['status_color']['text'] }}">
                                {{ ucfirst($meeting['status']) }}
                            </span>
                        </div>

                        <!-- Details -->
                        <div class="space-y-3 mb-6">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt w-4 mr-3"></i>
                                <span>{{ $meeting['district'] }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-calendar w-4 mr-3"></i>
                                <span>Created: {{ $meeting['created_date'] }}</span>
                            </div>
                            @if($meeting['meeting_date'])
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-clock w-4 mr-3"></i>
                                    <span>Meeting: {{ $meeting['meeting_date'] }}</span>
                                </div>
                            @endif
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-file-alt w-4 mr-3"></i>
                                <span>{{ $meeting['has_documents'] ? 'Has documents' : 'No documents' }}</span>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <a href="" 
                               class="flex items-center px-3 py-2 text-sm font-medium text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors">
                                <i class="fas fa-edit mr-2"></i>
                                Edit
                            </a>
                            <div class="flex space-x-2">
                                @auth
                                    @if(auth()->user()->role === 'admin')
                                        <button onclick="approveMeeting({{ $meeting['id'] }})" 
                                                class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors" 
                                                title="Approve" {{ $meeting['status'] === 'approved' ? 'disabled' : '' }}>
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="rejectMeeting({{ $meeting['id'] }})" 
                                                class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors" 
                                                title="Reject" {{ $meeting['status'] === 'rejected' ? 'disabled' : '' }}>
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                @endauth
                                <button onclick="deleteMeeting({{ $meeting['id'] }})" 
                                        class="p-2 text-gray-600 hover:text-gray-800 hover:bg-gray-50 rounded-lg transition-colors" 
                                        title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-list text-gray-400 text-xl"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No meetings found</h3>
            <p class="text-gray-500">Create your first meeting to get started.</p>
            <a href="" 
               class="mt-4 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Create Meeting
            </a>
        </div>
    @endif
</div>

 <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cache DOM elements with null checks
            const getElement = (id) => {
                const element = document.getElementById(id);
                if (!element) {
                    console.error(`Element with ID '${id}' not found`);
                }
                return element;
            };

            const elements = {
                modal: getElement('agendaModal'),
                openModalBtn: getElement('openModalBtn'),
                closeModalBtn: getElement('closeModalBtn'),
                cancelModalBtn: getElement('cancelModalBtn'),
                meetingForm: getElement('meetingForm'),
                uploadArea: getElement('uploadArea'),
                imageInput: getElement('imageInput'),
                imagePreview: getElement('imagePreview'),
                previewImg: getElement('previewImg'),
                fileName: getElement('fileName'),
                browseDocuments: getElement('browseDocuments'),
                documentInput: getElement('documentInput'),
                documentsList: getElement('documentsList'),
                addItemBtn: getElement('add-agenda-item'),
                itemInput: getElement('new-agenda-item'),
                customContainer: getElement('custom-items-container')
            };

            // Check if essential elements exist
            if (!elements.modal || !elements.meetingForm) {
                console.error('Essential elements not found. Please check your HTML structure.');
                return;
            }

            // Modal functionality
            function closeModal() {
                elements.modal.classList.add('hidden');
                document.body.style.overflow = 'auto';
            }

            if (elements.openModalBtn) {
                elements.openModalBtn.addEventListener('click', () => {
                    elements.modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }

            if (elements.closeModalBtn) {
                elements.closeModalBtn.addEventListener('click', closeModal);
            }

            if (elements.cancelModalBtn) {
                elements.cancelModalBtn.addEventListener('click', closeModal);
            }

            elements.modal.addEventListener('click', (event) => {
                if (event.target === elements.modal) closeModal();
            });

            // Image upload functionality
            if (elements.uploadArea && elements.imageInput) {
                elements.uploadArea.addEventListener('click', () => elements.imageInput.click());

                elements.imageInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        // Validate file size (5MB limit)
                        if (file.size > 5 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File too large',
                                text: 'Please select an image smaller than 5MB'
                            });
                            this.value = ''; // Clear the file input
                            return;
                        }

                        // Validate file type
                        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
                        if (!validTypes.includes(file.type)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid file type',
                                text: 'Please select a valid image (JPEG, PNG, GIF)'
                            });
                            this.value = ''; // Clear the file input
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            if (elements.previewImg) elements.previewImg.src = e.target.result;
                            if (elements.fileName) elements.fileName.textContent = file.name;
                            if (elements.imagePreview) elements.imagePreview.classList.remove('hidden');
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Drag and drop for image
            if (elements.uploadArea) {
                ['dragover', 'dragleave', 'drop'].forEach(eventName => {
                    elements.uploadArea.addEventListener(eventName, (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        if (eventName === 'dragover') {
                            elements.uploadArea.classList.add('drag-over');
                        } else {
                            elements.uploadArea.classList.remove('drag-over');
                        }

                        if (eventName === 'drop') {
                            const files = e.dataTransfer.files;
                            if (files.length > 0 && files[0].type.startsWith('image/')) {
                                if (elements.imageInput) {
                                    elements.imageInput.files = files;
                                    elements.imageInput.dispatchEvent(new Event('change'));
                                }
                            }
                        }
                    });
                });
            }

            // Document upload functionality
            if (elements.browseDocuments && elements.documentInput) {
                elements.browseDocuments.addEventListener('click', () => elements.documentInput.click());

                elements.documentInput.addEventListener('change', function() {
                    const files = Array.from(this.files);
                    if (elements.documentsList) elements.documentsList.innerHTML = '';

                    // Validate files
                    const validTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

                    files.forEach((file, index) => {
                        // Validate file type
                        if (!validTypes.includes(file.type)) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid file type',
                                text: 'Please select only PDF or Word documents'
                            });
                            this.value = ''; // Clear the file input
                            if (elements.documentsList) elements.documentsList.innerHTML = '';
                            return;
                        }

                        // Validate file size (5MB limit)
                        if (file.size > 5 * 1024 * 1024) {
                            Swal.fire({
                                icon: 'error',
                                title: 'File too large',
                                text: 'Please select documents smaller than 5MB'
                            });
                            this.value = ''; // Clear the file input
                            if (elements.documentsList) elements.documentsList.innerHTML = '';
                            return;
                        }

                        if (elements.documentsList) {
                            const fileElement = document.createElement('div');
                            fileElement.className = 'flex items-center justify-between p-2 bg-gray-50 rounded border';
                            fileElement.innerHTML = `
                                <div class="flex items-center">
                                    <i class="fas ${file.type === 'application/pdf' ? 'fa-file-pdf text-red-500' : 'fa-file-word text-blue-500'} text-xl mr-3"></i>
                                    <span class="text-sm text-gray-700">${file.name}</span>
                                    <span class="text-xs text-gray-500 ml-2">(${Math.round(file.size / 1024)} KB)</span>
                                </div>
                                <button type="button" class="text-red-500 hover:text-red-700 remove-document" data-index="${index}">
                                    <i class="fas fa-times"></i>
                                </button>
                            `;
                            elements.documentsList.appendChild(fileElement);
                        }
                    });
                });
            }

            // Remove document functionality
            if (elements.documentsList) {
                elements.documentsList.addEventListener('click', function(e) {
                    const removeBtn = e.target.closest('.remove-document');
                    if (removeBtn && elements.documentInput) {
                        const index = parseInt(removeBtn.dataset.index);
                        const dt = new DataTransfer();
                        const files = elements.documentInput.files;

                        for (let i = 0; i < files.length; i++) {
                            if (index !== i) dt.items.add(files[i]);
                        }

                        elements.documentInput.files = dt.files;
                        removeBtn.closest('div').remove();
                    }
                });
            }

            // Dropdown setup
            function setupDropdown(toggleId, contentId) {
                const toggle = getElement(toggleId);
                const content = getElement(contentId);

                if (!toggle || !content) return;

                const chevron = toggle.querySelector('i');
                if (!chevron) return;

                toggle.addEventListener('click', () => {
                    content.classList.toggle('hidden');
                    chevron.classList.toggle('fa-chevron-down');
                    chevron.classList.toggle('fa-chevron-up');
                });
            }

            setupDropdown('default-agenda-toggle', 'default-agenda-content');
            setupDropdown('custom-agenda-toggle', 'custom-agenda-content');
            setupDropdown('closing-remarks-toggle', 'closing-remarks-content');

            // Add custom agenda item
            if (elements.addItemBtn && elements.itemInput && elements.customContainer) {
                elements.addItemBtn.addEventListener('click', function() {
                    const value = elements.itemInput.value.trim();
                    if (value !== '') {
                        const itemCount = elements.customContainer.children.length + 1;
                        const newItem = document.createElement('div');
                        newItem.className = 'flex items-center justify-between p-2 bg-gray-50 rounded border agenda-item';
                        newItem.innerHTML = `
                            <span>${itemCount}. ${value}</span>
                            <div>
                                <i class="fas fa-times text-red-400 cursor-pointer delete-item"></i>
                            </div>
                        `;

                        newItem.querySelector('.delete-item').addEventListener('click', function() {
                            newItem.remove();
                            // Renumber items
                            const items = elements.customContainer.querySelectorAll('div');
                            items.forEach((item, index) => {
                                const text = item.querySelector('span').textContent;
                                item.querySelector('span').textContent = `${index + 1}. ${text.split('. ')[1]}`;
                            });
                        });

                        elements.customContainer.appendChild(newItem);
                        elements.itemInput.value = '';
                    }
                });
            }

            // Form submission
            if (elements.meetingForm) {
                elements.meetingForm.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    // Show loading immediately
                    Swal.fire({
                        title: 'Saving Meeting...',
                        html: 'Please wait while we process your request.',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {
                        // Prepare agenda data
                        const getAgendaItems = (selector) => {
                            const container = document.querySelector(selector);
                            if (!container) return [];
                            return Array.from(container.querySelectorAll('.agenda-item'))
                                .map(item => item.querySelector('span').textContent);
                        };

                        const defaultAgendaData = getElement('defaultAgendaData');
                        const customAgendaData = getElement('customAgendaData');
                        const closingRemarksData = getElement('closingRemarksData');

                        // Ensure we're sending proper JSON arrays
                        if (defaultAgendaData) defaultAgendaData.value = JSON.stringify(getAgendaItems('#agenda-items-container'));
                        if (customAgendaData) customAgendaData.value = JSON.stringify(getAgendaItems('#custom-items-container'));

                        // Fix for closing remarks - ensure it's always an array
                        const closingItems = getAgendaItems('#closing-items-container');
                        if (closingRemarksData) closingRemarksData.value = JSON.stringify(closingItems.length > 0 ? closingItems : []);

                        // Submit form
                        const formData = new FormData(this);
                        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
                        const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                        const response = await fetch(this.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                confirmButtonText: 'OK',
                                timer: 3000
                            });

                            // Reset form and close modal
                            this.reset();
                            if (elements.imagePreview) elements.imagePreview.classList.add('hidden');
                            if (elements.documentsList) elements.documentsList.innerHTML = '';
                            if (elements.customContainer) elements.customContainer.innerHTML = '';
                            closeModal();

                        } else {
                            if (data.errors) {
                                let errorMessages = '';
                                for (const field in data.errors) {
                                    errorMessages += data.errors[field].join('<br>') + '<br>';
                                }
                                throw new Error(errorMessages);
                            }
                            throw new Error(data.message || 'Something went wrong');
                        }

                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: error.message || 'Failed to save meeting. Please try again.',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    </script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Filtering functionality
    const statusFilter = document.getElementById('status-filter');
    const districtFilter = document.getElementById('district-filter');
    const meetingCards = document.querySelectorAll('#meetings-grid > div');

    function filterMeetings() {
        const statusValue = statusFilter.value.toLowerCase();
        const districtValue = districtFilter.value;

        meetingCards.forEach(card => {
            const cardStatus = card.dataset.status;
            const cardDistrict = card.dataset.district;

            const statusMatch = !statusValue || cardStatus === statusValue;
            const districtMatch = !districtValue || cardDistrict === districtValue;

            if (statusMatch && districtMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    statusFilter.addEventListener('change', filterMeetings);
    districtFilter.addEventListener('change', filterMeetings);
});

// Meeting action functions
function approveMeeting(meetingId) {
    if (confirm('Are you sure you want to approve this meeting?')) {
        fetch(`/meetings/${meetingId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error approving meeting');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error approving meeting');
        });
    }
}

function rejectMeeting(meetingId) {
    if (confirm('Are you sure you want to reject this meeting?')) {
        fetch(`/meetings/${meetingId}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error rejecting meeting');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error rejecting meeting');
        });
    }
}

function deleteMeeting(meetingId) {
    if (confirm('Are you sure you want to delete this meeting? This action cannot be undone.')) {
        fetch(`/meetings/${meetingId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting meeting');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting meeting');
        });
    }
}
</script>
@endsection
