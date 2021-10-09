<?php

namespace Mimo\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * @property \Mimo\Console $laravel
 */
class TestMakeCommand extends GeneratorCommand
{
    protected $name = 'make:test';

    protected $description = 'Create a new phpunit feature test class';

    protected $type = 'Test';

    protected function getStub()
    {
        return stubs_path('phpunit.feature.stub');
    }

    protected function getPath($name)
    {
        $name = Str::replaceFirst('Tests', '', $name);
        $name = rtrim($name, 'Test');
        $name .= 'Test';

        return base_path('tests').str_replace('\\', '/', $name).'.php';
    }

    public function handle()
    {
        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }
    }

    protected function rootNamespace()
    {
        return 'Tests\\Feature';
    }

    protected function buildClass($name)
    {
        $controllerClassName = $this->option('controller');
        $controllerFQN = $this->laravel->qualifyController($controllerClassName);
        $readableController = $this->laravel->testCaseVar($controllerClassName);
        $model = $this->laravel->qualifyModel($this->option('model'));
        $readableModel = $this->laravel->testCaseVar($this->option('model'));
        $replace = [
            '{{ testClass }}' => $controllerClassName.'Test',
            '{{ controller }}' => $controllerFQN,
            '{{ readableController }}' => $readableController,
            '{{ model }}' => $model,
            '{{ readableModel }}' => $readableModel,
        ];

        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }

    protected function getOptions()
    {
        return [
            [
                'force',
                null,
                InputOption::VALUE_OPTIONAL,
                'Create the test even if it exists.',
            ],
            [
                'controller',
                'c',
                InputOption::VALUE_REQUIRED,
                'Controller to mock.',
            ],
            [
                'model',
                'm',
                InputOption::VALUE_REQUIRED,
                'Model to use.',
            ],
        ];
    }
}
