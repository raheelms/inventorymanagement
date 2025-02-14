<x-filament-panels::page>
    @php
        $itemsPerPage = 10;
        $items = $inventory->inventory_items->chunk($itemsPerPage);
        $currentPage = 1;
        $totalPages = ceil($inventory->inventory_items->count() / $itemsPerPage);
    @endphp

    <head>
        <meta charset="UTF-8">
        <title>Purchase From {{$inventory->provider?->name}}</title>
        <style>
            @page {
                size: A4;
                margin: 0;
            }
            @media print {
                body {
                    margin: 0;
                    padding: 0;
                }
                .page-break {
                    page-break-before: always;
                }
                .avoid-break {
                    page-break-inside: avoid;
                }
                .first-page-header {
                    display: none;
                }
                .first-page-header:first-of-type {
                    display: block;
                }
                .repeated-header {
                    display: block;
                }
                .repeated-header:first-of-type {
                    display: none;
                }
                .pagination-controls {
                    display: none;
                }
            }
        </style>
    </head>

    <div class="bg-white w-[210mm] mx-auto min-h-[297mm] relative">
        @foreach($items as $pageItems)
            @if($currentPage > 1)
                <div class="page-break"></div>
            @endif

            <!-- First Page Header -->
            @if($currentPage === 1)
            <div class="first-page-header">
                <div class="p-8">
                    <!-- Billing Information -->
                    <div class="px-8 py-4 text-sm">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Left Address -->
                            <div class="text-sm text-gray-950 space-y-0.5">
                                <h2 class="text-lg font-semibold">{{ $provider->company_name }}</h2>
                                <p>{{ $provider->shipping_street_name }} {{ $provider->shipping_house_number }}</p>
                                <p>{{ $provider->shipping_postal_code }} {{ $provider->shipping_city }}</p>
                                <p class="mb-2">{{ $provider->shipping_country }}</p>
                                <p>{{ __('Chamber of Commerce: ') }}23456789</p>
                                <p>{{ __('VAT Number ') }}23456789</p>                           
                                <p>{{ __('Email Address: ') }}{{ $provider->email }}</p>
                            </div>
                            <!-- Right Address -->
                            <div class="text-sm text-gray-950 space-y-0.5 text-right">
                                <h2 class="text-lg font-semibold">FK Trade</h2>
                                <p>9552 Vandervort Spurs</p>
                                <p>Paradise, 43325</p>
                                <p class="mb-2">United States</p>
                                <p>{{ __('Chamber of Commerce: ') }}23456789</p>
                                <p>{{ __('VAT Number ') }}23456789</p>                           
                                <p>{{ __('Email Address: ') }}{{ $provider->email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Details -->
                    <div class="bg-stone-100 px-8 py-3 text-sm">
                        <div class="flex justify-end gap-8">
                            <div class="text-right">
                                <p class="whitespace-nowrap text-slate-400">{{ __('Date') }}</p>
                                <p class="whitespace-nowrap font-bold text-main">{{ $inventory->purchase_date->format('F j, Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="whitespace-nowrap text-slate-400">{{ __('Invoice Number') }}</p>
                                <p class="whitespace-nowrap font-bold text-main">{{ $inventory->invoice_number }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            <!-- Repeated Header for subsequent pages -->
            <div class="repeated-header p-8">
                <div class="flex justify-between items-center px-8 py-2 border-b">
                    <div class="text-sm">
                        <h2 class="font-bold">Invoice #{{ $inventory->invoice_number }}</h2>
                        <p class="text-gray-600">{{ $inventory->purchase_date->format('F j, Y') }}</p>
                    </div>
                    <div class="text-sm text-right">
                        <p>Page {{ $currentPage }} of {{ $totalPages }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Items Table -->
            <div class="px-8 py-4 text-sm text-neutral-700">
                <!-- Headers -->
                <div class="grid grid-cols-6 border-b-2 border-main pb-2">
                    <div class="font-bold text-main pl-2">#</div>
                    <div class="font-bold text-main pl-2 col-span-2">Product details</div>
                    <div class="font-bold text-main pl-2 text-center">Qty.</div>
                    <div class="font-bold text-main pl-2 text-right">Price</div>
                    <div class="font-bold text-main pl-2 pr-2 text-right">Total</div>
                </div>

                <!-- Items -->
                @foreach($pageItems as $item)
                <div class="grid grid-cols-6 border-b py-2">
                    <div class="pl-2">{{ ($currentPage - 1) * $itemsPerPage + $loop->iteration }}.</div>
                    <div class="pl-2 col-span-2">{{ $item->product ? $item->product->name : 'No Product' }}</div>
                    <div class="pl-2 text-center">{{ $item->quantity }}</div>
                    <div class="pl-2 text-right">{{ strtoupper($inventory->currency) }} {{ number_format($item->price, 2) }}</div>
                    <div class="pl-2 pr-2 text-right">{{ strtoupper($inventory->currency) }} {{ number_format($item->quantity * $item->price, 2) }}</div>
                </div>
                @endforeach
            </div>

            @if($loop->last)
            <!-- Totals Section (only on last page) -->
            <div class="avoid-break px-8 py-4">
                <div class="flex justify-end">
                    <div class="w-1/3 text-sm space-y-1">
                        <div class="flex justify-between border-b p-2">
                            <div class="whitespace-nowrap text-slate-400">{{ __('Sub Total') }}</div>
                            <div class="whitespace-nowrap font-bold text-main">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->total_amount, 2) }}</div>
                        </div>
                        <div class="flex justify-between p-2">
                            <div class="whitespace-nowrap text-slate-400">{{ __('Discount') }}</div>
                            <div class="whitespace-nowrap font-bold text-main">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->discount_amount, 2) }}</div>
                        </div>
                        <div class="flex justify-between p-2">
                            <div class="whitespace-nowrap text-slate-400">{{ __('VAT Total') }}</div>
                            <div class="whitespace-nowrap font-bold text-main">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->taxes, 2) }}</div>
                        </div>
                        <div class="flex justify-between p-2">
                            <div class="whitespace-nowrap text-slate-400">{{ __('Shipping Charges') }}</div>
                            <div class="whitespace-nowrap font-bold text-main">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->shipping_amount, 2) }}</div>
                        </div>
                        <div class="flex justify-between bg-main p-2">
                            <div class="whitespace-nowrap font-bold text-gray-900">{{ __('Grand Total ') }}</div>
                            <div class="whitespace-nowrap font-bold text-gray-900">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->grand_total, 2) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="text-md text-neutral-700 space-y-0.5 mt-8">
                    <p class="text-main font-bold">{{ __('Notes') }}</p>
                    <p class="font-medium">{{ $inventory->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Pagination -->
            @if($currentPage > 1)
                <div class="absolute bottom-16 right-0 w-full px-8 pagination-controls">
                    <div class="flex justify-end items-center space-x-4">
                        <span class="text-sm text-gray-500">{{ __('Page:') }}</span>
                        <div class="flex space-x-1">
                            @for ($i = 1; $i <= $totalPages; $i++)
                                <span class="px-3 py-1 {{ $currentPage === $i ? 'bg-main text-white' : 'bg-gray-100' }} rounded cursor-pointer">
                                    {{ $i }}
                                </span>
                            @endfor
                        </div>
                    </div>
                </div>
            @endif

            @if($loop->last)
            <!-- Footer -->
            <footer class="absolute bottom-0 left-0 bg-slate-100 w-full text-neutral-600 text-center text-xs py-3">
                fk trade
            </footer>
            @endif

            @php $currentPage++; @endphp
        @endforeach
    </div>
</x-filament-panels::page>