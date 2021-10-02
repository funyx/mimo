<?php

namespace Mimo\Console;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class ActionMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:action';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new action class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Action';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        $stub = '/action.stub';

        return $this->resolveStubPath($stub);
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param string $stub
     *
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = stubs_path($stub)) ? $customPath : __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Actions';
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base action import if we are already in the base namespace.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $actionNamespace = $this->getNamespace($name);

        $replace = [];

        $replace["use {$actionNamespace}\Action;\n"] = '';

        return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            [
                'force',
                null,
                InputOption::VALUE_NONE,
                'Create the class even if the action already exists',
            ],
        ];
    }
}
