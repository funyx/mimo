<?php

use DI\Bridge\Slim\Bridge;
use DI\ContainerBuilder;
use Mimo\Actions\HomeAction;

$container = new ContainerBuilder();

$container->addDefinitions( require_once __DIR__ . '/container.php');

try {
    $app = Bridge::create( $container->build() );
} catch( Exception $e ) {
    throw new RuntimeException( 'DI\Container build failed' );
}

$app->get( '/', new HomeAction );

if(!array_key_exists('app', $_SERVER)){
    $_SERVER['app'] = $app;
}

return $app;
