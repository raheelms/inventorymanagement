<div>
    <a wire:navigate href="{{ route('category.show', $category) }}">
        <x-mary-badge value="{{ $category->name }}" class="badge badge-primary" /> 
    </a>     
</div>
