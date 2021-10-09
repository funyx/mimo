<?php

use Illuminate\Config\Repository;
use Illuminate\Console\Application;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Console\DbCommand;
use Illuminate\Database\Console\Factories\FactoryMakeCommand;
use Illuminate\Database\Console\Migrations\FreshCommand;
use Illuminate\Database\Console\Migrations\InstallCommand;
use Illuminate\Database\Console\Migrations\MigrateCommand;
use Illuminate\Database\Console\Migrations\MigrateMakeCommand;
use Illuminate\Database\Console\Migrations\RefreshCommand;
use Illuminate\Database\Console\Migrations\ResetCommand;
use Illuminate\Database\Console\Migrations\RollbackCommand;
use Illuminate\Database\Console\Migrations\StatusCommand;
use Illuminate\Database\Console\PruneCommand;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Database\Console\Seeds\SeederMakeCommand;
use Illuminate\Database\Console\WipeCommand;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\Migrator;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\Facades\Facade;
use Mimo\AliasLoader;
use Mimo\Console;
use Mimo\Console\ActionMakeCommand;
use Mimo\Console\ControllerMakeCommand;
use Mimo\Console\DocsServeCommand;
use Mimo\Console\DumpCommand;
use Mimo\Console\MiddlewareMakeCommand;
use Mimo\Console\ModelMakeCommand;
use Mimo\Console\TestMakeCommand;
use Mimo\PackageManifest;

$app = new Console();
$app->bind('config', fn () => new Repository([
    'database' => config('database'),
    'providers' => [],
    'aliases' => config('aliases'),
]));
$app['path'] = 'app';
$app['env'] = fn () => new Repository(config('env'));

$app->offsetSet(Dispatcher::class, fn () => new Dispatcher());
$app->offsetSet(Filesystem::class, fn () => new Filesystem());
$app->offsetSet(Manager::class, function ($app) {
    $m = new Manager($app);
    $m->setAsGlobal();
    $m->bootEloquent();

    return $m;
});
$app->offsetSet(ConnectionResolver::class, function (Console $app) {
    $resolver = new ConnectionResolver();
    $resolver->addConnection('default', $app['db']->getConnection());
    $resolver->setDefaultConnection('default');

    return $resolver;
});
$app->offsetSet(Composer::class, fn ($app) => new Composer($app['files'], base_path()));
$app->offsetSet(DatabaseMigrationRepository::class, fn ($app) => new DatabaseMigrationRepository($app['db.connection'], config('database.migrations')));
$app->offsetSet(Migrator::class, fn ($app) => new Migrator($app['db.migration.repo'], $app['db.connection'], $app['files']));
$app->offsetSet(MigrationCreator::class, fn ($app) => new MigrationCreator($app['files'], stubs_path('migrations')));
$app->offsetSet(PackageManifest::class, fn ($app) => new PackageManifest($app['files'], base_path(), base_path('cache/packages.php')));

$app->alias(Dispatcher::class, 'events');
$app->alias(Filesystem::class, 'files');
$app->alias(ConnectionResolver::class, ConnectionResolverInterface::class);
$app->alias(Manager::class, 'db');
$app->alias(ConnectionResolver::class, 'db.connection');
$app->alias(Migrator::class, 'db.migrator');
$app->alias(MigrationCreator::class, 'db.migration.creator');
$app->alias(DatabaseMigrationRepository::class, 'db.migration.repo');
$app->alias(Composer::class, 'composer');
$app->alias(Dispatcher::class, 'Illuminate\Contracts\Events\Dispatcher');

Facade::clearResolvedInstances();
Facade::setFacadeApplication($app);
AliasLoader::getInstance(array_merge(
    config('aliases'),
    $app->make(PackageManifest::class)->aliases()
))->register();

$console = new Application($app, $app['events'], '0.1');
$console->setName('Mimo');

$console->addCommands([
    new DbCommand(),
    new DumpCommand(),
    new PruneCommand(),
    new WipeCommand(),
    new FactoryMakeCommand($app['files']),
    new FreshCommand(),
    new InstallCommand($app['db.migration.repo']),
    new MigrateCommand($app['db.migrator'], $app['events']),
    new MigrateMakeCommand($app['db.migration.creator'], $app['composer']),
    new RefreshCommand(),
    new ResetCommand($app['db.migrator']),
    new RollbackCommand($app['db.migrator']),
    new StatusCommand($app['db.migrator']),
    new SeedCommand($app['db.connection']),
    new SeederMakeCommand($app['files']),
    new ActionMakeCommand($app['files']),
    new ControllerMakeCommand($app['files']),
    new MiddlewareMakeCommand($app['files']),
    new ModelMakeCommand($app['files']),
    new TestMakeCommand($app['files']),
    new DocsServeCommand(),
]);

if (! array_key_exists('console', $_SERVER)) {
    $_SERVER['console'] = $console;
}

return $console;
