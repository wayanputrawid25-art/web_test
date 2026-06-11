<?php

namespace App\Modules;

use Illuminate\Support\ServiceProvider;

abstract class ModuleServiceProvider extends ServiceProvider
{
    protected string $name = '';
    protected string $nameLower = '';

    public function register(): void
    {
        $this->registerModule();
    }

    public function boot(): void
    {
        $this->bootModule();
    }

    protected function registerModule(): void
    {
        $this->nameLower = strtolower($this->name);
    }

    protected function bootModule(): void
    {
        //
    }

    public static function getModuleName(): string
    {
        return static::class;
    }
}