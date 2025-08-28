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
            <button id="approved-tab" class="py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 tab-btn">
                Approved Agendas
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
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea id="description" name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                                </div>

                                <div>
                                    <label for="district_selection" class="block text-sm font-medium text-gray-700 mb-1">District</label>
                                    <input type="text" id="district_selection" name="district_selection" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                                </div>

                                <div>
                                    <label for="agenda_leader" class="block text-sm font-medium text-gray-700 mb-1">Agenda Leader</label>
                                    <input type="text" id="agenda_leader" name="agenda_leader" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
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
    <div id="manage-content" class="tab-content">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-900">Manage Agendas</h2>
                    <div class="flex space-x-2">
                        <select class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option>All Status</option>
                            <option>Pending</option>
                            <option>Approved</option>
                            <option>Rejected</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">District</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded object-cover mr-3" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' fill='%23e5e7eb'/%3E%3Ctext x='20' y='20' text-anchor='middle' dy='0.35em' fill='%236b7280'%3E📋%3C/text%3E%3C/svg%3E" alt="">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Monthly District Meeting</div>
                                        <div class="text-sm text-gray-500">January 2025 Session</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">District 1</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jan 15, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded object-cover mr-3" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' fill='%23e5e7eb'/%3E%3Ctext x='20' y='20' text-anchor='middle' dy='0.35em' fill='%236b7280'%3E📋%3C/text%3E%3C/svg%3E" alt="">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Quarterly Review Session</div>
                                        <div class="text-sm text-gray-500">Q4 2024 Review</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">All Districts</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    Rejected
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jan 12, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded object-cover mr-3" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='40' height='40' viewBox='0 0 40 40'%3E%3Crect width='40' height='40' fill='%23e5e7eb'/%3E%3Ctext x='20' y='20' text-anchor='middle' dy='0.35em' fill='%236b7280'%3E📋%3C/text%3E%3C/svg%3E" alt="">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Annual Budget Planning</div>
                                        <div class="text-sm text-gray-500">Fiscal Year 2025</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">District 3</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    Approved
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Jan 10, 2025</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-indigo-600 hover:text-indigo-900 mr-3">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 mr-3">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Approved Agendas -->
    <div id="approved-content" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b">
                <h2 class="text-xl font-bold text-gray-900">Approved Agendas - Ready for Landing Page</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <img class="w-full h-32 object-cover rounded mb-3" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='150' viewBox='0 0 300 150'%3E%3Crect width='300' height='150' fill='%23e5e7eb'/%3E%3Ctext x='150' y='75' text-anchor='middle' dy='0.35em' fill='%236b7280'%3EMeeting Image%3C/text%3E%3C/svg%3E" alt="Meeting">
                    <h3 class="font-semibold text-gray-900 mb-2">District Leadership Summit</h3>
                    <p class="text-sm text-gray-600 mb-3">Annual leadership development session for all district coordinators...</p>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Approved
                        </span>
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-print mr-1"></i>Print
                            </button>
                            <button class="text-green-600 hover:text-green-800 text-sm">
                                <i class="fas fa-globe mr-1"></i>Publish
                            </button>
                        </div>
                    </div>
                </div>

                <div class="border rounded-lg p-4 hover:shadow-md transition-shadow">
                    <img class="w-full h-32 object-cover rounded mb-3" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='300' height='150' viewBox='0 0 300 150'%3E%3Crect width='300' height='150' fill='%23e5e7eb'/%3E%3Ctext x='150' y='75' text-anchor='middle' dy='0.35em' fill='%236b7280'%3EMeeting Image%3C/text%3E%3C/svg%3E" alt="Meeting">
                    <h3 class="font-semibold text-gray-900 mb-2">Budget Planning Session</h3>
                    <p class="text-sm text-gray-600 mb-3">Comprehensive budget review and planning for the upcoming fiscal year...</p>
                    <div class="flex items-center justify-between">
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Approved
                        </span>
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-print mr-1"></i>Print
                            </button>
                            <button class="text-green-600 hover:text-green-800 text-sm">
                                <i class="fas fa-globe mr-1"></i>Publish
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
@endsection
