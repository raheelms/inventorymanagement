<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use App\Filament\Resources\InventoryResource;
use App\Models\Inventory;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;

class Invoice extends Page
{
    /**
     * The Filament resource class associated with this component.
     */
    protected static string $resource = InventoryResource::class;

    public $record;
    public $inventory;
    public $provider;
    public $settings;

    /**
     * Initialize the component with the given record ID.
     *
     * @param mixed $record The record ID to load
     * @return void
     */
    public function mount($record)
    {
        $this->record = $record;
        $this->inventory = Inventory::with(['providers', 'inventory_items'])->find($record);
        $this->provider = $this->inventory->providers->first();
    }

    /**
     * Define the header actions for the Filament resource page.
     *
     * @return array Array of Filament Action objects
     */
    public function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label(__('Print'))
                ->icon('heroicon-o-printer')
                ->requiresConfirmation()
                ->url(route('print.inventory_invoice', ['id'=>$this->record]))
                ->openUrlInNewTab(),

            Action::make('download')
                ->label(__('Download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->requiresConfirmation()
                ->url(route('download.inventory_invoice', ['id' => $this->record]))
                ->openUrlInNewTab()
        ];
    }

    protected static string $view = 'filament.resources.inventory-resource.pages.invoice';
}
