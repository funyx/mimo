<?php

namespace Mimo\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @property \Mimo\Console $laravel
 */
class GenFromSpecCommand extends Command
{
    protected $name = 'gen:spec';
    protected $description = 'Generate project files from openapi 3 spec';
    private string $spec;

    public function handle()
    {
        $file = base_path('spec/'.trim($this->argument('name')));
        if (! file_exists($file)) {
            $this->error('The spec file "'.$this->getNameInput().'" does not exist.');

            return false;
        }

        if (! $this->spec = file_get_contents($file)) {
            $this->error('The spec file "'.$this->getNameInput().'" is not readable.');

            return false;
        }
	    echo PHP_EOL;
        echo "****************************** WIP ******************************";
        echo PHP_EOL;
	    echo PHP_EOL;
        print_r($this->spec);
	    echo PHP_EOL;
    }



    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the openapi spec file'],
        ];
    }
	protected function getNameInput()
	{
		return trim($this->argument('name'));
	}
}
