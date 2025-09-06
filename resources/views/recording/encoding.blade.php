@extends('layouts.app')
@section('title', 'AI Meeting Journal - Intelligent Document Processing & Export')

@section('content')
    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .upload-area {
            border: 2px dashed #e2e8f0;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #667eea;
            background-color: #f8fafc;
        }
        .upload-area.dragover {
            border-color: #667eea;
            background-color: #eff6ff;
            transform: scale(1.02);
        }
        .ai-button {
            background: linear-gradient(45deg, #4F46E5, #7C3AED);
            background-size: 200% 200%;
            animation: gradientShift 3s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .editor-content {
            min-height: 600px;
            outline: none;
        }
        .floating-action {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 50;
        }
        @media print {
            .no-print { display: none !important; }
            .editor-content { border: none !important; }
            body * { visibility: hidden; }
            .printable, .printable * { visibility: visible; }
            .printable { position: absolute; left: 0; top: 0; width: 100%; }
        }
        .export-option {
            transition: all 0.3s ease;
        }
        .export-option:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
        <!-- Header -->
        <header class="glass-effect shadow-lg no-print sticky top-0 z-40">
            <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-robot text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                                AI Meeting Journal
                            </h1>
                            <p class="text-sm text-gray-600">Intelligent Document Processing & Export</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <button onclick="saveDocument()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2.5 rounded-xl flex items-center space-x-2 transition-all transform hover:scale-105">
                            <i class="fas fa-save"></i>
                            <span>Save</span>
                        </button>
                        <button onclick="printDocument()" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2.5 rounded-xl flex items-center space-x-2 transition-all transform hover:scale-105">
                            <i class="fas fa-print"></i>
                            <span>Print</span>
                        </button>
                        <button onclick="toggleExportOptions()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl flex items-center space-x-2 transition-all transform hover:scale-105">
                            <i class="fas fa-file-export"></i>
                            <span>Export</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Export Options Modal -->
        <div id="exportModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-semibold text-gray-800">Export Document</h3>
                    <button onclick="toggleExportOptions()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="space-y-4">
                    <div onclick="exportToPDF()" class="export-option cursor-pointer flex items-center p-4 border border-gray-200 rounded-xl hover:bg-blue-50 transition-all">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Export to PDF</div>
                            <div class="text-sm text-gray-500">High-quality document for printing</div>
                        </div>
                    </div>
                    <div onclick="exportToWord()" class="export-option cursor-pointer flex items-center p-4 border border-gray-200 rounded-xl hover:bg-blue-50 transition-all">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-file-word text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-800">Export to Word</div>
                            <div class="text-sm text-gray-500">Editable document for Microsoft Word</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <main class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <div class="glass-effect rounded-2xl shadow-xl p-6 mb-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cloud-upload-alt text-blue-600 text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Upload Document</h2>
                        </div>
                        
                        <!-- Upload Area -->
                        <div id="uploadArea" class="upload-area rounded-xl p-8 text-center mb-4">
                            <div class="mb-4">
                                <i class="fas fa-file-upload text-4xl text-gray-400 mb-4"></i>
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">Drop your files here</h3>
                                <p class="text-sm text-gray-500 mb-4">PDF, Word (DOC/DOCX), or Text files</p>
                                <input type="file" id="fileInput" accept=".pdf,.doc,.docx,.txt" style="display: none;" onchange="handleFileUpload(event)">
                                <button onclick="document.getElementById('fileInput').click()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-all transform hover:scale-105">
                                    <i class="fas fa-folder-open mr-2"></i>
                                    Choose File
                                </button>
                            </div>
                        </div>

                        <!-- Uploaded Files List -->
                        <div id="uploadedFiles" class="space-y-3 mb-6">
                            <!-- Files will be displayed here -->
                        </div>

                        <!-- AI Summarize Button -->
                        <button id="aiSummarizeBtn" onclick="summarizeWithAI()" class="w-full ai-button text-white font-bold py-4 px-6 rounded-xl shadow-lg transform transition-all duration-300 hover:scale-105 pulse-animation" style="display: none;">
                            <i class="fas fa-magic mr-3 text-xl"></i>
                            <span class="text-lg">✨ Summarize with AI ✨</span>
                        </button>
                    </div>

                    <!-- Document Templates -->
                    <div class="glass-effect rounded-2xl shadow-xl p-6">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-file-alt text-purple-600 text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Quick Templates</h2>
                        </div>
                        
                        <div class="space-y-3">
                            <button onclick="loadTemplate('blank')" class="w-full text-left p-4 rounded-xl hover:bg-gray-50 border border-gray-200 transition-all transform hover:scale-105">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-file text-gray-600 text-xl"></i>
                                    <div>
                                        <div class="font-semibold text-gray-800">Blank Document</div>
                                        <div class="text-sm text-gray-500">Start from scratch</div>
                                    </div>
                                </div>
                            </button>
                            <button onclick="loadTemplate('meeting')" class="w-full text-left p-4 rounded-xl hover:bg-gray-50 border border-gray-200 transition-all transform hover:scale-105">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-users text-blue-600 text-xl"></i>
                                    <div>
                                        <div class="font-semibold text-gray-800">Meeting Agenda</div>
                                        <div class="text-sm text-gray-500">Standard meeting format</div>
                                    </div>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Main Editor -->
                <div class="lg:col-span-2">
                    <div class="glass-effect rounded-2xl shadow-xl">
                        <!-- Toolbar -->
                        <div class="border-b border-gray-200 p-4 rounded-t-2xl no-print">
                            <div class="flex flex-wrap gap-2">
                                <!-- Text Formatting -->
                                <div class="flex items-center space-x-1 bg-gray-50 rounded-lg p-1">
                                    <button onclick="formatText('bold')" class="toolbar-button p-2 rounded hover:bg-white transition-all" title="Bold">
                                        <i class="fas fa-bold text-gray-700"></i>
                                    </button>
                                    <button onclick="formatText('italic')" class="toolbar-button p-2 rounded hover:bg-white transition-all" title="Italic">
                                        <i class="fas fa-italic text-gray-700"></i>
                                    </button>
                                    <button onclick="formatText('underline')" class="toolbar-button p-2 rounded hover:bg-white transition-all" title="Underline">
                                        <i class="fas fa-underline text-gray-700"></i>
                                    </button>
                                </div>

                                <!-- Alignment -->
                                <div class="flex items-center space-x-1 bg-gray-50 rounded-lg p-1">
                                    <button onclick="formatText('justifyLeft')" class="toolbar-button p-2 rounded hover:bg-white transition-all" title="Align Left">
                                        <i class="fas fa-align-left text-gray-700"></i>
                                    </button>
                                    <button onclick="formatText('justifyCenter')" class="toolbar-button p-2 rounded hover:bg-white transition-all" title="Center">
                                        <i class="fas fa-align-center text-gray-700"></i>
                                    </button>
                                    <button onclick="formatText('justifyRight')" class="toolbar-button p-2 rounded hover:bg-white transition-all" title="Align Right">
                                        <i class="fas fa-align-right text-gray-700"></i>
                                    </button>
                                </div>

                                <!-- Lists -->
                                <div class="flex items-center space-x-1 bg-gray-50 rounded-lg p-1">
                                    <button onclick="formatText('insertUnorderedList')" class="toolbar-button p-2 rounded hover:bg-white transition-all" title="Bullet List">
                                        <i class="fas fa-list-ul text-gray-700"></i>
                                    </button>
                                    <button onclick="formatText('insertOrderedList')" class="toolbar-button p-2 rounded hover:bg-white transition-all" title="Numbered List">
                                        <i class="fas fa-list-ol text-gray-700"></i>
                                    </button>
                                </div>

                                <!-- Font Size -->
                                <select onchange="changeFontSize(this.value)" class="bg-gray-50 border-0 rounded-lg px-3 py-2 text-sm font-medium">
                                    <option value="12px">12px</option>
                                    <option value="14px" selected>14px</option>
                                    <option value="16px">16px</option>
                                    <option value="18px">18px</option>
                                    <option value="20px">20px</option>
                                    <option value="24px">24px</option>
                                </select>
                            </div>
                        </div>

                        <!-- Document Header (Printable) -->
                        <div class="p-6 border-b border-gray-200 printable">
                            <input type="text" id="docTitle" placeholder="Enter Document Title" class="text-3xl font-bold w-full border-none outline-none mb-4 text-gray-800 placeholder-gray-400">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-blue-600 no-print"></i>
                                    <label class="font-semibold text-gray-700">Date:</label>
                                    <input type="date" id="docDate" class="border border-gray-300 rounded-lg px-3 py-2 flex-1 no-print">
                                    <span id="docDateDisplay" class="print-only font-medium"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-clock text-green-600 no-print"></i>
                                    <label class="font-semibold text-gray-700">Time:</label>
                                    <input type="time" id="docTime" class="border border-gray-300 rounded-lg px-3 py-2 flex-1 no-print">
                                    <span id="docTimeDisplay" class="print-only font-medium"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-map-marker-alt text-red-600 no-print"></i>
                                    <label class="font-semibold text-gray-700">Location:</label>
                                    <input type="text" id="docLocation" placeholder="Meeting location" class="border border-gray-300 rounded-lg px-3 py-2 flex-1 no-print">
                                    <span id="docLocationDisplay" class="print-only font-medium"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Editor Content (Printable) -->
                        <div class="p-6 printable">
                            <div id="editor" class="editor-content border border-gray-200 rounded-xl p-6 bg-white shadow-inner" contenteditable="true" onkeyup="updateWordCount()">
                                <div class="text-center text-gray-400 py-20">
                                    <i class="fas fa-edit text-6xl mb-4"></i>
                                    <h3 class="text-xl font-semibold mb-2">Start Creating Your Document</h3>
                                    <p>Upload a file for AI summarization or start typing your meeting notes here...</p>
                                </div>
                            </div>
                            
                            <!-- Status Bar -->
                            <div class="flex justify-between items-center mt-4 p-4 bg-gray-50 rounded-lg no-print">
                                <div class="flex items-center space-x-6 text-sm text-gray-600">
                                    <span id="wordCount" class="flex items-center"><i class="fas fa-file-word mr-2 text-blue-600"></i>Words: 0</span>
                                    <span id="charCount" class="flex items-center"><i class="fas fa-keyboard mr-2 text-green-600"></i>Characters: 0</span>
                                </div>
                                <div id="lastSaved" class="text-xs text-gray-500"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Floating Action Button -->
        <div class="floating-action no-print">
            <button onclick="newDocument()" class="bg-purple-600 hover:bg-purple-700 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center transition-all transform hover:scale-110">
                <i class="fas fa-plus text-xl"></i>
            </button>
        </div>

        <!-- Loading Modal -->
        <div id="loadingModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[99999] hidden">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-spinner fa-spin text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Processing with AI</h3>
                <p class="text-gray-600 mb-6">Your document is being analyzed and summarized by our AI engine</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                </div>
            </div>
        </div>
    </div>

    
    <script>
        let uploadedFile = null;
        let currentDocId = null;

        // Initialize
        document.getElementById('docDate').value = new Date().toISOString().split('T')[0];
        document.getElementById('docTime').value = new Date().toTimeString().slice(0, 5);
        updateDisplayFields();

        // Drag and drop functionality
        const uploadArea = document.getElementById('uploadArea');
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.classList.add('dragover');
        }

        function unhighlight() {
            uploadArea.classList.remove('dragover');
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleFiles(files);
        }

        function handleFileUpload(event) {
            const files = event.target.files;
            handleFiles(files);
        }

        function handleFiles(files) {
            if (files.length === 0) return;
            
            const file = files[0];
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain'];
            const allowedExtensions = ['.pdf', '.doc', '.docx', '.txt'];
            
            const isValidType = allowedTypes.includes(file.type) || allowedExtensions.some(ext => file.name.toLowerCase().endsWith(ext));
            
            if (!isValidType) {
                showNotification('Please upload a PDF, Word document (DOC/DOCX), or text file', 'error');
                return;
            }
            
            uploadedFile = file;
            displayUploadedFile(file);
            document.getElementById('aiSummarizeBtn').style.display = 'block';
        }

        function displayUploadedFile(file) {
            const container = document.getElementById('uploadedFiles');
            container.innerHTML = '';
            
            let icon = 'fa-file-alt';
            let color = 'green';
            
            if (file.type === 'application/pdf') {
                icon = 'fa-file-pdf';
                color = 'red';
            } else if (file.type.includes('word') || file.name.endsWith('.doc') || file.name.endsWith('.docx')) {
                icon = 'fa-file-word';
                color = 'blue';
            }
            
            const fileElement = document.createElement('div');
            fileElement.className = 'flex items-center justify-between p-3 bg-green-50 border border-green-200 rounded-lg';
            
            fileElement.innerHTML = `
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-${color}-100 rounded-lg flex items-center justify-center">
                        <i class="fas ${icon} text-${color}-600"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">${file.name}</div>
                        <div class="text-sm text-gray-500">${(file.size / 1024).toFixed(1)} KB</div>
                    </div>
                </div>
                <button onclick="removeFile()" class="text-red-600 hover:text-red-800 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            container.appendChild(fileElement);
        }

        function removeFile() {
            uploadedFile = null;
            document.getElementById('uploadedFiles').innerHTML = '';
            document.getElementById('aiSummarizeBtn').style.display = 'none';
        }

        async function summarizeWithAI() {
            if (!uploadedFile) return;
            
            // Show loading modal
            const modal = document.getElementById('loadingModal');
            const progressBar = document.getElementById('progressBar');
            modal.classList.remove('hidden');
            
            // Simulate progress
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 5;
                progressBar.style.width = `${Math.min(progress, 90)}%`;
                if (progress >= 90) clearInterval(progressInterval);
            }, 200);
            
            try {
                // Create FormData for file upload
                const formData = new FormData();
                formData.append('file', uploadedFile);
                
                // Make API call to Laravel backend
                const response = await fetch('/api/ai-summarize', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    // Complete progress bar
                    progressBar.style.width = '100%';
                    
                    // Update document with AI summary
                    document.getElementById('docTitle').value = data.title;
                    document.getElementById('editor').innerHTML = data.content;
                    updateWordCount();
                    updateDisplayFields();
                    
                    // Success feedback
                    setTimeout(() => {
                        modal.classList.add('hidden');
                        showNotification('AI summary completed successfully!', 'success');
                    }, 500);
                } else {
                    throw new Error(data.message || 'Unknown error occurred');
                }
                
            } catch (error) {
                console.error('Error:', error);
                modal.classList.add('hidden');
                showNotification('Error processing document: ' + error.message, 'error');
            }
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg text-white font-semibold transform transition-all duration-300 z-50 ${
                type === 'success' ? 'bg-green-600' : 'bg-red-600'
            }`;
            notification.innerHTML = `
                <div class="flex items-center space-x-2">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 4000);
        }

        function formatText(command) {
            document.execCommand(command, false, null);
            document.getElementById('editor').focus();
        }

        function changeFontSize(size) {
            document.execCommand('fontSize', false, '7');
            const fontElements = document.querySelectorAll('font[size="7"]');
            fontElements.forEach(el => {
                el.removeAttribute('size');
                el.style.fontSize = size;
            });
            document.getElementById('editor').focus();
        }

        function newDocument() {
            currentDocId = null;
            document.getElementById('docTitle').value = '';
            document.getElementById('docDate').value = new Date().toISOString().split('T')[0];
            document.getElementById('docTime').value = new Date().toTimeString().slice(0, 5);
            document.getElementById('docLocation').value = '';
            document.getElementById('editor').innerHTML = `
                <div class="text-center text-gray-400 py-20">
                    <i class="fas fa-edit text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold mb-2">Start Creating Your Document</h3>
                    <p>Upload a file for AI summarization or start typing your meeting notes here...</p>
                </div>
            `;
            updateWordCount();
            updateDisplayFields();
            
            // Clear uploaded files
            uploadedFile = null;
            document.getElementById('uploadedFiles').innerHTML = '';
            document.getElementById('aiSummarizeBtn').style.display = 'none';
        }

        function loadTemplate(type) {
            let content = '';
            let title = '';
            
            if (type === 'meeting') {
                title = 'Meeting Agenda Template';
                content = `
                    <h2><strong>Meeting Agenda</strong></h2>
                    
                    <h3><strong>Meeting Details</strong></h3>
                    <p><strong>Purpose:</strong> [Enter meeting purpose]</p>
                    <p><strong>Duration:</strong> [Estimated time]</p>
                    <p><strong>Attendees:</strong> [List participants]</p>
                    
                    <h3><strong>Agenda Items</strong></h3>
                    <ol>
                        <li><strong>Opening & Welcome</strong> (5 mins)</li>
                        <li><strong>Review Previous Minutes</strong> (10 mins)</li>
                        <li><strong>Main Discussion Topics</strong>
                            <ul>
                                <li>Topic 1: [Description and time allocation]</li>
                                <li>Topic 2: [Description and time allocation]</li>
                                <li>Topic 3: [Description and time allocation]</li>
                            </ul>
                        </li>
                        <li><strong>Action Items & Next Steps</strong> (15 mins)</li>
                        <li><strong>Closing & Adjournment</strong> (5 mins)</li>
                    </ol>
                    
                    <h3><strong>Notes Section</strong></h3>
                    <p>[Use this space for meeting notes and important points discussed]</p>
                    
                    <h3><strong>Action Items</strong></h3>
                    <table border="1" style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background-color: #f3f4f6;">
                                <th style="padding: 8px; text-align: left;">Action Item</th>
                                <th style="padding: 8px; text-align: left;">Responsible Person</th>
                                <th style="padding: 8px; text-align: left;">Due Date</th>
                                <th style="padding: 8px; text-align: left;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="padding: 8px;">[Action description]</td>
                                <td style="padding: 8px;">[Name]</td>
                                <td style="padding: 8px;">[Date]</td>
                                <td style="padding: 8px;">[Pending/In Progress/Complete]</td>
                            </tr>
                            <tr>
                                <td style="padding: 8px;">[Action description]</td>
                                <td style="padding: 8px;">[Name]</td>
                                <td style="padding: 8px;">[Date]</td>
                                <td style="padding: 8px;">[Pending/In Progress/Complete]</td>
                            </tr>
                        </tbody>
                    </table>
                `;
            } else {
                title = '';
                content = `
                    <div class="text-center text-gray-400 py-20">
                        <i class="fas fa-edit text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold mb-2">Blank Document Ready</h3>
                        <p>Start typing your content here or upload a document for AI summarization...</p>
                    </div>
                `;
            }
            
            document.getElementById('docTitle').value = title;
            document.getElementById('editor').innerHTML = content;
            updateWordCount();
            updateDisplayFields();
        }

        function saveDocument() {
            const title = document.getElementById('docTitle').value || 'Untitled Document';
            document.getElementById('lastSaved').textContent = `Last saved: ${new Date().toLocaleTimeString()}`;
            
            // Visual feedback
            const saveBtn = event.currentTarget;
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Saved!';
            saveBtn.classList.add('bg-green-600');
            
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.classList.remove('bg-green-600');
            }, 2000);
        }

        function updateWordCount() {
            const content = document.getElementById('editor').innerText;
            const words = content.trim().split(/\s+/).filter(word => word.length > 0).length;
            const chars = content.length;
            
            // Don't count placeholder text
            if (content.includes('Start Creating Your Document') || content.includes('Blank Document Ready')) {
                document.getElementById('wordCount').innerHTML = '<i class="fas fa-file-word mr-2 text-blue-600"></i>Words: 0';
                document.getElementById('charCount').innerHTML = '<i class="fas fa-keyboard mr-2 text-green-600"></i>Characters: 0';
            } else {
                document.getElementById('wordCount').innerHTML = `<i class="fas fa-file-word mr-2 text-blue-600"></i>Words: ${words}`;
                document.getElementById('charCount').innerHTML = `<i class="fas fa-keyboard mr-2 text-green-600"></i>Characters: ${chars}`;
            }
        }

        function updateDisplayFields() {
            // Update display fields for printing
            document.getElementById('docDateDisplay').textContent = document.getElementById('docDate').value;
            document.getElementById('docTimeDisplay').textContent = document.getElementById('docTime').value;
            document.getElementById('docLocationDisplay').textContent = document.getElementById('docLocation').value;
        }

        // Update display fields when inputs change
        document.getElementById('docDate').addEventListener('change', updateDisplayFields);
        document.getElementById('docTime').addEventListener('change', updateDisplayFields);
        document.getElementById('docLocation').addEventListener('change', updateDisplayFields);

        function toggleExportOptions() {
            const modal = document.getElementById('exportModal');
            modal.classList.toggle('hidden');
        }

        function printDocument() {
            // Update display fields before printing
            updateDisplayFields();
            
            // Ensure content is clean for printing
            const editor = document.getElementById('editor');
            const content = editor.innerHTML;
            
            // If it's placeholder content, don't print
            if (content.includes('Start Creating Your Document') || content.includes('Blank Document Ready')) {
                showNotification('Please add content to the document before printing', 'error');
                return;
            }
            
            // Print the document
            window.print();
        }

        function exportToPDF() {
            const title = document.getElementById('docTitle').value || 'Meeting Document';
            const date = document.getElementById('docDate').value;
            const time = document.getElementById('docTime').value;
            const location = document.getElementById('docLocation').value;
            const content = document.getElementById('editor').innerHTML;
            
            // Check if there's actual content
            if (content.includes('Start Creating Your Document') || content.includes('Blank Document Ready')) {
                showNotification('Please add content to the document before exporting', 'error');
                return;
            }
            
            // Create a clean printable version
            const printable = document.createElement('div');
            printable.innerHTML = `
                <div style="font-family: 'Segoe UI', Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 30px; line-height: 1.6;">
                    <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #4F46E5; padding-bottom: 20px;">
                        <h1 style="color: #4F46E5; margin-bottom: 10px; font-size: 28px;">${title}</h1>
                        <div style="display: flex; justify-content: space-between; color: #666; font-size: 14px; margin-top: 15px;">
                            <div><strong>Date:</strong> ${date}</div>
                            <div><strong>Time:</strong> ${time}</div>
                            <div><strong>Location:</strong> ${location}</div>
                        </div>
                    </div>
                    <div style="font-size: 14px; color: #333;">${content}</div>
                    <div style="margin-top: 50px; text-align: center; color: #888; font-size: 11px; border-top: 1px solid #eee; padding-top: 15px;">
                        Generated with AI Meeting Journal on ${new Date().toLocaleDateString()}
                    </div>
                </div>
            `;
            
            // PDF options
            const options = {
                margin: [15, 15, 15, 15],
                filename: `${title.replace(/[^a-zA-Z0-9]/g, '_')}.pdf`,
                image: { type: 'jpeg', quality: 0.95 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    letterRendering: true
                },
                jsPDF: { 
                    unit: 'mm', 
                    format: 'a4', 
                    orientation: 'portrait',
                    compress: true
                }
            };
            
            html2pdf().set(options).from(printable).save().then(() => {
                showNotification('PDF exported successfully!', 'success');
            }).catch(() => {
                showNotification('Error exporting PDF. Please try again.', 'error');
            });
            
            toggleExportOptions();
        }

        function exportToWord() {
            const title = document.getElementById('docTitle').value || 'Meeting Document';
            const date = document.getElementById('docDate').value;
            const time = document.getElementById('docTime').value;
            const location = document.getElementById('docLocation').value;
            const content = document.getElementById('editor').innerHTML;
            
            // Check if there's actual content
            if (content.includes('Start Creating Your Document') || content.includes('Blank Document Ready')) {
                showNotification('Please add content to the document before exporting', 'error');
                return;
            }
            
            // Convert HTML to clean text for Word
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = content;
            const textContent = tempDiv.innerText || tempDiv.textContent;
            
            // Create Word document HTML
            const wordHTML = `
                <html xmlns:o="urn:schemas-microsoft-com:office:office" 
                      xmlns:w="urn:schemas-microsoft-com:office:word" 
                      xmlns="http://www.w3.org/TR/REC-html40">
                <head>
                    <meta charset="utf-8">
                    <title>${title}</title>
                    <xml>
                        <w:WordDocument>
                            <w:View>Print</w:View>
                            <w:Zoom>90</w:Zoom>
                            <w:DoNotPromptForConvert/>
                            <w:DoNotShowHTMLMenuAndToolbar/>
                        </w:WordDocument>
                    </xml>
                    <style>
                        @page {
                            size: 8.5in 11in;
                            margin: 1in;
                        }
                        body {
                            font-family: 'Calibri', 'Arial', sans-serif;
                            font-size: 11pt;
                            line-height: 1.6;
                            color: #333;
                        }
                        h1 {
                            color: #4F46E5;
                            font-size: 24pt;
                            text-align: center;
                            margin-bottom: 12pt;
                            border-bottom: 2pt solid #4F46E5;
                            padding-bottom: 6pt;
                        }
                        h2 {
                            color: #4F46E5;
                            font-size: 16pt;
                            margin-top: 18pt;
                            margin-bottom: 6pt;
                        }
                        h3 {
                            color: #333;
                            font-size: 14pt;
                            margin-top: 12pt;
                            margin-bottom: 6pt;
                        }
                        .header-info {
                            text-align: center;
                            margin-bottom: 24pt;
                            padding-bottom: 12pt;
                            border-bottom: 1pt solid #ccc;
                        }
                        .footer {
                            color: #888;
                            font-size: 9pt;
                            text-align: center;
                            margin-top: 36pt;
                            border-top: 1pt solid #eee;
                            padding-top: 12pt;
                        }
                        table {
                            border-collapse: collapse;
                            width: 100%;
                            margin: 12pt 0;
                        }
                        th, td {
                            border: 1pt solid #ccc;
                            padding: 6pt;
                            text-align: left;
                        }
                        th {
                            background-color: #f3f4f6;
                            font-weight: bold;
                        }
                        ul, ol {
                            margin: 6pt 0;
                        }
                        li {
                            margin-bottom: 3pt;
                        }
                    </style>
                </head>
                <body>
                    <h1>${title}</h1>
                    <div class="header-info">
                        <strong>Date:</strong> ${date} &nbsp;&nbsp;&nbsp;
                        <strong>Time:</strong> ${time} &nbsp;&nbsp;&nbsp;
                        <strong>Location:</strong> ${location}
                    </div>
                    <div>${content}</div>
                    <div class="footer">
                        Generated with AI Meeting Journal on ${new Date().toLocaleDateString()}
                    </div>
                </body>
                </html>
            `;
            
            // Create and download blob
            const blob = new Blob([wordHTML], {
                type: 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            });
            
            saveAs(blob, `${title.replace(/[^a-zA-Z0-9]/g, '_')}.doc`);
            
            toggleExportOptions();
            showNotification('Word document exported successfully!', 'success');
        }

        // Initialize
        updateWordCount();
        updateDisplayFields();
    </script>
@endsection 
                                