<?php 

return [

'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth' ],

'allowed_methods' => ['*'],

'allowed_origins' => ['http://127.0.0.1:5173'],

'allowed_origins_patterns' => [],

'allowed_headers' => ['*'],

'exposed_headers' => [],

'max_age' => 0,

'supports_credentials' => true,

];
