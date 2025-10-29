<?php


    return [
        'hms' => [
            'access_key' => env('HMS_ACCESS_KEY'),
            'app_secret' => env('HMS_APP_SECRET'),
            'room_id' => env('HMS_ROOM_ID'),
            'subdomain' => 'jolina-livestream-8874.app',
            'region' => env('HMS_REGION'),
            'template_id' => env('HMS_TEMPLATE_ID'),
        ],
    
        'postmark' => [
            'token' => env('POSTMARK_TOKEN'),
        ],
    
        'ses' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        ],
    
        'resend' => [
            'key' => env('RESEND_KEY'),
        ],
    
        'slack' => [
            'notifications' => [
                'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
                'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
            ],
        ],
    
        'livekit' => [
            'api_key' => env('LIVEKIT_API_KEY', 'APIPXk2svnnu58p'),
            'api_secret' => env('LIVEKIT_API_SECRET', 'PDDsyAFxCzdM1KQLHrm3MueNBj4eacFNbsieOfLv4vRC'),
            'room_name' => env('LIVEKIT_ROOM_NAME', 'default-room'),
            'url' => env('LIVEKIT_URL', 'wss://myproject-0lnvqwz1.livekit.cloud'),
        ],
    ];
    