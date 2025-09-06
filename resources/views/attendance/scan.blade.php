@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="mx-auto w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Mark Attendance</h1>
            <p class="text-gray-600 mt-2">{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</p>
            <div class="mt-2 text-sm text-gray-500">
                Current Time: <span id="current-time" class="font-semibold"></span>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <ul class="text-red-800 space-y-1">
                    @foreach($errors->all() as $error)
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            {{ $error }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Attendance Form -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
            <form action="{{ route('attendance.process-scan') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="attendance_date" value="{{ $date }}">
                
                <!-- Employee Name -->
                <div>
                    <label for="employee_name" class="block text-sm font-medium text-gray-700 mb-2">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="employee_name" 
                           id="employee_name" 
                           required
                           value="{{ old('employee_name') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_name') border-red-500 @enderror"
                           placeholder="Enter your full name">
                </div>

                <!-- Employee ID -->
                <div>
                    <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Employee ID <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           name="employee_id" 
                           id="employee_id" 
                           required
                           value="{{ old('employee_id') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('employee_id') border-red-500 @enderror"
                           placeholder="Enter your employee ID">
                </div>

                <!-- Department (Optional) -->
                <div>
                    <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                        Department 
                    </label>
                    <input type="text" 
                           name="department" 
                           id="department" 
                           value="{{ old('department') }}"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter your department">
                </div>

                <!-- Selfie Upload Section -->
                <div>
                    <label for="selfie_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Verification Photo 
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center bg-gray-50">
                        <div class="py-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <label for="selfie_image" class="cursor-pointer">
                                <span class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                                    📷 Take/Upload Photo (Optional)
                                </span>
                                <input type="file" 
                                       name="selfie_image" 
                                       id="selfie_image" 
                                       accept="image/*" 
                                       capture="user" 
                                       class="hidden">
                            </label>
                        </div>
                        
                        <!-- Preview -->
                        <div id="image-preview" class="hidden mt-4">
                            <img id="preview-image" 
                                 src="" 
                                 alt="Photo preview" 
                                 class="w-full max-w-sm mx-auto rounded-lg border border-gray-300">
                            <p class="text-xs text-green-600 mt-2">✅ Image selected successfully</p>
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Optional: You can take a photo for verification purposes.</p>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea name="notes" 
                              id="notes" 
                              rows="3"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Mark Attendance
                </button>
            </form>
        </div>

        <!-- Time Information -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <h3 class="font-medium text-gray-900 mb-3">Attendance Guidelines</h3>
            <div class="space-y-2 text-sm text-gray-600">
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                    <span><strong>On Time:</strong> 7:30 AM - 8:15 AM</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                    <span><strong>Late:</strong> After 8:15 AM</span>
                </div>
                <div class="flex items-center">
                    <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                    <span><strong>Early:</strong> Before 7:30 AM</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update time every second
    updateTime();
    setInterval(updateTime, 1000);
    
    // Focus on first input
    document.getElementById('employee_name').focus();
    
    // Image preview functionality
    const fileInput = document.getElementById('selfie_image');
    const preview = document.getElementById('image-preview');
    const previewImage = document.getElementById('preview-image');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        } else {
            preview.classList.add('hidden');
        }
    });
});

function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour12: true,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        timeZone: 'Asia/Manila'
    });
    document.getElementById('current-time').textContent = timeString;
}
</script>
@endsection