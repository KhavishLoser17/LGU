@extends('layouts.app')
@section('title', 'AI Meeting Journal - Intelligent Document Processing & Export')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <i class="fas fa-archive text-white text-2xl"></i>
                </div>
                <h1 class="ml-3 text-2xl font-bold text-gray-900">Documents Archiving System</h1>
            </div>
            <div class="flex items-center">
                <button onclick="toggleExportOptions()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-all">
                    <i class="fas fa-file-export"></i>
                    <span>Export Options</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Export Options Modal -->
    <div id="exportModal" class="fixed inset-0 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-800">Export Documents</h3>
                <button onclick="toggleExportOptions()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-4">
                <div onclick="exportToPDF()" class="export-option cursor-pointer flex items-center p-4 border border-gray-500 rounded-xl hover:bg-blue-50 transition-all">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">Export to PDF</div>
                        <div class="text-sm text-gray-500">High-quality document for printing</div>
                    </div>
                </div>
                <div onclick="exportToWord()" class="export-option cursor-pointer flex items-center p-4 border border-gray-500 rounded-xl hover:bg-blue-50 transition-all">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-file-word text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">Export to Word</div>
                        <div class="text-sm text-gray-500">Editable document for Microsoft Word</div>
                    </div>
                </div>
                <div onclick="exportToExcel()" class="export-option cursor-pointer flex items-center p-4 border border-gray-500 rounded-xl hover:bg-blue-50 transition-all">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-file-excel text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-gray-800">Export to Excel</div>
                        <div class="text-sm text-gray-500">Spreadsheet format for data analysis</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 flex items-center justify-center z-50 hidden border border-gray-900 border-opacity-50 rounded-lg shadow-lg">
        <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-800">Upload Document</h3>
                <button onclick="hideUploadModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Form for document upload -->
            <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Document Name</label>
                    <input type="text" name="name" id="docName" class="w-full px-3 py-2 border border-gray-500 rounded-md" placeholder="Enter document name" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select name="category" id="docCategory" class="w-full px-3 py-2 border border-gray-500 rounded-md" required>
                        <option value="">Select Category</option>
                        <option value="Financial">Financial</option>
                        <option value="Legal">Legal</option>
                        <option value="HR">HR</option>
                        <option value="Projects">Projects</option>
                        <option value="Contracts">Contracts</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="docDescription" class="w-full px-3 py-2 border border-gray-500 rounded-md" placeholder="Enter document description" rows="3"></textarea>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload File</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-500 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <div class="flex text-sm text-gray-600">
                                <label for="document" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                    <span>Upload a file</span>
                                    <input id="document" name="document" type="file" class="sr-only" required>
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, XLSX up to 10MB</p>
                        </div>
                        </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">
                    Upload Document
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="flex-1 min-w-0">
                    <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">All Documents</h2>
                    <p class="mt-1 text-sm text-gray-500">Browse, filter and manage your archived documents</p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <button type="button" onclick="showUploadModal()" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-plus mr-2"></i> Upload Document
                    </button>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Error!</strong>
                <ul class="mt-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Filters -->
            <div class="bg-white p-6 rounded-lg shadow mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Name</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-file text-gray-400"></i>
                            </div>
                            <input type="text" id="filterName" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Document name...">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Date</label>
                        <div class="flex space-x-4">
                            <div class="relative rounded-md shadow-sm flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="date" id="filterDateFrom" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md p-2 border">
                            </div>
                            <span class="self-center text-gray-400">to</span>
                            <div class="relative rounded-md shadow-sm flex-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-calendar text-gray-400"></i>
                                </div>
                                <input type="date" id="filterDateTo" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md p-2 border">
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Category</label>
                        <select id="filterCategory" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Categories</option>
                            <option value="Financial">Financial</option>
                            <option value="Legal">Legal</option>
                            <option value="HR">HR</option>
                            <option value="Projects">Projects</option>
                            <option value="Contracts">Contracts</option>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="button" id="resetFilters" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Reset
                    </button>
                    <button type="button" id="applyFilters" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Apply Filters
                    </button>
                </div>
            </div>

            <!-- Documents Grid -->
            <div class="bg-white shadow rounded-lg overflow-hidden">
                <div class="flex flex-col">
                    <div class="overflow-x-auto">
                        <div class="align-middle inline-block min-w-full">
                            <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Category
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Date Added
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Size
                                            </th>
                                            <th scope="col" class="relative px-6 py-3">
                                                <span class="sr-only">Actions</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200" id="documentsTableBody">
                                        @forelse($documents as $document)
                                        <tr class="document-row">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center">
                                                        <i class="fas fa-file-pdf text-blue-600"></i>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $document->name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $document->file_type }} Document</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $document->category }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $document->created_at->format('M d, Y') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $document->file_size }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></a>
                                                <a href="{{ Storage::url($document->file_path) }}" download class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-download"></i></a>
                                                <form action="{{ route('documents.destroy', $document->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this document?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr id="noDocumentsRow">
                                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                                No documents found. Upload your first document to get started.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden mt-8">
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">No documents found</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Try adjusting your filters to find what you're looking for.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($documents->count() > 0)
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-8 rounded-lg shadow">
                <div class="flex-1 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ ($documents->currentPage() - 1) * $documents->perPage() + 1 }}</span>
                            to
                            <span class="font-medium">{{ min($documents->currentPage() * $documents->perPage(), $documents->total()) }}</span>
                            of
                            <span class="font-medium">{{ $documents->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            {{-- Previous Page Link --}}
                            @if ($documents->onFirstPage())
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $documents->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif

                            {{-- Pagination Elements --}}
                            @foreach ($documents->getUrlRange(1, $documents->lastPage()) as $page => $url)
                                @if ($page == $documents->currentPage())
                                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-600 text-sm font-medium text-white">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach

                            {{-- Next Page Link --}}
                            @if ($documents->hasMorePages())
                                <a href="{{ $documents->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-300">
                                    <span class="sr-only">Next</span>
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif
                        </nav>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
    // Store original documents for filtering
    const originalDocuments = @json($documents->items());
    
    // Format date to display as "Oct 15, 2023"
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }

    // Toggle export options modal
    function toggleExportOptions() {
        const modal = document.getElementById('exportModal');
        modal.classList.toggle('hidden');
    }

    // Show upload modal
    function showUploadModal() {
        const modal = document.getElementById('uploadModal');
        modal.classList.remove('hidden');
    }

    // Hide upload modal
    function hideUploadModal() {
        const modal = document.getElementById('uploadModal');
        modal.classList.add('hidden');
    }

    // Export to PDF function
    function exportToPDF() {
        // Create a printable version of the document list
        const content = document.querySelector('.min-w-full').outerHTML;
        
        const printable = document.createElement('div');
        printable.innerHTML = `
            <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
                <h1 style="text-align: center; color: #4F46E5; margin-bottom: 10px;">Document Archive Report</h1>
                <div style="text-align: center; color: #777; margin-bottom: 30px;">
                    Generated on ${new Date().toLocaleDateString()}
                </div>
                ${content}
            </div>
        `;
        
        // Create PDF
        const options = {
            margin: 10,
            filename: `document_archive_${new Date().toISOString().split('T')[0]}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        
        html2pdf().set(options).from(printable).save();
        
        toggleExportOptions();
        alert('PDF exported successfully!');
    }

    // Export to Word function
    function exportToWord() {
        const title = "Document Archive Report";
        const date = new Date().toLocaleDateString();
        
        // Create a simple Word document
        const header = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" 
                  xmlns:w="urn:schemas-microsoft-com:office:word" 
                  xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="utf-8">
                <title>${title}</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    h1 { color: #4F46E5; text-align: center; }
                    .footer { color: #777; font-size: 12px; text-align: center; margin-top: 40px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f9fafb; }
                </style>
            </head>
            <body>
                <h1>${title}</h1>
                <div style="text-align: center; color: #777; margin-bottom: 30px;">
                    Generated on ${date}
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Date Added</th>
                            <th>Size</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $document)
                        <tr>
                            <td>{{ $document->name }}</td>
                            <td>{{ $document->category }}</td>
                            <td>{{ $document->created_at->format('M d, Y') }}</td>
                            <td>{{ $document->file_size }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="footer">
                    Generated with Document Archiving System on ${date}
                </div>
            </body>
            </html>
        `;
        
        // Convert to blob and download
        const blob = new Blob([header], {type: 'application/msword'});
        saveAs(blob, `document_archive_${new Date().toISOString().split('T')[0]}.doc`);
        
        toggleExportOptions();
        alert('Word document exported successfully!');
    }

    // Export to Excel function
    function exportToExcel() {
        // Create a simple CSV content
        let csvContent = "data:text/csv;charset=utf-8,Name,Category,Date Added,Size\n";
        
        // Add document data
        @foreach($documents as $document)
        csvContent += "{{ $document->name }},{{ $document->category }},{{ $document->created_at->format('M d, Y') }},{{ $document->file_size }}\n";
        @endforeach
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", `document_archive_${new Date().toISOString().split('T')[0]}.csv`);
        document.body.appendChild(link);
        
        link.click();
        
        toggleExportOptions();
        alert('Excel file exported successfully!');
    }

    // Filter documents based on criteria
    function filterDocuments() {
        const nameFilter = document.getElementById('filterName').value.toLowerCase();
        const dateFrom = document.getElementById('filterDateFrom').value;
        const dateTo = document.getElementById('filterDateTo').value;
        const categoryFilter = document.getElementById('filterCategory').value;
        
        const filteredDocs = originalDocuments.filter(doc => {
            // Filter by name
            if (nameFilter && !doc.name.toLowerCase().includes(nameFilter)) {
                return false;
            }
            
            // Filter by category
            if (categoryFilter && doc.category !== categoryFilter) {
                return false;
            }
            
            // Filter by date range
            const docDate = new Date(doc.created_at).toISOString().split('T')[0];
            if (dateFrom && docDate < dateFrom) {
                return false;
            }
            
            if (dateTo && docDate > dateTo) {
                return false;
            }
            
            return true;
        });
        
        renderFilteredDocuments(filteredDocs);
    }

    // Render filtered documents to the table
    function renderFilteredDocuments(docs) {
        const tableBody = document.getElementById('documentsTableBody');
        const noResults = document.getElementById('noResults');
        const noDocumentsRow = document.getElementById('noDocumentsRow');
        
        // Clear current table content
        tableBody.innerHTML = '';
        
        if (docs.length === 0) {
            // Show no results message
            noResults.classList.remove('hidden');
            if (noDocumentsRow) noDocumentsRow.classList.add('hidden');
            return;
        }
        
        // Hide no results message
        noResults.classList.add('hidden');
        if (noDocumentsRow) noDocumentsRow.classList.add('hidden');
        
        // Add documents to table
        docs.forEach(doc => {
            const row = document.createElement('tr');
            row.className = 'document-row';
            
            // Determine icon based on file type
            let iconClass = 'fa-file';
            let iconColor = 'text-gray-600';
            let bgColor = 'bg-gray-100';
            
            if (doc.file_type && doc.file_type.toLowerCase() === 'pdf') {
                iconClass = 'fa-file-pdf';
                iconColor = 'text-red-600';
                bgColor = 'bg-red-100';
            } else if (doc.file_type && (doc.file_type.toLowerCase() === 'doc' || doc.file_type.toLowerCase() === 'docx')) {
                iconClass = 'fa-file-word';
                iconColor = 'text-blue-600';
                bgColor = 'bg-blue-100';
            } else if (doc.file_type && (doc.file_type.toLowerCase() === 'xls' || doc.file_type.toLowerCase() === 'xlsx')) {
                iconClass = 'fa-file-excel';
                iconColor = 'text-green-600';
                bgColor = 'bg-green-100';
            } else if (doc.file_type && (doc.file_type.toLowerCase() === 'jpg' || doc.file_type.toLowerCase() === 'jpeg' || doc.file_type.toLowerCase() === 'png')) {
                iconClass = 'fa-file-image';
                iconColor = 'text-purple-600';
                bgColor = 'bg-purple-100';
            }
            
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 ${bgColor} rounded-md flex items-center justify-center">
                            <i class="fas ${iconClass} ${iconColor}"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${doc.name}</div>
                            <div class="text-sm text-gray-500">${doc.file_type} Document</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${doc.category}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${formatDate(doc.created_at)}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${doc.file_size}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="/storage/${doc.file_path}" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></a>
                    <a href="/storage/${doc.file_path}" download class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-download"></i></a>
                    <form action="/documents/${doc.id}" method="POST" class="inline-block">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this document?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            `;
            tableBody.appendChild(row);
        });
    }

    // Reset all filters
    function resetFilters() {
        document.getElementById('filterName').value = '';
        document.getElementById('filterDateFrom').value = '';
        document.getElementById('filterDateTo').value = '';
        document.getElementById('filterCategory').value = '';
        
        // Show all documents again
        const noResults = document.getElementById('noResults');
        const noDocumentsRow = document.getElementById('noDocumentsRow');
        
        noResults.classList.add('hidden');
        if (noDocumentsRow) noDocumentsRow.classList.remove('hidden');
        
        // Re-render all documents
        const tableBody = document.getElementById('documentsTableBody');
        tableBody.innerHTML = '';
        
        @forelse($documents as $document)
        tableBody.innerHTML += `
            <tr class="document-row">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-md flex items-center justify-center">
                            <i class="fas fa-file-pdf text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $document->name }}</div>
                            <div class="text-sm text-gray-500">{{ $document->file_type }} Document</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $document->category }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $document->created_at->format('M d, Y') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $document->file_size }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-eye"></i></a>
                    <a href="{{ Storage::url($document->file_path) }}" download class="text-blue-600 hover:text-blue-900 mr-3"><i class="fas fa-download"></i></a>
                    <form action="{{ route('documents.destroy', $document->id) }}" method="POST" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this document?')">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
        `;
        @empty
        tableBody.innerHTML += `
            <tr id="noDocumentsRow">
                <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                    No documents found. Upload your first document to get started.
                </td>
            </tr>
        `;
        @endforelse
    }

    // Initialize the page
    document.addEventListener('DOMContentLoaded', function() {
        // Set up event listeners
        document.getElementById('applyFilters').addEventListener('click', filterDocuments);
        document.getElementById('resetFilters').addEventListener('click', resetFilters);
        
        // Add event listeners for real-time filtering on input change
        document.getElementById('filterName').addEventListener('input', filterDocuments);
        document.getElementById('filterDateFrom').addEventListener('change', filterDocuments);
        document.getElementById('filterDateTo').addEventListener('change', filterDocuments);
        document.getElementById('filterCategory').addEventListener('change', filterDocuments);
    });
</script>
@endsection