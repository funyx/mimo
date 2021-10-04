<?php

namespace Mimo;

use Illuminate\Container\Container;
use Illuminate\Support\Str;

class Console extends Container
{
    public const VERSION = '0.1';

    public function environment(...$environments)
    {
        return $this['env'];
    }

    public function databasePath($path = ''): string
    {
        return database_path($path);
    }

    public function basePath($path = ''): string
    {
        return base_path($path);
    }

    public function getNamespace(): string
    {
        return 'Mimo\\';
    }

    public function provideFacades($namespace)
    {
        AliasLoader::setFacadeNamespace($namespace);
    }

	public function qualifyModel($name)
	{
		$name = ltrim($name, '\\/');
		$name = str_replace('/', '\\', $name);
		$rootNamespace = $this->getNamespace().'Models\\';
		if (Str::startsWith($name, $rootNamespace)) {
			return $name;
		}

		return $rootNamespace.$name;
	}

	public function qualifyFactory($name)
	{
		$name = ltrim($name, '\\/');
		$name = str_replace('/', '\\', $name);
		$rootNamespace = 'Database\\Factories\\';
		if (Str::startsWith($name, $rootNamespace)) {
			return $name;
		}

		return $rootNamespace.$name;
	}

	public function qualifyTest($name, $type = 'Feature')
	{
		$name = ltrim($name, '\\/');
		$name = str_replace('/', '\\', $name);
		$rootNamespace = 'Tests\\' . $type . '\\';
		if (Str::startsWith($name, $rootNamespace)) {
			return $name;
		}

		return $rootNamespace.$name;
	}

    public function qualifyController($name)
    {
        $name = ltrim($name, '\\/');
        $name = str_replace('/', '\\', $name);
        $rootNamespace = $this->getNamespace().'Controllers\\';
        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return $rootNamespace.$name;
    }

    public function routeUri(string $var)
    {
    	$segments = explode(' ', $this->descriptionVariable($var));
	    array_pop($segments);
        return implode('-' ,$segments);
    }

    public function descriptionVariable(string $var)
    {
        return rtrim(strtolower(implode(' ', preg_split('/(?<=\\w)(?=[A-Z])/', $var))), ' ');
    }
}
