<?php
namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    protected $cloudinary;

    public function __construct()
    {
        // Debug environment variables
        $cloudName = env('CLOUDINARY_CLOUD_NAME');
        $apiKey = env('CLOUDINARY_API_KEY');
        $apiSecret = env('CLOUDINARY_API_SECRET');
        
        Log::info('Cloudinary config check', [
            'cloud_name' => $cloudName,
            'api_key' => $apiKey ? 'present' : 'missing',
            'api_secret' => $apiSecret ? 'present' : 'missing'
        ]);
        
        if (!$cloudName || !$apiKey || !$apiSecret) {
            throw new \Exception('Cloudinary configuration missing: cloud_name=' . ($cloudName ?: 'null') . ', api_key=' . ($apiKey ? 'present' : 'null') . ', api_secret=' . ($apiSecret ? 'present' : 'null'));
        }
        
        $config = Configuration::instance([
            'cloud' => [
                'cloud_name' => $cloudName,
                'api_key' => $apiKey,
                'api_secret' => $apiSecret,
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        $this->cloudinary = new Cloudinary($config);
    }

    public function uploadRecording($filePath, $fileName, $folder = 'livestream_recordings')
    {
        try {
            Log::info('Cloudinary upload starting', [
                'file' => $fileName,
                'size' => filesize($filePath),
                'path' => $filePath,
                'folder' => $folder
            ]);
            
            // Determine public_id based on folder
            $publicIdPrefix = $folder === 'sermons/videos' ? 'sermons/' : 'livestreams/';
            
            $result = $this->cloudinary->uploadApi()->upload($filePath, [
                'resource_type' => 'video',
                'public_id' => $publicIdPrefix . pathinfo($fileName, PATHINFO_FILENAME),
                'folder' => $folder,
                'chunk_size' => 6000000, // 6MB chunks (minimum 5MB required by Cloudinary)
                'timeout' => 600, // 10 minutes timeout for larger/slow networks
            ]);
            
            Log::info('Cloudinary upload success', [
                'public_id' => $result['public_id'],
                'url' => $result['secure_url']
            ]);
            
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function getThumbnailUrl($publicId)
    {
        try {
            // Generate thumbnail URL (Cloudinary auto-generates thumbnails)
            $url = "https://res.cloudinary.com/" . env('CLOUDINARY_CLOUD_NAME') . "/video/upload/w_640,h_360,c_fill/" . $publicId . ".jpg";
            return $url;
        } catch (\Exception $e) {
            Log::error('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteRecording($publicId)
    {
        try {
            Log::info('Deleting from Cloudinary', ['public_id' => $publicId]);
            
            $result = $this->cloudinary->uploadApi()->destroy($publicId, [
                'resource_type' => 'video',
                'invalidate' => true
            ]);
            
            Log::info('Cloudinary delete result', ['result' => $result]);
            return $result;
            
        } catch (\Exception $e) {
            Log::error('Cloudinary delete failed: ' . $e->getMessage());
            return false;
        }
    }
}