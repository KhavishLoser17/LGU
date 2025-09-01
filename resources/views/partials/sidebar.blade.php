<aside id="sidebar"
  class="fixed lg:static inset-y-0 left-0 z-40 w-68 bg-blue-900 text-white flex flex-col shadow-xl
         transform -translate-x-full lg:translate-x-0 transition-transform duration-300
         h-[calc(100vh-60px)] overflow-y-auto">
    <div class="bg-blue-950 p-4 border-b border-white border-opacity-15">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-3 rounded-full overflow-hidden border-2 border-white">
                <img src="{{ asset('images/default-avatar.jpg') }}" alt="User" class="w-full h-full object-cover">
            </div>
            <div class="font-bold text-white">
                {{ auth()->user()->name ?? 'Guest' }}
            </div>
        </div>
    </div>

    <!-- Navigation -->
   <nav class="flex-1 p-2 overflow-y-auto">

    <!-- Dashboard -->
    <div class="nav-group open mb-1 bg-grey-100 bg-opacity-5 rounded-lg overflow-hidden">
        <button class="nav-group-toggle w-full text-left p-3 flex items-center gap-3 font-semibold hover:bg-grey-100 hover:bg-opacity-10 transition-colors">
            <span class="w-5 flex justify-center">
                <i class="fa-solid fa-gauge"></i>
            </span>
            Dashboard
        </button>
        <ul class="nav-sublist pl-8 pb-2 overflow-hidden transition-all duration-300" style="max-height: 200px;">
            <li>
                <a href=""
                   class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('dashboard') ? 'bg-white text-blue-900 font-bold' : '' }}">
                    Overview
                </a>
            </li>
        </ul>
    </div>

    <!-- Agenda & Resolution Tracking -->
    <div class="nav-group mb-2 bg-grey-100 bg-opacity-5 rounded-lg overflow-hidden">
        <button class="nav-group-toggle w-full text-left p-3 flex items-center gap-3 font-semibold hover:bg-grey-100 hover:bg-opacity-10 transition-colors">
            <span class="w-5 flex justify-center">
                <i class="fa-solid fa-gavel"></i>
            </span>
            Agenda & Resolution
            <i class="fa-solid fa-chevron-down caret ml-auto transition-transform duration-200"></i>
        </button>
        <ul class="nav-sublist pl-8 pb-2 max-h-0 overflow-hidden transition-all duration-300">
            <li>
                <a href="{{route('agenda.manage')}}"
                   class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('ordinance.draft-creation') ? 'bg-white text-blue-900 font-bold' : '' }}">
                    Draft Creation & Editing
                </a>
            </li>
            <li>
                <a href="{{route('minutes.status')}}"
                   class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('ordinance.status-tracking') ? 'bg-white text-blue-900 font-bold' : '' }}">
                    Status Tracking
                </a>
            </li>
        </ul>
    </div>

    <!-- Attendance & Monitoring -->
        <div class="nav-group mb-2 bg-grey-100 bg-opacity-5 rounded-lg overflow-hidden">
            <button class="nav-group-toggle w-full text-left p-3 flex items-center gap-3 font-semibold hover:bg-grey-100 hover:bg-opacity-10 transition-colors">
                <span class="w-5 flex justify-center">
                 <i class="fa-solid fa-handshake-angle"></i>
                </span>
                Attendance & Monitoring
                <i class="fa-solid fa-chevron-down caret ml-auto transition-transform duration-200"></i>
            </button>
            <ul class="nav-sublist pl-8 pb-2 max-h-0 overflow-hidden transition-all duration-300">
                <li>
                    <a href="{{route('attendance.index')}}"
                    class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('session.attendance') ? 'bg-white text-blue-900 font-bold' : '' }}">
                        Attendance Monitoring
                    </a>
                </li>
                <li>
                    <a href="{{route('attendance.records')}}"
                    class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('session.rollcall') ? 'bg-white text-blue-900 font-bold' : '' }}">
                        Roll Call Management
                    </a>
                </li>
                <li>
                    <a href=""
                    class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('session.analytics') ? 'bg-white text-blue-900 font-bold' : '' }}">
                        Session Analytics
                    </a>
                </li>
            </ul>
        </div>

        <!-- Minutes of Session -->
        <div class="nav-group mb-2 bg-grey-100 bg-opacity-5 rounded-lg overflow-hidden">
            <button class="nav-group-toggle w-full text-left p-3 flex items-center gap-3 font-semibold hover:bg-grey-100 hover:bg-opacity-10 transition-colors">
                <span class="w-5 flex justify-center">
                    <i class="fa-solid fa-folder-open"></i>
                </span>
                Minutes of Session
                <i class="fa-solid fa-chevron-down caret ml-auto transition-transform duration-200"></i>
            </button>
            <ul class="nav-sublist pl-8 pb-2 max-h-0 overflow-hidden transition-all duration-300">
                <li>
                    <a href=""
                    class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('session.minutes') ? 'bg-white text-blue-900 font-bold' : '' }}">
                        Minutes Encoding
                    </a>
                </li>
                <li>
                    <a href=""
                    class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('records.cataloging') ? 'bg-white text-blue-900 font-bold' : '' }}">
                        Records Cataloging
                    </a>
                </li>
                <li>
                    <a href=""
                    class="block py-2 px-2 rounded text-white hover:bg-grey-100 hover:bg-opacity-20 transition-colors {{ request()->routeIs('records.documents') ? 'bg-white text-blue-900 font-bold' : '' }}">
                        Document Archive
                    </a>
                </li>
            </ul>
        </div>

    </nav>

</aside>
