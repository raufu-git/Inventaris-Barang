<nav class="navbar navbar-expand-lg custom-navbar border-bottom shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <x-application-logo style="height: 40px; width:auto;" />
        </a>

        <!-- Toggler -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menu -->
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side -->
            <ul class="navbar-nav me-auto">
                @php 
                    $navs = [
                        ['route' => 'dashboard', 'name' => 'Dashboard'],
                        ['route' => 'barang.index', 'name' => 'Barang'],
                        ['route' => 'lokasi.index', 'name' => 'Lokasi'],
                        ['route' => 'kategori.index', 'name' => 'Kategori'],
                        ['route'=>'peminjaman.index','name'=>'Peminjaman'],
                        ['route' => 'user.index', 'name' => 'User', 'role' => 'admin'],
                    ];
                @endphp

                @foreach ($navs as $nav)
                    @php extract($nav); @endphp
                    @if (isset($role))
                        @role($role)
                            <li class="nav-item">
                                <x-nav-link :active="request()->routeIs($route)" :href="route($route)">
                                    {{ __($name) }}
                                </x-nav-link>
                            </li>
                        @endrole
                    @else
                        <li class="nav-item">
                            <x-nav-link :active="request()->routeIs($route)" :href="route($route)">
                                {{ __($name) }}
                            </x-nav-link>
                        </li>
                    @endif
                @endforeach
            </ul>

            <!-- Right Side -->
            <!-- Right Side -->
        <ul class="navbar-nav ms-auto">
            <x-dropdown>
                <x-slot name="trigger">
                    <i data-lucide="user-circle" class="me-1"></i> {{ Auth::user()->name }}
                </x-slot>

                <x-slot name="content">
                    <!-- Profile -->
                    <x-dropdown-link :href="route('profile.edit')">
                        <i data-lucide="user" class="me-2" style="width:16px;height:16px;"></i>
                        {{ __('Profile') }}
                    </x-dropdown-link>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                            <i data-lucide="log-out" class="me-2" style="width:16px;height:16px;"></i>
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </ul>
    </div>
</nav>

<style>
    /* Navbar custom theme - Olive & Sand Brown */
.custom-navbar {
    background-color: #708238 !important; /* Olive green */
    color: #f5f5dc !important; /* Light sand */
}

.custom-navbar .navbar-brand {
    color: #f5f5dc !important;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.custom-navbar .navbar-nav .nav-link {
    color: #f5f5dc !important;
    font-weight: 500;
    margin-right: 10px;
    transition: all 0.2s ease;
}

.custom-navbar .navbar-nav .nav-link:hover {
    color: #d2b48c !important; /* Sand brown hover */
}

.custom-navbar .navbar-nav .nav-link.active {
    color: #fffbe0 !important;
    border-bottom: 2px solid #d2b48c;
}

/* User dropdown */
.custom-navbar .dropdown-toggle {
    color: #f5f5dc !important;
    font-weight: 500;
}

.custom-navbar .dropdown-menu {
    background-color: #f5f5dc;
    border: none;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.custom-navbar .dropdown-item:hover {
    background-color: #d2b48c;
    color: #fff;
}

</style>