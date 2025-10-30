<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadService
{
    public function handleVideoUpload(UploadedFile $file, string $directory = 'videos'): ?string
    {
        try {
            // Increase memory and time limits
            ini_set('memory_limit', '1024M');
            set_time_limit(900);
            
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs($directory, $filename, 'public');
            
            Log::info('Video uploaded successfully', [
                'path' => $path,
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName()
            ]);
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Video upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }
    
    public function handleImageUpload(UploadedFile $file, string $directory = 'images'): ?string
    {
        try {
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs($directory, $filename, 'public');
            
            Log::info('Image uploaded successfully', [
                'path' => $path,
                'size' => $file->getSize()
            ]);
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            return null;
        }
    }
}