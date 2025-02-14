@php
    $itemsPerPage = 30; // Maximized to fit all items
    $items = $inventory->inventory_items->chunk($itemsPerPage);
    $currentPage = 1;
    $totalPages = ceil($inventory->inventory_items->count() / $itemsPerPage);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Purchase From') }} {{ $provider->company_name }}</title>
    <!-- Preload Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Open+Sans:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 10mm;
        }
        
        body {
            margin: 0;
            padding: 0;
            font-family: Roboto, 'Open Sans', 'Source Sans Pro', Nunito, 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.0;
        }
        
        .invoice-container {
            width: 190mm;
            margin: 0 auto;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 5mm;
        }
        
        .header-column {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .seller-details, .buyer-details {
            text-align: right;
        }
        
        .header-column:first-child .seller-details {
            text-align: left;
        }
        
        .seller-details h2, .buyer-details h2 {
            margin: 0 0 2mm 0;
            font-size: 9pt;
            font-weight: bold;
        }
        
        .seller-details p, .buyer-details p {
            margin: 1mm 0;
        }
        
        .invoice-details {
            display: table;
            width: 100%;
            background-color: #f5f5f4;
            margin-bottom: 5mm;
        }
        
        .invoice-details-row {
            display: table-row;
        }
        
        .invoice-details-column {
            display: table-cell;
            width: 20%;
            padding: 1mm;
            text-align: right;
        }
        
        .invoice-details-column.label {
            color: #666;
            font-weight: bold;
        }
        
        .invoice-details-column.value {
            font-weight: bold;
        }
        
        .invoice-details-column p {
            margin: 0;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }
        
        .items-table th, 
        .items-table td {
            border: 0.5pt solid #ddd;
            padding: 1.5mm;
            vertical-align: top;
            font-size: 9pt;
        }
        
        .totals-table {
            width: 50%;
            margin-left: auto;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 2mm;
            border-bottom: 0.5pt solid #ddd;
            font-size: 9pt;
        }
        
        .totals-table .total-label {
            text-align: right;
            padding-right: 5mm;
            color: #666;
        }
        
        .totals-table .total-amount {
            text-align: right;
        }
        
        .grand-total {
            font-weight: bold;
            background-color: #f0f0f0;
        }
        
        .notes {
            margin-top: 5mm;
            font-size: 9pt;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header with Billing Details -->
        <div class="header">
            <div class="header-column">
                <div class="seller-details">
                    <h2>{{ $provider->company_name }}</h2>
                    <p>{{ $provider->shipping_street_name }} {{ $provider->shipping_house_number }}</p>
                    <p>{{ $provider->shipping_postal_code }} {{ $provider->shipping_city }}</p>
                    <p>{{ $provider->shipping_country }}</p>
                    <p>{{ __('Chamber of Commerce: ') }}23456789</p>
                    <p>{{ __('VAT Number ') }}23456789</p>                           
                    <p>{{ __('Email Address: ') }}{{ $provider->email }}</p>
                </div>
            </div>
            <div class="header-column">
                <div class="buyer-details">
                    <h2>FK Trade</h2>
                    <p>9552 Vandervort Spurs</p>
                    <p>Paradise, 43325</p>
                    <p>United States</p>
                    <p>{{ __('Chamber of Commerce: ') }}23456789</p>
                    <p>{{ __('VAT Number ') }}23456789</p>                           
                    <p>{{ __('Email Address: ') }} email</p>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="invoice-details-row">
                <div class="invoice-details-column"></div>
                <div class="invoice-details-column"></div>
                <div class="invoice-details-column"></div>
                <div class="invoice-details-column label">{{ __('Date') }}</div>
                <div class="invoice-details-column label">{{ __('Invoice Number') }}</div>
            </div>
            <div class="invoice-details-row">
                <div class="invoice-details-column"></div>
                <div class="invoice-details-column"></div>
                <div class="invoice-details-column"></div>
                <div class="invoice-details-column value">{{ $inventory->purchase_date->format('F j, Y') }}</div>
                <div class="invoice-details-column value">{{ $inventory->invoice_number }}</div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 40%;">Product Details</th>
                    <th style="width: 10%;">Quantity</th>
                    <th style="width: 15%;">Price</th>
                    <th style="width: 15%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($inventory->inventory_items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}.</td>
                        <td>{{ $item->product ? $item->product->name : 'No Product' }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: right;">{{ strtoupper($inventory->currency) }} {{ number_format($item->price, 2) }}</td>
                        <td style="text-align: right;">{{ strtoupper($inventory->currency) }} {{ number_format($item->quantity * $item->price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals Section -->
        <table class="totals-table">
            <tr>
                <td class="total-label">{{ __('Sub Total') }}</td>
                <td class="total-amount">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->total_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">{{ __('Discount') }}</td>
                <td class="total-amount">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->discount_amount, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">{{ __('VAT Total') }}</td>
                <td class="total-amount">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->taxes, 2) }}</td>
            </tr>
            <tr>
                <td class="total-label">{{ __('Shipping Charges') }}</td>
                <td class="total-amount">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->shipping_amount, 2) }}</td>
            </tr>
            <tr class="grand-total">
                <td class="total-label">{{ __('Grand Total') }}</td>
                <td class="total-amount">{{ strtoupper($inventory->currency) }} {{ number_format($inventory->grand_total, 2) }}</td>
            </tr>
        </table>

        <!-- Notes -->
        <div class="notes">
            <p style="font-weight: bold; margin-bottom: 2mm;">{{ __('Notes') }}</p>
            <p>{{ $inventory->notes }}</p>
        </div>
    </div>
</body>
</html>