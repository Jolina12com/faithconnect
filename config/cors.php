<?php

return [
    'paths' => ['api/*', 'get-token', 'check-allowed'],
    'allowed_methods' => ['GET', 'POST', 'OPTIONS'],
    'allowed_origins' => ['*'],  // Adjust this for your specific origins
    'allowed_headers' => ['Content-Type', 'Authorization', 'X-CSRF-TOKEN'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
