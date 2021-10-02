<?php

declare( strict_types=1 );

use Di\Container;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use JetBrains\PhpStorm\Pure;
use Slim\App;
use Symfony\Component\VarDumper\VarDumper;

/*
 * dd
 * app
 * eloquent
 * throw_when
 * app_path
 * base_path
 * config_path
 * database_path
 * config
 * data_get
 * data_set
 */

if( !function_exists( 'dd' ) ) {
    #[NoReturn] function dd(): void {
        ( new Collection( func_get_args() ) )
            ->each( function($item) {
                VarDumper::dump( $item );
            } );
        exit;
    }
}
if( !function_exists( 'app' ) ) {
    function app(): App {
        return $_SERVER['app'];
    }
}
if( !function_exists('eloquent')) {
    function eloquent(): Manager {
        return \app()->getContainer()->get(Manager::class);
    }
}
if( !function_exists( 'container' ) ) {
    #[Pure] function container(): Container {
        /** @var Container $c */
        $c = app()->getContainer();

        return $c;
    }
}
if( !function_exists( 'throw_when' ) ) {
    function throw_when(bool $fails, string $message, string $exception = Exception::class) {
        if( !$fails ) {
            return;
        }

        throw new $exception( $message );
    }
}
if( !function_exists( 'base_path' ) ) {
    function base_path($path = ''): string {
        return __DIR__ . "/{$path}";
    }
}
if( !function_exists( 'app_path' ) ) {
    function app_path($path = ''): string {
        return base_path( "app/{$path}" );
    }
}
if( !function_exists( 'stubs_path' ) ) {
    function stubs_path($path = ''): string {
        return base_path( "stubs/{$path}" );
    }
}
if( !function_exists( 'config_path' ) ) {
    #[Pure] function config_path($path = '') {
        return base_path( "config/{$path}" );
    }
}
if( !function_exists( 'database_path' ) ) {
    #[Pure] function database_path($path = '') {
        return base_path( "database/{$path}" );
    }
}
if( !function_exists( 'config' ) ) {
    function config($path = null, $value = null) {
        $config = [];
        $folder = scandir( config_path() );
        foreach( $folder as $file ) {
            if( 'php' === Str::after( $file, '.' ) ) {
                data_set( $config, Str::before( $file, '.php' ), require config_path( $file ) );
            }
        }

        return data_get( $config, $path );
    }
}

if( !function_exists( 'data_get' ) ) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param mixed                 $target
     * @param array|int|string|null $key
     * @param mixed|null            $default
     *
     * @return mixed
     */
    function data_get(mixed $target, array|int|string|null $key, mixed $default = null): mixed {
        if( null === $key ) {
            return $target;
        }

        $key = is_array( $key ) ? $key : explode( '.', $key );

        while( null !== ( $segment = array_shift( $key ) ) ) {
            if( '*' === $segment ) {
                if( $target instanceof Collection ) {
                    $target = $target->all();
                } elseif( !is_array( $target ) ) {
                    return value( $default );
                }

                $result = [];

                foreach( $target as $item ) {
                    $result[] = data_get( $item, $key );
                }

                return in_array( '*', $key ) ? Arr::collapse( $result ) : $result;
            }

            if( Arr::accessible( $target ) && Arr::exists( $target, $segment ) ) {
                $target = $target[$segment];
            } elseif( is_object( $target ) && isset( $target->{$segment} ) ) {
                $target = $target->{$segment};
            } else {
                return value( $default );
            }
        }

        return $target;
    }
}

if( !function_exists( 'data_set' ) ) {
    /**
     * Set an item on an array or object using dot notation.
     *
     * @param mixed        $target
     * @param array|string $key
     * @param mixed        $value
     * @param bool         $overwrite
     *
     * @return mixed
     */
    function data_set(mixed &$target, array|string $key, mixed $value, bool $overwrite = true): mixed {
        $segments = is_array( $key ) ? $key : explode( '.', $key );

        if( ( $segment = array_shift( $segments ) ) === '*' ) {
            if( !Arr::accessible( $target ) ) {
                $target = [];
            }

            if( $segments ) {
                foreach( $target as &$inner ) {
                    data_set( $inner, $segments, $value, $overwrite );
                }
            } elseif( $overwrite ) {
                foreach( $target as &$inner ) {
                    $inner = $value;
                }
            }
        } elseif( Arr::accessible( $target ) ) {
            if( $segments ) {
                if( !Arr::exists( $target, $segment ) ) {
                    $target[$segment] = [];
                }

                data_set( $target[$segment], $segments, $value, $overwrite );
            } elseif( $overwrite || !Arr::exists( $target, $segment ) ) {
                $target[$segment] = $value;
            }
        } elseif( is_object( $target ) ) {
            if( $segments ) {
                if( !isset( $target->{$segment} ) ) {
                    $target->{$segment} = [];
                }

                data_set( $target->{$segment}, $segments, $value, $overwrite );
            } elseif( $overwrite || !isset( $target->{$segment} ) ) {
                $target->{$segment} = $value;
            }
        } else {
            $target = [];

            if( $segments ) {
                data_set( $target[$segment], $segments, $value, $overwrite );
            } elseif( $overwrite ) {
                $target[$segment] = $value;
            }
        }

        return $target;
    }
}
