<?php


namespace Mimo\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

/**
 * @see https://github.com/laravel/framework/blob/8.x/src/Illuminate/Foundation/Console/ModelMakeCommand.php
 */
class ModelMakeCommand extends GeneratorCommand
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:model';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new model class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Model';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle()
	{
		if (parent::handle() === false && !$this->option('force')) {
			return false;
		}

		if ($this->option('all')) {
			$this->input->setOption('factory', true);
			$this->input->setOption('seed', true);
			$this->input->setOption('migration', true);
			$this->input->setOption('controller', true);
		}

		if ($this->option('factory')) {
			$this->createFactory();
		}

		if ($this->option('migration')) {
			$this->createMigration();
		}

		if ($this->option('seed')) {
			$this->createSeeder();
		}

		if ($this->option('controller')) {
			$this->createController();
		}
	}

	/**
	 * Create a model factory for the model.
	 *
	 * @return void
	 */
	protected function createFactory()
	{
		$factory = Str::studly($this->argument('name'));

		$this->call('make:factory', [
			'name'    => "{$factory}Factory",
			'--model' => $this->qualifyClass($this->getNameInput()),
		]);
	}

	/**
	 * Create a migration file for the model.
	 *
	 * @return void
	 */
	protected function createMigration()
	{
		$table = Str::snake(Str::pluralStudly(class_basename($this->argument('name'))));

		$this->call('make:migration', [
			'name'     => "create_{$table}_table",
			'--create' => $table,
		]);
	}

	/**
	 * Create a seeder file for the model.
	 *
	 * @return void
	 */
	protected function createSeeder()
	{
		$seeder = Str::studly(class_basename($this->argument('name')));

		$this->call('make:seeder', [
			'name' => "{$seeder}Seeder",
		]);
	}

	/**
	 * Create a controller for the model.
	 *
	 * @return void
	 */
	protected function createController()
	{
		$name = Str::studly(class_basename($this->argument('name')));

		$modelName = $this->laravel->qualifyModel($this->getNameInput());

		$testName = $this->laravel->qualifyTest("{$modelName}");

		$factoryName = $this->laravel->qualifyFactory("{$modelName}");

		$this->call('make:controller', array_filter([
			'name'    => "{$name}Controller",
			'--model' => $modelName,
			'--test'  => $testName,
			'--factory' => $factoryName
		]));
	}


	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */
	protected function getStub()
	{
		return $this->resolveStubPath('/model.stub');
	}

	/**
	 * Resolve the fully-qualified path to the stub.
	 *
	 * @param string $stub
	 *
	 * @return string
	 */
	protected function resolveStubPath( $stub )
	{
		return stubs_path($stub);
	}

	/**
	 * Get the default namespace for the class.
	 *
	 * @param string $rootNamespace
	 *
	 * @return string
	 */
	protected function getDefaultNamespace( $rootNamespace )
	{
		return $rootNamespace.'\\Models';
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
				'all',
				'a',
				InputOption::VALUE_NONE,
				'Generate a migration, seeder, factory, and resource controller for the model'
			],
			[
				'controller',
				'c',
				InputOption::VALUE_NONE,
				'Create a new controller for the model'
			],
			[
				'factory',
				'f',
				InputOption::VALUE_NONE,
				'Create a new factory for the model'
			],
			[
				'force',
				null,
				InputOption::VALUE_NONE,
				'Create the class even if the model already exists'
			],
			[
				'migration',
				'm',
				InputOption::VALUE_NONE,
				'Create a new migration file for the model'
			],
			[
				'seed',
				's',
				InputOption::VALUE_NONE,
				'Create a new seeder file for the model'
			]
		];
	}
}
