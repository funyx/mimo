<?php


namespace Mimo\Console;


use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends GeneratorCommand
{
	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'make:controller';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Create a new controller class';

	/**
	 * The type of class being generated.
	 *
	 * @var string
	 */
	protected $type = 'Controller';

	/**
	 * Get the stub file for the generator.
	 *
	 * @return string
	 */

	public function handle()
	{
		if (parent::handle() === false && !$this->option('force')) {
			return false;
		}

		if ($this->option('factory')) {
			$this->createTest();
		}
		$route_uri = $this->laravel->routeUri($this->getNameInput());
		$controller = $this->laravel->qualifyController($this->getNameInput());
		$this->output->success("Controller generated. Add this routes to your app.");
		$this->output->text("// $route_uri routes");
		$this->output->text("\$app->get('/$route_uri',[\\$controller::class, 'paginator'])->setName('$route_uri.paginator');");
		$this->output->text("\$app->post('/$route_uri',[\\$controller::class, 'store'])->setName('$route_uri.store');");
		$this->output->text("\$app->get('/$route_uri/{id}',[\\$controller::class, 'show'])->setName('$route_uri.show');");
		$this->output->text("\$app->put('/$route_uri/{id}',[\\$controller::class, 'update'])->setName('$route_uri.update');");
		$this->output->text("\$app->delete('/$route_uri{id}',[\\$controller::class, 'destroy'])->setName('$route_uri.destroy');");
	}

	protected function getStub()
	{
		$stub = '/controller.stub';

		return $this->resolveStubPath($stub);
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
		return file_exists($customPath = stubs_path($stub)) ? $customPath : __DIR__.$stub;
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
		return $rootNamespace.'\Controllers';
	}

	/**
	 * Build the class with the given name.
	 *
	 * Remove the base controller import if we are already in the base namespace.
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	protected function buildClass( $name )
	{
		$controllerNamespace = $this->getNamespace($name);

		$replace = [];

		if ($this->option('parent')) {
			$replace = $this->buildParentReplacements();
		}

		if ($this->option('model')) {
			$replace = $this->buildModelReplacements($replace);
		}

		$replace["use {$controllerNamespace}\Controller;\n"] = '';

		return str_replace(array_keys($replace), array_values($replace), parent::buildClass($name));
	}

	/**
	 * Build the replacements for a parent controller.
	 *
	 * @return array
	 */
	protected function buildParentReplacements()
	{
		$parentModelClass = $this->parseModel($this->option('parent'));

		if ( !class_exists($parentModelClass)) {
			if ($this->confirm("A {$parentModelClass} model does not exist. Do you want to generate it?", true)) {
				$this->call('make:model', ['name' => $parentModelClass]);
			}
		}

		return [
			'ParentDummyFullModelClass'   => $parentModelClass,
			'{{ namespacedParentModel }}' => $parentModelClass,
			'{{namespacedParentModel}}'   => $parentModelClass,
			'ParentDummyModelClass'       => class_basename($parentModelClass),
			'{{ parentModel }}'           => class_basename($parentModelClass),
			'{{parentModel}}'             => class_basename($parentModelClass),
			'ParentDummyModelVariable'    => lcfirst(class_basename($parentModelClass)),
			'{{ parentModelVariable }}'   => lcfirst(class_basename($parentModelClass)),
			'{{parentModelVariable}}'     => lcfirst(class_basename($parentModelClass)),
		];
	}

	/**
	 * Get the fully-qualified model class name.
	 *
	 * @param string $model
	 *
	 * @return string
	 *
	 * @throws \InvalidArgumentException
	 */
	protected function parseModel( $model )
	{
		if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
			throw new InvalidArgumentException('Model name contains invalid characters.');
		}

		return $this->qualifyModel($model);
	}

	/**
	 * Build the model replacement values.
	 *
	 * @param array $replace
	 *
	 * @return array
	 */
	protected function buildModelReplacements( array $replace )
	{
		$modelClass = $this->parseModel($this->option('model'));

		if ( !class_exists($modelClass)) {
			if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
				$this->call('make:model', ['name' => $modelClass]);
			}
		}

		return array_merge($replace, [
			'DummyFullModelClass'   => $modelClass,
			'{{ namespacedModel }}' => $modelClass,
			'{{namespacedModel}}'   => $modelClass,
			'DummyModelClass'       => class_basename($modelClass),
			'{{ model }}'           => class_basename($modelClass),
			'{{model}}'             => class_basename($modelClass),
			'DummyModelVariable'    => lcfirst(class_basename($modelClass)),
			'{{ modelVariable }}'   => lcfirst(class_basename($modelClass)),
			'{{modelVariable}}'     => lcfirst(class_basename($modelClass)),
		]);
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
				'Create the class even if the controller already exists'
			],
			[
				'model',
				'm',
				InputOption::VALUE_OPTIONAL,
				'Generate a resource controller for the given model.'
			],
			[
				'parent',
				'p',
				InputOption::VALUE_OPTIONAL,
				'Generate a nested resource controller class.'
			],
			[
				'test',
				't',
				InputOption::VALUE_OPTIONAL,
				'Generate a PEST test for the controller class.'
			],
			[
				'factory',
				'f',
				InputOption::VALUE_OPTIONAL,
				'Used when test option is specified.'
			],
		];
	}

	protected function createTest()
	{
		$name = Str::studly(class_basename($this->argument('name')));

		$this->call('make:test', [
			'name' => "{$name}Test",
			'--controller' => $name,
			'--model' => $this->parseModel($this->option('model'))
		]);
	}

	protected function qualifyFactory($name)
	{
		$name = ltrim($name, '\\/');

		$name = str_replace('/', '\\', $name);

		$rootNamespace = 'Database\\Factories\\';

		if (Str::startsWith($name, $rootNamespace)) {
			return $name;
		}

		return $rootNamespace.$name;
	}
}
