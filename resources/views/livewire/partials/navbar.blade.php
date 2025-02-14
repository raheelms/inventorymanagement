<div>
    <!-- content: Logo, Search bar, Sign in, Register -->
    <div class="navbar bg-[#003953] bg-cyan-800 justify-center">

        <!-- Big Devices - First Bar -->
        <div class="w-full max-w-[85rem] flex items-center justify-between lg:flex hidden">
            
            <!-- Left Section, Mobile Menu Button, Logo, Search Bar -->
            <div class="navbar-start flex items-center">
                <!-- Mobile Menu Button -->
                <div aria-label="Mobile Menu Button" wire:click="toggleDrawer" tabindex="0" role="button"
                    class="lg:hidden bg-stone-200 hover:bg-stone-200 btn btn-ghost btn-circle">
                    <svg class="w-6 h-6 fill-current transform -scale-x-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M3 4H21V6H3V4ZM9 11H21V13H9V11ZM3 18H21V20H3V18Z"></path>
                    </svg>                    
                </div>
        
                <!-- Logo -->
                <a href="/" wire:navigate class="text-xl font-bold text-white mx-10">
                    {{ config('app.name') }}
                </a>
                
                <!-- Search Bar -->
                <div>
                    <livewire:partials.search-bar />
                </div>
            </div>
        
            <!-- Right Section, Sign in and Register -->
            <div class="navbar-end flex items-center gap-4">
                <ul class="flex items-center font-medium justify-end w-full">
                    <!-- Dropdown -->
                    @auth('customer')
                    <li class="relative inline-flex">
                        <!-- Dropdown Trigger -->
                        <button id="hs-dropdown-account" type="button"
                            class="peer h-[38px] w-[38px] flex justify-center items-center rounded-full border border-transparent text-gray-800 focus:outline-none dark:text-white"
                            aria-haspopup="menu" aria-expanded="false" aria-label="Dropdown">
                            <img
                                class="shrink-0 h-[38px] w-[38px] rounded-full"
                                src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80"
                                alt="Avatar"/>
                        </button>
            
                        <!-- Dropdown Menu -->
                        <div id="flyout-menu" class="absolute right-0 mt-14 hidden min-w-[240px] bg-white shadow-md rounded-lg dark:bg-neutral-800 dark:border dark:border-neutral-700 peer-focus:block z-50">
                            <div class="py-3 px-6 bg-gray-100 rounded-t-lg dark:bg-neutral-700">
                                <p class="text-sm text-gray-500 dark:text-neutral-500">{{ __('Signed in as') }}</p>
                                <p class="text-sm font-medium text-gray-800 dark:text-neutral-200">
                                    {{ Auth::guard('customer')->user()->name }}
                                </p>
                            </div>
        
                            <div class="p-1.5 space-y-0.5">
                                <a wire:navigate href="{{ url('/dashboard') }}" class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300">
                                    {{ __('Dashboard') }}
                                </a>
                                <a wire:navigate href="{{ url('/profile') }}" class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300">
                                    {{ __('Profile') }}
                                </a>
                                <a wire:navigate href="#" class="flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300">
                                    My Orders
                                </a>
            
                                <!-- Authentication -->
                                <button wire:click="logout" class="w-full text-start flex items-center gap-x-3.5 py-2 px-3 rounded-lg text-sm text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 dark:text-neutral-400 dark:hover:bg-neutral-700 dark:hover:text-neutral-300 dark:focus:bg-neutral-700 dark:focus:text-neutral-300">
                                    {{ __('Log Out') }}
                                </button>
                            </div>
                        </div>
                    </li>
                    @else
                    @if (Route::has('login'))
                    <!-- Sign in User -->
                    <li>
                        <a wire:navigate href="/login" class="flex items-center px-3 py-2 font-semibold text-white bg-white rounded-full bg-opacity-10 group">
                            <span class="mr-2">{{ __('Sign in') }}</span>
                            <svg class="stroke-current" width="10" height="10" stroke-width="2" viewBox="0 0 10 10" aria-hidden="true">
                                <g fill-rule="evenodd">
                                    <path class="transition duration-200 ease-in-out opacity-0 group-hover:opacity-100" d="M0 5h7"></path>
                                    <path class="transition duration-200 ease-in-out opacity-100 group-hover:transform group-hover:translate-x-1" d="M1 1l4 4-4 4"></path>
                                </g>
                            </svg>
                        </a>
                    </li>
                    @endif
            
                    @if (Route::has('register'))
                    <!-- Create an Account User -->
                    <li>
                        <a wire:navigate href="{{ route('register') }}" class="flex items-center px-3 py-2 font-semibold text-white bg-white rounded-full bg-opacity-10 group">
                            <span class="mr-2">{{ __('Create account') }}</span>
                            <svg class="stroke-current" width="10" height="10" stroke-width="2" viewBox="0 0 10 10" aria-hidden="true">
                                <g fill-rule="evenodd">
                                    <path class="transition duration-200 ease-in-out opacity-0 group-hover:opacity-100" d="M0 5h7"></path>
                                    <path class="transition duration-200 ease-in-out opacity-100 group-hover:transform group-hover:translate-x-1" d="M1 1l4 4-4 4"></path>
                                </g>
                            </svg>
                        </a>
                    </li>
                    @endif
                    @endauth
                </ul>
            </div>
        </div>

        <!-- Small Devices - First Bar -->        
        <div class="w-full max-w-[85rem] flex items-center justify-between flex lg:hidden">
            <!-- Left Section: Mobile Menu Button and Logo -->
            <div class="navbar-start flex items-center gap-x-2">
                <!-- Mobile Menu Button -->
                <div aria-label="Mobile Menu Button" wire:click="toggleDrawer" tabindex="0" role="button"
                    class="bg-stone-200 hover:bg-stone-300 btn btn-ghost btn-circle flex items-center justify-center">
                    <svg class="w-6 h-6 fill-current transform -scale-x-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M3 4H21V6H3V4ZM9 11H21V13H9V11ZM3 18H21V20H3V18Z"></path>
                    </svg>
                </div>

                <!-- Logo -->
                <a href="/" wire:navigate class="text-lg font-bold text-white">
                    {{ config('app.name') }}
                </a>
            </div>
        
            <!-- Right Section with Icons for Mobile -->
            <div class="flex items-center gap-4 lg:hidden">
                <!-- Search Button -->
                <button id="mobile-search-button" type="button" class="flex items-center justify-center h-[32px] w-[32px] rounded-full bg-white bg-opacity-10 hover:bg-opacity-20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0z" />
                    </svg>
                </button>
                
                @auth('customer')
                <!-- User Avatar Button -->
                <button id="mobile-user-avatar" type="button"
                    class="h-[32px] w-[32px] flex justify-center items-center rounded-full">
                    <img class="shrink-0 h-[32px] w-[32px] rounded-full"
                        src="https://images.unsplash.com/photo-1568602471122-7832951cc4c5?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=facearea&facepad=2&w=320&h=320&q=80"
                        alt="Avatar"/>
                </button>
                @else
                <!-- Sign In Button with Icon -->
                <a wire:navigate href="/login"
                    class="flex items-center justify-center h-[32px] w-[32px] rounded-full bg-white bg-opacity-10 hover:bg-opacity-20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>                      
                </a>

                <!-- Register Button with Icon 
                <a wire:navigate href="{{ route('register') }}"
                    class="flex items-center justify-center h-[32px] w-[32px] rounded-full bg-white bg-opacity-10 hover:bg-opacity-20">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg>                      
                </a> -->
                @endauth

                <!-- Cart Button -->
                <a wire:navigate href="/cart"
                    class="flex items-center justify-center h-[32px] w-[32px] rounded-full bg-white bg-opacity-10 hover:bg-opacity-20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3 3h2l1.68 8.39a2 2 0 001.99 1.61h8.66a2 2 0 001.98-1.75L21 6H6m9 13a1 1 0 11-2 0 1 1 0 012 0zm-6 0a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                </a>
            </div>

        </div>        
        
        <x-mary-drawer wire:model.persist="responsiveMenu" class="w-11/12 lg:w-1/3 bg-sky-950 text-white">
            <div x-data="{ activeMenu: 'main', menuHistory: [] }" class="relative h-full">
        
                <!-- Main Menu -->
                <div x-show="activeMenu === 'main'"
                        x-transition:enter="transition transform ease-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition transform ease-in duration-300"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="-translate-x-full"
                        class="absolute inset-0 bg-sky-950">

                    <ul class="space-y-4">
                        <li class="w-full text-center">
                            <a href="/" wire:navigate class="items-center text-xl font-bold mb-2 mx-auto">
                                {{ config('app.name') }}
                            </a>
                        </li>
        
                        @if($menu && $menu->menuItems)
                            @foreach($menu->menuItems as $item)
                                <li class="relative px-3 py-2">
                                    @if($item->children->count() > 0)
                                        <!-- Main menu item with sub-children -->
                                        <button @click="menuHistory.push(activeMenu); activeMenu = 'menu-{{ $item->id }}'" class="w-full text-left">
                                            <div class="flex items-center justify-between">
                                                @if($item->icon)
                                                    <div class="p-1 bg-gray-200 rounded-xl">
                                                        @svg($item->icon, 'w-5 h-5 text-sky-950')
                                                    </div>
                                                @endif
                                                <span>{{ $item->title }}</span>
                                                <!-- Add the right arrow for the main menu -->
                                                <svg class="w-4 h-4 transform" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </div>
                                        </button>
                                    @else
                                        <a href="{{ $item->url }}" class="flex items-center gap-x-2">
                                            @if($item->icon)
                                                <div class="p-1 bg-gray-200 rounded-xl">
                                                    @svg($item->icon, 'w-5 h-5 text-sky-950')
                                                </div>
                                            @endif
                                            <span>{{ $item->title }}</span>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
        
                <!-- Submenus (Separate List) -->
                @if($menu && $menu->menuItems)
                    @foreach($menu->menuItems as $item)
                        @if($item->children->count() > 0)
                            <!-- First-level Submenu (Separate Sliding Container) -->
                            <div x-show="activeMenu === 'menu-{{ $item->id }}'"
                                    x-transition:enter="transition transform ease-out duration-200"
                                    x-transition:enter-start="translate-x-full"
                                    x-transition:enter-end="translate-x-0"
                                    x-transition:leave="transition transform ease-in duration-200"
                                    x-transition:leave-start="translate-x-0"
                                    x-transition:leave-end="-translate-x-full"
                                    class="absolute inset-0 bg-sky-950 p-4 z-20">
                                <!-- Back Button and Parent Title -->
                                <div class="flex items-center bg-sky-900 gap-2 px-4 py-3 mb-4">
                                    <button @click="activeMenu = menuHistory.pop()" class="text-white">
                                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 19l-7-7 7-7"/>
                                        </svg>
                                    </button>
                                    <span class="text-md font-semibold">{{ $item->title }}</span>
                                </div>
        
                                <!-- Submenu Items (First-level children) -->
                                <ul class="space-y-4">
                                    @foreach($item->children as $child)
                                        <li class="relative px-3 py-2">
                                            @if($child->children->count() > 0)
                                                <!-- Second-level Submenu (Separate List for sub-children) -->
                                                <button @click="menuHistory.push(activeMenu); activeMenu = 'menu-{{ $child->id }}'" class="w-full text-left">
                                                    <div class="flex items-center justify-between">
                                                        @if($child->icon)
                                                            <div class="p-1 bg-gray-200 rounded-xl">
                                                                @svg($child->icon, 'w-5 h-5 text-sky-950')
                                                            </div>
                                                        @endif
                                                        <span>{{ $child->title }}</span>
                                                        <svg class="w-4 h-4 transform" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M9 5l7 7-7 7"/>
                                                        </svg>
                                                    </div>
                                                </button>
                                            @else
                                                <a href="{{ $child->url }}" class="flex items-center gap-x-2">
                                                    @if($child->icon)
                                                        <div class="p-1 bg-gray-200 rounded-xl">
                                                            @svg($child->icon, 'w-5 h-5 text-sky-950')
                                                        </div>
                                                    @endif
                                                    <span>{{ $child->title }}</span>
                                                </a>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                @endif
        
                <!-- Sub-Children (Separate List) -->
                @if($menu && $menu->menuItems)
                    @foreach($menu->menuItems as $item)
                        @foreach($item->children as $child)
                            @if($child->children->count() > 0)
                                <!-- Second-level Submenu (Sub-children separated) -->
                                <div x-show="activeMenu === 'menu-{{ $child->id }}'"
                                        x-transition:enter="transition transform ease-out duration-200"
                                        x-transition:enter-start="translate-x-full"
                                        x-transition:enter-end="translate-x-0"
                                        x-transition:leave="transition transform ease-in duration-200"
                                        x-transition:leave-start="translate-x-0"
                                        x-transition:leave-end="-translate-x-full"
                                        class="absolute inset-0 bg-sky-950 p-4 z-30">
                                    <!-- Back Button and Parent Title for Sub-Children -->
                                    <div class="flex items-center bg-sky-900 gap-2 px-4 py-3 mb-4">
                                        <button @click="activeMenu = menuHistory.pop()" class="text-white">
                                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </button>
                                        <span class="text-md font-semibold">{{ $child->title }}</span>
                                    </div>
        
                                    <!-- Sub-Children Items (Second-level children) -->
                                    <ul class="space-y-4">
                                        @foreach($child->children as $subChild)
                                            <li class="px-3 py-2">
                                                <a href="{{ $subChild->url }}" class="flex items-center gap-x-2">
                                                    @if($subChild->icon)
                                                        <div class="p-1 bg-gray-200 rounded-xl">
                                                            @svg($subChild->icon, 'w-5 h-5 text-sky-950')
                                                        </div>
                                                    @endif
                                                    <span>{{ $subChild->title }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    @endforeach
                @endif
        
            </div>
        </x-mary-drawer>

    </div>

    <!-- Big Devices - Second Bar - content: MegaMenu, Cart -->
    <div class="antialiased bg-slate-200 bg-no-repeat text-cyan-800">
        <header class="w-full max-w-[85rem] flex items-center justify-between px-4 mx-auto ">

            <!-- Left Section, MegaMenu -->
            <nav class="hidden md:flex">
                <ul class="flex items-center justify-center font-semibold">

                    @if($menu && $menu->menuItems)
                        @foreach($menu->menuItems as $item)
                            @if($item->children->count() > 0)

                                <li class="relative px-3 py-0 group">
                                    <div class="mt-1 space-y-3">
                                        <div class="flex items-center gap-x-2">
                                            @if($item->icon)
                                                <div class="p-1 rounded-xl">
                                                    @svg($item->icon, 'w-5 h-5 text-blue-700')
                                                </div>
                                            @endif
                                            <div class="flex flex-col">
                                                <div class="cursor-default hover:opacity-50">
                                                    {{ $item->title }}
                                                </div>
                                            </div>
                                        </div>

                                        @if($item->subtitle)
                                            <p class="text-sm text-gray-800">
                                                <i> {{ $item->subtitle }}</i>
                                            </p>
                                        @endif
                                    </div>

                                    <div class="absolute -top-7 justify-start transition group-hover:translate-y-5 translate-y-0 opacity-0 invisible group-hover:opacity-100 group-hover:visible duration-500 ease-in-out group-hover:transform z-50 min-w-[560px] transform">
                                        <div class="relative w-full p-6 bg-white shadow-xl top-12 rounded-xl">
                                            <!-- <div class="w-10 h-10 bg-white transform rotate-45 absolute top-0 z-0 translate-x-0 transition-transform group-hover:translate-x-[12.65rem] duration-500 ease-in-out rounded-sm"></div> -->

                                            <div class="relative z-10">

                                                @php
                                                    $childCount = $item->children->count();
                                                    $gridCols = $childCount > 3 ? 'grid-cols-3' : ($childCount > 1 ? 'grid-cols-' . $childCount : 'grid-cols-1');
                                                @endphp

                                                <div class="grid {{ $gridCols }} grid-cols-2 md:{{ $gridCols }} lg:{{ $gridCols }} gap-6 mt-6">
                                                    @foreach($item->children as $child)
                                                        <div>
                                                            <div class="mt-3 space-y-3">
                                                                <div class="flex items-center gap-x-2">
                                                                    @if($child->icon)
                                                                        <div class="p-1 bg-gray-200 rounded-xl">
                                                                            @svg($child->icon, 'w-5 h-5 text-blue-700')
                                                                        </div>
                                                                    @endif
                                                                    <div class="flex flex-col">
                                                                        <p class="uppercase text-sm text-gray-500 text-[13px]">{{ $child->title }}</p>
                                                                    </div>
                                                                </div>
                                                                @if($child->subtitle)
                                                                    <p class="text-sm text-gray-800">
                                                                        <i> {{ $child->subtitle }}</i>
                                                                    </p>
                                                                @endif
                                                            </div>

                                                            <ul class="mt-3 text-[15px] space-y-4">
                                                                <li>
                                                                    @foreach($child->children as $subChild)
                                                                        <div class="mt-3 space-y-3">
                                                                            <div
                                                                                class="flex items-center gap-x-2">
                                                                                @if($subChild->icon)
                                                                                    <div
                                                                                        class="p-1 bg-gray-200 rounded-xl">
                                                                                        @svg($subChild->icon, 'w-5 h-5 text-blue-700')
                                                                                    </div>
                                                                                @endif
                                                                                <div class="flex flex-col">
                                                                                    <a wire:navigate
                                                                                        href="{{ $subChild->url }}"
                                                                                        class="flex items-center p-2 -mx-2 space-x-2 text-sm text-gray-800 transition duration-300 ease-in-out rounded-lg hover:bg-green-50 hover:text-green-600">
                                                                                        {{ $subChild->title }}
                                                                                    </a>
                                                                                </div>
                                                                            </div>
                                                                            @if($subChild->subtitle)
                                                                                <p class="text-sm text-gray-800">
                                                                                    <i> {{ $subChild->subtitle }}</i>
                                                                                </p>
                                                                            @endif
                                                                        </div>
                                                                    @endforeach
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                            @else

                                <li class="relative px-3 py-0 group">

                                    <div class="mt-1 space-y-3">
                                        <div class="flex items-center gap-x-2">
                                            @if($item->icon)
                                                <div class="p-1 bg-gray-200 rounded-xl">
                                                    @svg($item->icon, 'w-5 h-5 text-blue-700')
                                                </div>
                                            @endif
                                            <div class="flex flex-col">
                                                <a href="{{ $item->url }}"
                                                    class="cursor-default hover:opacity-50">{{ $item->title }}
                                                </a>
                                            </div>
                                        </div>

                                        @if($item->subtitle)
                                            <p class="text-sm text-gray-800">
                                                <i> {{ $item->subtitle }}</i>
                                            </p>
                                        @endif
                                    </div>

                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </nav>

            <!-- Right Section, Cart -->
            <nav class="hidden md:flex">

                <ul class="flex items-center space-x-4">
                    <li class="relative px-3 py-0 group">
                        <a wire:navigate
                            class="font-medium flex items-center py-2 md:py-4 {{ request()->is('cart') ? 'text-blue-600' : 'text-gray-500' }} dark:text-gray-400 dark:hover:text-gray-500 dark:focus:outline-none dark:focus:ring-1 dark:focus:ring-gray-600"
                            href="{{ route('cart') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="flex-shrink-0 w-5 h-5 mr-1">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <span class="mr-1">Cart</span> 
                            <span class="py-0.5 px-1.5 rounded-full text-xs font-medium bg-blue-50 border border-blue-200 text-blue-600">{{ $total_count }}</span>
                        </a>
                    </li>
                    <li class="relative px-3 py-0 group">
                        <button class="cursor-default hover:opacity-50">
                            <div class="absolute text-xs rounded-full -mt-1 -mr-2 px-1 font-bold top-0 right-0 bg-red-700 text-white">
                                {{ $total_count }}
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    class="feather feather-shopping-cart w-6 h-6 mt-0">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                        </button>

                        <div
                            class="absolute top-0 right-0 items-center justify-center 
                            transition-all ease-in-out duration-400 
                            group-hover:translate-y-5 translate-y-0 opacity-0 invisible 
                            group-hover:opacity-100 group-hover:visible group-hover:delay-600 
                            group-hover:transform z-50 min-w-[300px] w-[350px] transform">

                            <div class="relative w-full p-2 bg-white shadow-xl top-10 rounded-xl dark:bg-neutral-800 dark:border dark:border-neutral-700">
                                <div class="relative z-10">
                                    <ul class="mt-2 text-[15px] space-y-2">
                                        <!-- Product Item -->
                                        <li>
                                            <div
                                                class="p-2 flex hover:bg-gray-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-100 dark:border-neutral-700 rounded-lg transition-all duration-200">
                                                <div class="p-2 w-12">
                                                    <img
                                                        src="https://dummyimage.com/50x50/bababa/0011ff&amp;text=50x50"
                                                        alt="img product">
                                                </div>
                                                <div class="flex-auto text-sm w-32">
                                                    <div
                                                        class="font-medium text-gray-800 dark:text-neutral-200">
                                                        Product
                                                        1
                                                    </div>
                                                    <div class="truncate text-gray-400 dark:text-neutral-500">
                                                        Product 1
                                                        description
                                                    </div>
                                                    <div class="text-gray-400 dark:text-neutral-500">Qt: 2</div>
                                                </div>
                                                <div class="flex flex-col w-18 font-medium items-end">
                                                    <div
                                                        class="w-6 h-6 mb-4 hover:bg-red-200 rounded-full cursor-pointer text-red-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                stroke-width="1.5"
                                                                stroke="currentColor"
                                                                class="size-6">
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                        </svg>
                                                    </div>
                                                    $12.22
                                                </div>
                                            </div>

                                            <div
                                                class="p-2 flex hover:bg-gray-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-100 dark:border-neutral-700 rounded-lg transition-all duration-200">
                                                <div class="p-2 w-12">
                                                    <img
                                                        src="https://dummyimage.com/50x50/bababa/0011ff&amp;text=50x50"
                                                        alt="img product">
                                                </div>
                                                <div class="flex-auto text-sm w-32">
                                                    <div
                                                        class="font-medium text-gray-800 dark:text-neutral-200">
                                                        Product
                                                        1
                                                    </div>
                                                    <div class="truncate text-gray-400 dark:text-neutral-500">
                                                        Product 1
                                                        description
                                                    </div>
                                                    <div class="text-gray-400 dark:text-neutral-500">Qt: 2</div>
                                                </div>
                                                <div class="flex flex-col w-18 font-medium items-end">
                                                    <div
                                                        class="w-6 h-6 mb-4 hover:bg-red-200 rounded-full cursor-pointer text-red-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                stroke-width="1.5"
                                                                stroke="currentColor"
                                                                class="size-6">
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                        </svg>
                                                    </div>
                                                    $12.22
                                                </div>
                                            </div>

                                            <div
                                                class="p-2 flex hover:bg-gray-100 dark:bg-neutral-800 dark:hover:bg-neutral-700 cursor-pointer border-b border-gray-100 dark:border-neutral-700 rounded-lg transition-all duration-200">
                                                <div class="p-2 w-12">
                                                    <img
                                                        src="https://dummyimage.com/50x50/bababa/0011ff&amp;text=50x50"
                                                        alt="img product">
                                                </div>
                                                <div class="flex-auto text-sm w-32">
                                                    <div
                                                        class="font-medium text-gray-800 dark:text-neutral-200">
                                                        Product
                                                        1
                                                    </div>
                                                    <div class="truncate text-gray-400 dark:text-neutral-500">
                                                        Product 1
                                                        description
                                                    </div>
                                                    <div class="text-gray-400 dark:text-neutral-500">Qt: 2</div>
                                                </div>
                                                <div class="flex flex-col w-18 font-medium items-end">
                                                    <div
                                                        class="w-6 h-6 mb-4 hover:bg-red-200 rounded-full cursor-pointer text-red-700">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                                stroke-width="1.5"
                                                                stroke="currentColor"
                                                                class="size-6">
                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                                        </svg>
                                                    </div>
                                                    $12.22
                                                </div>
                                            </div>
                                        </li>

                                        <!-- Repeat Product Items -->

                                        <!-- New Subtotal, VAT, Shipping, Total Row -->
                                        <div class="p-2 border-t border-gray-100 dark:border-neutral-700">
                                            <div
                                                class="flex justify-between font-medium text-gray-800 dark:text-neutral-200 mb-2">
                                                <span>Subtotal:</span>
                                                <span>$24.44</span>
                                            </div>
                                            <div
                                                class="flex justify-between font-medium text-gray-800 dark:text-neutral-200 mb-2">
                                                <span>VAT (5%):</span>
                                                <span>$1.22</span>
                                            </div>
                                            <div
                                                class="flex justify-between font-medium text-gray-800 dark:text-neutral-200 mb-2">
                                                <span>Shipping:</span>
                                                <span>$5.00</span>
                                            </div>
                                            <div
                                                class="flex justify-between font-bold text-gray-800 dark:text-neutral-200">
                                                <span>Total:</span>
                                                <span>$30.66</span>
                                            </div>
                                        </div>

                                        <div class="p-4 justify-center flex">
                                            <button class="text-base hover:scale-110 focus:outline-none flex justify-center px-3 py-1 rounded-full font-semibold cursor-pointer
                                            hover:bg-teal-700 hover:text-teal-100 bg-teal-100 text-teal-700 border duration-200 ease-in-out border-teal-600 transition">
                                                Checkout $30.66
                                            </button>
                                        </div>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </nav>

        </header>
    </div>

    <!-- Small Devices - Second Bar - content: Register -->
    <div class="antialiased bg-slate-200 bg-no-repeat text-gray-900">
        <header class="w-full max-w-[85rem] flex items-center justify-between px-4 mx-auto ">    

            <!-- Right Section, Register -->
            <nav class="flex justify-end w-full">
                <ul class="flex items-center space-x-4">
                    @guest('customer')
                    <!-- Register Button (Visible only on smaller devices) -->
                    <li>
                        <a wire:navigate href="{{ route('register') }}" class="md:hidden flex items-center py-2 text-lg font-semibold text-gray-900 bg-white rounded-full bg-opacity-10 group">
                            <span>{{ __('Create account') }}</span>
                        </a>
                    </li>
                    @endguest
                </ul>
            </nav>

        </header>
    </div>
</div>
