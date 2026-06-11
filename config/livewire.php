<?php

return [
    'asset_url' => env('LIVEWIRE_ASSET_URL'),
    'endpoint_namespace' => 'App\\Http\\Livewire',
    'temporary_file_upload' => [
        'disk' => 'local',
        'directory' => 'livewire-tmp',
        'max_upload_time' => 5,
    ],
    'persist_navigation' => true,
    'navigate_with_progress_bar' => true,
    'morph_maskable_attributes' => [
        'data-navigate-on-click',
        'data-navigate-self',
        'data-navigate-cache',
        'data-navigate-persist',
        'data-navigate-prefetch',
    ],
];