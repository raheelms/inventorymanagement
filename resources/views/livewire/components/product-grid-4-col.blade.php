<div>
    @if($this->products->isNotEmpty())

        <div class="container mx-auto mt-0 relative">
            <!-- Product Cards -->
            <div class="responsive flex flex-wrap justify-between">
                @foreach ($this->products as $index => $product)
                    <div class="w-full sm:w-1/2 md:w-1/4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-{{ max(4, $this->products->count()) }} px-2">
                        <livewire:components.product-card :product="$product" :key="$product->id" />
                    </div>
                @endforeach
        
                <!-- Fill empty spaces with placeholders to maintain full width -->
                @for ($i = $this->products->count(); $i < 4; $i++)
                    <div class="w-full sm:w-1/2 md:w-1/4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3">
                        <div class="invisible"></div>
                    </div>
                @endfor
            </div>
        </div>    

        {{-- 
        <div class="container mx-auto mt-8">
            <!-- Image Slider with Slick -->
            <div class="responsive relative">
                <img src="https://wallpapers.com/images/featured-full/beautiful-mountain-pictures-wfvd4x42htesrnyp.jpg" alt="Image 1">
                <img src="https://media.cntraveler.com/photos/5a009c8e25be133d871c008e/16:9/w_1920,c_limit/Mountain-Travel_GettyImages-503689316.jpg" alt="Image 2">
                <img src="https://images.pexels.com/photos/417173/pexels-photo-417173.jpeg" alt="Image 3">
                <img src="https://geographical.co.uk/wp-content/uploads/Photographing-mountains-in-spring.jpg" alt="Image 4">
            </div>
        </div>
        --}}

        <!-- Show the "Load More" button if there are more products to load -->
        @if($this->show_load_more === 'true')
            <div class="text-center mt-4">
                <button wire:click="loadMore"
                    class="bg-blue-600 text-white font-semibold rounded px-6 py-3 
                           hover:bg-blue-700 focus:outline-none 
                           focus:ring-2 focus:ring-blue-400 focus:ring-offset-2 
                           active:bg-blue-700 transition duration-200 ease-in-out">
                    {{ __('Load More') }}
                </button>
            </div>
        @endif

    @endif
</div>

<script>
    $(document).ready(function () {
        // Initialize Slick slider
        $('.responsive').slick({
            dots: false, // Show navigation dots
            infinite: false, // Infinite scrolling
            speed: 300, // Transition speed
            slidesToShow: 4, // Number of slides visible
            slidesToScroll: 1, // Number of slides to scroll per click
            prevArrow: `<button class="slick-prev slick-arrow absolute top-1/2 left-[-2] rounded-full
                                    transform -translate-y-1/2 bg-gray-900 hover:bg-gray-700 
                                    focus:outline-none focus:ring-2 focus:ring-gray-500 
                                    active:bg-gray-800 z-50 w-12 h-12 flex items-center justify-center" aria-label="Previous">
                                    ←
                        </button>`,
            nextArrow: `<button class="slick-next slick-arrow absolute top-1/2 right-[-2] rounded-full
                                    transform -translate-y-1/2 bg-gray-900 hover:bg-gray-700 
                                    focus:outline-none focus:ring-2 focus:ring-gray-500 
                                    active:bg-gray-800 z-50 w-12 h-12 flex items-center justify-center" aria-label="Next">
                                    →
                        </button>`,
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 1,
                    },
                },
                {
                    breakpoint: 600,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1,
                    },
                },
            ],
        });

        // Ensure the hover effect does not persist on the slick arrows
        $('.slick-arrow').on('mouseup', function () {
            $(this).blur(); // Remove focus from the button after clicking
        });
    });
</script>
