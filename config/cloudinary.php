<?php

return [
    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key'    => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),
    'secure'     => true, // Use HTTPS
    
    // Video upload settings
    'video' => [
        'folder' => 'livestream-recordings', // Organize videos in folder
        'resource_type' => 'video',
        'quality' => 'auto:good', // Automatic quality optimization
        'format' => 'mp4',
    ],
];