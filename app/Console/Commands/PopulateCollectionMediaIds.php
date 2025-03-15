<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Collection;
use Awcodes\Curator\Models\Media;
use Illuminate\Support\Str;

class PopulateCollectionMediaIds extends Command
{
    protected $signature = 'collections:media-ids 
        {--force : Force update even if media_id is already set}';

    protected $description = 'Populate media_id for collections based on their first image';

    public function handle()
    {
        $force = $this->option('force');
        $updatedCount = 0;
        $skippedCount = 0;

        // Fetch collections with images
        $collectionsWithImages = Collection::whereNotNull('images')
            ->where('images', '!=', '[]')
            ->get();

        $this->info('Processing ' . $collectionsWithImages->count() . ' collections with images');

        foreach ($collectionsWithImages as $collection) {
            // Decode images
            $images = is_string($collection->images) 
                ? json_decode($collection->images, true) 
                : $collection->images;

            // Skip if no images
            if (empty($images)) {
                $skippedCount++;
                continue;
            }

            // Get first image path
            $firstImagePath = $images[0];

            // Check existing media_id
            if (!$force && !empty($collection->media_id)) {
                $skippedCount++;
                continue;
            }

            // Find or create media
            $media = Media::firstOrCreate(
                ['path' => $firstImagePath],
                [
                    'name' => basename($firstImagePath),
                    'ext' => pathinfo($firstImagePath, PATHINFO_EXTENSION),
                    'alt' => Str::headline(pathinfo($firstImagePath, PATHINFO_FILENAME)),
                    'title' => Str::headline(pathinfo($firstImagePath, PATHINFO_FILENAME)),
                ]
            );

            // Update collection
            $collection->media_id = $media->id;
            $collection->save();

            $updatedCount++;
        }

        // Output results
        $this->info("Media ID population complete.");
        $this->info("Updated collections: {$updatedCount}");
        $this->info("Skipped collections: {$skippedCount}");

        return Command::SUCCESS;
    }
}