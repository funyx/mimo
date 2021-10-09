<?php

namespace Mimo\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

/**
 * @property \Mimo\Console $laravel
 */
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
        if (parent::handle() === false && ! $this->option('force')) {
            return false;
        }

        if ($this->option('factory')) {
            $this->createTest();
        }
        $uri_segments = array_map(
            'strtolower',
            array_filter($this->laravel->explodeCaps($this->getNameInput()), fn ($v) => $v != 'Controller')
        );
        $last_uri_segment = array_key_last($uri_segments);
        $plural_form = Str::plural($uri_segments[$last_uri_segment]);
        $singular_form = Str::singular($uri_segments[$last_uri_segment]);
        $pluralize = str_replace($singular_form, '', $plural_form);

        //set last segment in singular form
        $singular_uri_semgnets = $plural_uri_segments = $uri_segments;
        $singular_uri_semgnets[$last_uri_segment] = $singular_form;
        $plural_uri_segments[$last_uri_segment] = $plural_form;

        $uri_route = implode('-', $singular_uri_semgnets);
	    $singular_named_route = implode('.', $singular_uri_semgnets);
        $plurar_named_route = implode('.', $plural_uri_segments);

        $controller = $this->laravel->qualifyController($this->getNameInput());
        $lines = [];
        $lines[] = "\$app->group('/$uri_route', function (\Slim\Interfaces\RouteCollectorProxyInterface \$proxy) {";
        $lines[] = "    \$proxy->get('$pluralize', [\\$controller::class, 'paginator'])->setName('$plurar_named_route.paginator');";
        $lines[] = "    \$proxy->post('[/]', [\\$controller::class, 'store'])->setName('$singular_named_route.store');";
        $lines[] = "    \$proxy->get('/{id}', [\\$controller::class, 'show'])->setName('$singular_named_route.show');";
        $lines[] = "    \$proxy->put('/{id}', [\\$controller::class, 'update'])->setName('$singular_named_route.update');";
        $lines[] = "    \$proxy->delete('/{id}', [\\$controller::class, 'destroy'])->setName('$singular_named_route.destroy');";
        $lines[] = "});";

        $app_file_path = base_path('core/app.php');
        $app_file_content = new Collection(file($app_file_path));
        $id = $app_file_content->search($app_file_content->first(fn ($v) => Str::contains($v, 'COMMAND ANCHOR')));
        if ($id) {
            $before = (clone $app_file_content)->splice(0, $id);
            $after = (clone $app_file_content)->splice($id + 1, $app_file_content->count());
            $new_app_new_file_content = $before->toArray();
            array_push($new_app_new_file_content, ...array_map(fn ($v) => $v.PHP_EOL, $lines));
            array_push($new_app_new_file_content, PHP_EOL);
            array_push($new_app_new_file_content, $app_file_content->get($id));
            array_push($new_app_new_file_content, ...$after->toArray());

            $f = fopen($app_file_path, 'r+w');
            flock($f, LOCK_EX);
            fwrite($f, implode('', $new_app_new_file_content));
            fclose($f);

            $this->output->success("Controller generated. Routes for $plural_form added to your app.");
        } else {
            $this->output->success("Controller generated. Add this $plural_form routes to your app.");
            foreach ($lines as $line) {
                $this->output->text($line);
            }
        }
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
    protected function buildClass($name)
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

        if (! class_exists($parentModelClass)) {
            if ($this->confirm("A {$parentModelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $parentModelClass]);
            }
        }

        return [
            'ParentDummyFullModelClass' => $parentModelClass,
            '{{ namespacedParentModel }}' => $parentModelClass,
            '{{namespacedParentModel}}' => $parentModelClass,
            'ParentDummyModelClass' => class_basename($parentModelClass),
            '{{ parentModel }}' => class_basename($parentModelClass),
            '{{parentModel}}' => class_basename($parentModelClass),
            'ParentDummyModelVariable' => lcfirst(class_basename($parentModelClass)),
            '{{ parentModelVariable }}' => lcfirst(class_basename($parentModelClass)),
            '{{parentModelVariable}}' => lcfirst(class_basename($parentModelClass)),
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
    protected function parseModel($model)
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
    protected function buildModelReplacements(array $replace)
    {
        $modelClass = $this->parseModel($this->option('model'));

        if (! class_exists($modelClass)) {
            if ($this->confirm("A {$modelClass} model does not exist. Do you want to generate it?", true)) {
                $this->call('make:model', ['name' => $modelClass]);
            }
        }

        return array_merge($replace, [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
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
                'Create the class even if the controller already exists',
            ],
            [
                'model',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Generate a resource controller for the given model.',
            ],
            [
                'parent',
                'p',
                InputOption::VALUE_OPTIONAL,
                'Generate a nested resource controller class.',
            ],
            [
                'test',
                't',
                InputOption::VALUE_OPTIONAL,
                'Generate a PEST test for the controller class.',
            ],
            [
                'factory',
                'f',
                InputOption::VALUE_OPTIONAL,
                'Used when test option is specified.',
            ],
        ];
    }

    protected function createTest()
    {
        $name = Str::studly(class_basename($this->argument('name')));

        $this->call('make:test', [
            'name' => "{$name}Test",
            '--controller' => $name,
            '--model' => $this->parseModel($this->option('model')),
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
