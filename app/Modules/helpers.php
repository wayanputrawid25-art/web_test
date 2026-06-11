<?php

namespace App\Modules;

if (!function_exists('module_path')) {
    function module_path(string $name, string $path = ''): string
    {
        $modulePath = app_path('Modules/'.$name);
        
        if ($path) {
            return $modulePath .= '/'.$path;
        }
        
        return $modulePath;
    }
}