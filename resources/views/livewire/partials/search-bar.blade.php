<div>
    <nav class="hidden items-center justify-center md:flex">
        <ul class="flex font-semibold relative">

            <div class="hidden md:block">
                <!-- Search Input -->
                <div class="relative">

                    <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none z-20 ps-3.5">
                        <svg class="shrink-0 size-4 text-gray-800 dark:text-white/60" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/>
                            <path d="m21 21-4.3-4.3"/>
                        </svg>
                    </div>

                    <input id="searchInput" type="text" class="py-2 ps-10 pe-16 pr-10 block w-[32rem] bg-white border-gray-200 rounded-lg text-sm text-gray-800 focus:outline-none focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder:text-neutral-400 dark:focus:ring-neutral-600" placeholder="Search">
                    
                    <div class="hidden absolute inset-y-0 end-0 flex items-center pointer-events-none z-20 pe-1 pr-2">
                        <button type="button"
                                class="inline-flex shrink-0 justify-center items-center size-6 rounded-full text-gray-500 hover:text-blue-600 focus:outline-none focus:text-blue-600 dark:text-neutral-500 dark:hover:text-blue-500 dark:focus:text-blue-500"
                                aria-label="Close">
                            <span class="sr-only">Close</span>
                            <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="m15 9-6 6"/>
                                <path d="m9 9 6 6"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Dropdown for search results -->
                    <div id="searchResults"
                            class="absolute w-[32rem] bg-white border border-gray-200 rounded-lg shadow-lg mt-1 z-50 hidden">
                        <ul id="resultsList" class="p-2 space-y-2">
                            <li class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 cursor-pointer">Result 1
                            </li>
                            <li class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 cursor-pointer">Result 2
                            </li>
                            <li class="p-2 bg-gray-100 rounded-lg hover:bg-gray-200 cursor-pointer">Result 3
                            </li>
                        </ul>
                    </div>

                </div>
                <!-- End Search Input -->
            </div>

            <div class="flex flex-row items-center justify-end gap-1">
                <button type="button" class="md:hidden size-[38px] relative inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-full border border-transparent text-gray-800 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none dark:text-white dark:hover:bg-neutral-700 dark:focus:bg-neutral-700">
                    <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/> 
                        <path d="m21 21-4.3-4.3"/>
                    </svg>
                    <span class="sr-only">Search</span>
                </button>
            </div>

        </ul>
    </nav>
</div>
