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
/* ===== NAVBAR CUSTOM THEME ===== */
.custom-navbar {
    background-color: #2f4f4f !important; /* Slate Gray */
    color: #a8e6cf !important; /* Mint */
    position: relative;
}

.custom-navbar .navbar-brand {
    color: #a8e6cf !important;
    font-weight: 600;
    letter-spacing: 0.3px;
}

/* ===== NAV LINK (normal menu) ===== */
.custom-navbar .navbar-nav .nav-link {
    color: #a8e6cf !important;
    font-weight: 500;
    margin-right: 10px;
    position: relative;
    padding-bottom: 4px;
    transition: color 0.25s ease;
}

/* Underline animasi untuk nav biasa */
.custom-navbar .navbar-nav .nav-link:not(.dropdown-toggle)::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    height: 2px;
    width: 0%;
    background-color: #a8e6cf;
    transition: width 0.3s ease;
}

/* Efek hover dan active untuk nav biasa */
.custom-navbar .navbar-nav .nav-link:not(.dropdown-toggle):hover::after,
.custom-navbar .navbar-nav .nav-link:not(.dropdown-toggle).active::after {
    width: 100%;
}

.custom-navbar .navbar-nav .nav-link:hover {
    color: #d0f0e0 !important;
}

.custom-navbar .navbar-nav .nav-link.active {
    color: #ffffff !important;
}

/* ===== DROPDOWN (USER) ===== */
.custom-navbar .dropdown-toggle {
    color: #a8e6cf !important;
    font-weight: 500;
    position: relative;
    border: none;
    background: transparent;
    padding-bottom: 4px;
}

/* Hapus ikon caret bawaan Bootstrap */
.custom-navbar .dropdown-toggle::after {
    display: none !important;
}

/* Underline langsung muncul saat aktif (tanpa animasi) */
.custom-navbar .dropdown-toggle.active::before,
.custom-navbar .dropdown-toggle.show::before {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    height: 2px;
    width: 100%;
    background-color: #ffffff; /* putih */
}

/* Hover tanpa animasi */
.custom-navbar .dropdown-toggle:hover,
.custom-navbar .dropdown-toggle:focus {
    color: #ffffff !important;
}

/* Dropdown menu styling */
.custom-navbar .dropdown-menu {
    background-color: #a8e6cf;
    border: none;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.custom-navbar .dropdown-item:hover {
    background-color: #2f4f4f;
    color: #ffffff;
}
/* Dropdown menu styling */
.custom-navbar .dropdown-menu {
    background-color: #a8e6cf; /* Mint */
    border: none;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.custom-navbar .dropdown-item:hover {
    background-color: #2f4f4f; /* Slate Gray hover */
    color: #ffffff;
}

</style>
