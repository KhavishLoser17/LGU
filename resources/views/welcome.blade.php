{{-- resources/views/landing/page.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>District Meetings - Public Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card-hover {
            transition: all 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">District Meetings</h1>
                        <p class="text-sm text-gray-500">Public Portal</p>
                    </div>
                </div>
                <nav class="hidden md:flex items-center space-x-6">
                    <a href="#meetings" class="text-gray-700 hover:text-blue-600 transition-colors">Meetings</a>
                    <a href="#about" class="text-gray-700 hover:text-blue-600 transition-colors">About</a>
                    <a href="#contact" class="text-gray-700 hover:text-blue-600 transition-colors">Contact</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-gradient text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-4xl font-bold mb-4">Upcoming District Meetings</h2>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Stay informed about upcoming district meetings, agendas, and important announcements
            </p>
            <div class="flex items-center justify-center space-x-4 text-blue-100">
                <div class="flex items-center">
                    <i class="fas fa-calendar-check mr-2"></i>
                    <span>{{ $publishedMeetings->count() }}</span> Published Meetings
                </div>
                <div class="w-1 h-6 bg-blue-300"></div>
                <div class="flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    All Districts
                </div>
            </div>
        </div>
    </section>

    <!-- Published Meetings Section -->
    <section id="meetings" class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h3 class="text-3xl font-bold text-gray-900 mb-4">Published Meetings</h3>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    Browse all published meeting agendas and information. Click on any meeting to view detailed agenda items.
                </p>
            </div>

            @if($publishedMeetings->count() > 0)
                <!-- Meeting Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($publishedMeetings as $meeting)
                        <div class="bg-white rounded-xl shadow-lg border card-hover">
                            <div class="relative">
                                @if($meeting['image_url'])
                                    <img src="{{ $meeting['image_url'] }}" alt="{{ $meeting['title'] }}" 
                                         class="h-48 w-full object-cover rounded-t-xl">
                                @else
                                    <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 rounded-t-xl flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-white text-4xl"></i>
                                    </div>
                                @endif
                                <div class="absolute top-4 right-4">
                                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                        <i class="fas fa-globe mr-1"></i>
                                        Live
                                    </span>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="mb-4">
                                    <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $meeting['title'] }}</h4>
                                    <p class="text-gray-600 text-sm mb-3">{{ $meeting['description'] }}</p>
                                </div>
                                
                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-map-marker-alt w-4 mr-3"></i>
                                        <span>{{ $meeting['district'] }}</span>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-calendar w-4 mr-3"></i>
                                        <span>{{ $meeting['meeting_date'] }}</span>
                                    </div>
                                    @if($meeting['meeting_time'])
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-clock w-4 mr-3"></i>
                                            <span>{{ $meeting['meeting_time'] }}</span>
                                        </div>
                                    @endif
                                    @if($meeting['agenda_leader'])
                                        <div class="flex items-center text-sm text-gray-500">
                                            <i class="fas fa-user w-4 mr-3"></i>
                                            <span>{{ $meeting['agenda_leader'] }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                        <span class="text-sm text-gray-600">Published</span>
                                    </div>
                                    <button onclick="viewMeetingDetails({{ $meeting['id'] }})" 
                                            class="flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-calendar-times text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">No Published Meetings</h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-8">
                        There are currently no published meetings available. Please check back later for updates.
                    </p>
                    <div class="flex items-center justify-center space-x-4">
                        <button onclick="window.location.reload()" 
                                class="flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-refresh mr-2"></i>
                            Refresh Page
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto text-center">
                <h3 class="text-3xl font-bold text-gray-900 mb-6">About District Meetings</h3>
                <p class="text-lg text-gray-600 mb-8">
                    Our district meetings serve as a platform for community engagement, decision-making, and information sharing. 
                    All published meetings are open to the public and provide transparency in our administrative processes.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Community Focused</h4>
                        <p class="text-gray-600">Meetings designed to serve community needs and interests</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-eye text-green-600 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Transparent</h4>
                        <p class="text-gray-600">Open processes with published agendas and outcomes</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clock text-purple-600 text-xl"></i>
                        </div>
                        <h4 class="font-semibold text-gray-900 mb-2">Timely Updates</h4>
                        <p class="text-gray-600">Regular meetings with up-to-date information and agendas</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <div class="flex items-center justify-center space-x-3 mb-4">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                    <h4 class="text-lg font-semibold">District Meetings Portal</h4>
                </div>
                <p class="text-gray-400 mb-4">Connecting communities through transparent governance</p>
                <p class="text-sm text-gray-500">© 2025 District Meetings. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Meeting Details Modal -->
    <div id="meetingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Meeting Details</h3>
                    <button onclick="closeMeetingModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="meetingDetails">
                    <p class="text-gray-600">Loading meeting details...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewMeetingDetails(meetingId) {
            document.getElementById('meetingModal').classList.remove('hidden');
            document.getElementById('meetingModal').classList.add('flex');
            
            // Simulate loading meeting details
            document.getElementById('meetingDetails').innerHTML = `
                <div class="space-y-4">
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h4 class="font-semibold text-gray-900">Meeting Information</h4>
                        <p class="text-gray-600">Detailed agenda and information for Meeting ID: ${meetingId}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h5 class="font-medium text-gray-900 mb-2">Agenda Items</h5>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li>• Opening remarks and introductions</li>
                            <li>• Review of previous meeting minutes</li>
                            <li>• Main discussion topics</li>
                            <li>• Community Q&A session</li>
                            <li>• Closing remarks</li>
                        </ul>
                    </div>
                    <div class="text-sm text-gray-500">
                        <p><strong>Note:</strong> This is a demo view. In production, this would show actual meeting agenda items and details from the database.</p>
                    </div>
                </div>
            `;
        }

        function closeMeetingModal() {
            document.getElementById('meetingModal').classList.add('hidden');
            document.getElementById('meetingModal').classList.remove('flex');
        }

        // Close modal when clicking outside
        document.getElementById('meetingModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeMeetingModal();
            }
        });
    </script>
</body>
</html>