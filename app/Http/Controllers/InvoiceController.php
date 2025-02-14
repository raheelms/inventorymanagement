<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InvoiceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

/**
 * Controller for handling inventory invoice operations.
 */
class InvoiceController extends Controller
{
    /**
     * Common method to handle invoice generation and record creation.
     * 
     * @param int $id The inventory ID
     * @param string $disposition Either 'inline' for viewing or 'attachment' for download
     * @return mixed Response with PDF or redirect on error
     */
    private function handleInvoice($id, $disposition)
    {
        // Load inventory with all necessary relationships and items
        $inventory = Inventory::with([
            'providers',
            'inventory_items.product'  // Assuming you want product details for each item
        ])->find($id);
        
        if (!$inventory) {
            Notification::make()
                ->title(__('No inventory record found...'))
                ->danger()
                ->send();
            return redirect()->back();
        }
    
        // Get the first provider
        $provider = $inventory->providers->first();
    
        // Create invoice record
        InvoiceRecord::create([
            'user_id' => Filament::auth()->id(),
            'inventory_id' => $id
        ]);
    
        // Pass all necessary data to the view
        $data = [
            'inventory' => $inventory,
            'provider' => $provider,
            'items' => $inventory->inventory_items,
            'invoice_number' => $inventory->invoice_number,
            'purchase_date' => $inventory->purchase_date,
            'total_amount' => $inventory->total_amount,
            'taxes' => $inventory->taxes,
            'shipping_amount' => $inventory->shipping_amount,
            'discount_amount' => $inventory->discount_amount,
            'grand_total' => $inventory->grand_total,
        ];
    
        $pdf = PDF::loadView('pdf.inventory_invoice', $data);
        
        return Response::make($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="invoice.pdf"'
        ]);
    }

    /**
     * Generate and stream an inventory invoice PDF for viewing.
     * 
     * @param mixed $id The ID of the inventory record to print
     * @return \Illuminate\Http\Response PDF stream response
     */
    public function printInventoryInvoice($id)
    {
        return $this->handleInvoice($id, 'inline');
    }

    /**
     * Generate and download an inventory invoice PDF.
     *
     * @param mixed $id The ID of the inventory record to download
     * @return \Illuminate\Http\Response PDF download response
     */
    public function downloadInventoryInvoice($id)
    {
        return $this->handleInvoice($id, 'attachment');
    }
}