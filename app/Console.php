<?php

namespace Mimo;

use Illuminate\Container\Container;
use JetBrains\PhpStorm\Pure;

class Console extends Container
{
    const VERSION = '0.1';
    public function environment(...$environments)
    {
        return $this['env'];
    }
    #[Pure] public function databasePath($path = ''): string {
        return database_path($path);
    }
    #[Pure] public function basePath($path = ''): string {
        return base_path($path);
    }
    public function getNamespace(): string {
        return 'Mimo\\';
    }
}
