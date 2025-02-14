@props(['page'])
<div>
    <x-filament-fabricator::layouts.base :title="$page->title">
        {{-- Header Here --}}
    
        <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
    
        {{-- Footer Here --}}
    </x-filament-fabricator::layouts.base>
</div>