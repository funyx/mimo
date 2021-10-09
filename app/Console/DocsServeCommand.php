<?php

namespace Mimo\Console;

use Illuminate\Console\Command;

class DocsServeCommand extends Command
{
    protected $name = 'make:docs';

    protected $description = 'Create documentation';

    public function handle()
    {
        $path = '/docs';
        shell_exec("redoc-cli bundle -o ./public$path/index.html ./spec/openapi.yaml");
        $this->output->info('Documentation (re)generated at '. config('env.base_path') .$path);
    }
}
