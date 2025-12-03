<nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">

            {{-- Logo / Titel --}}
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="text-xl font-semibold text-green-700 tracking-tight">
                    NatuurMoment
                </a>
            </div>

            {{-- Desktop menu --}}
            <div class="hidden sm:flex items-center space-x-6">
                <a href="{{ route('home') }}"
                   class="text-gray-700 hover:text-green-700 text-sm font-medium">
                    Home
                </a>

                @auth
                    <a href="{{ route('dashboard') }}"
                       class="text-gray-700 hover:text-green-700 text-sm font-medium">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-gray-700 hover:text-green-700 text-sm font-medium">
                        Login
                    </a>
                @endauth
            </div>

            {{-- Mobile hamburger menu --}}
            <div class="flex items-center sm:hidden">
                <button id="nav-toggle"
                        class="p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none">
                    <svg id="nav-icon" class="h-6 w-6" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- Mobile menu panel --}}
    <div id="mobile-menu" class="sm:hidden hidden border-t border-gray-200 bg-white">
        <div class="px-4 py-3 space-y-3">

            <a href="{{ route('home') }}"
               class="block text-gray-700 hover:text-green-700 text-sm">
                Home
            </a>

            @auth
                <a href="{{ route('dashboard') }}"
                   class="block text-gray-700 hover:text-green-700 text-sm">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="block text-gray-700 hover:text-green-700 text-sm">
                    Login
                </a>
            @endauth

        </div>
    </div>

    <script>
        document.getElementById('nav-toggle').addEventListener('click', () => {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        });
    </script>
</nav>

{{-- Push content down (nav is fixed) --}}
<div class="h-16"></div>
