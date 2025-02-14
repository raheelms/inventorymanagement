@aware(['page'])
@props(['images'])

<div class="p-6 bg-stone-100">
    <div class="max-w-4xl mx-auto">
        <x-mary-carousel :slides="array_map(function ($item) {
            return [
                'image' => '/' . $item['image'],
                'title' => $item['title'],
                'description' => $item['description'],
                'url' => $item['url'],
                'urlText' => $item['urlText'],
            ];
        }, $images)" class="h-[500px] shadow-none" />
    </div>
</div>

