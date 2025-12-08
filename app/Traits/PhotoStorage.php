<?php

namespace App\Traits;

trait PhotoStorage
{
    /**
     * Get the storage disk for photos
     * Uses disk name from config or defaults to 'photos', falls back to 'public'
     */
    protected function getPhotoDisk(): string
    {
        $diskName = config('filesystems.photos_disk', 'photos');
        
        // Check if the disk is configured
        if (config("filesystems.disks.{$diskName}")) {
            return $diskName;
        }
        
        // Fallback to public disk
        return 'public';
    }
}

