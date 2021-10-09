# mimo
mimo

slim4 skeleton:
- eloquent orm
- mimo cli (artisan port)
- openapi 3.1.0 documentation (auto-generated using `redoc-cli`)
- openapi 3.1.0 (spec auto-testing using middleware)

```shell
$ ./mimo                                                                                                                                                                       [14:49:00]
Mimo 0.1

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display help for the given command. When no command is given display help for the list command
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi|--no-ansi  Force (or disable --no-ansi) ANSI output
  -n, --no-interaction  Do not ask any interactive question
      --env[=ENV]       The environment the command should run under
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  db                Start a new database CLI session
  help              Display help for a command
  list              List commands
  migrate           Run the database migrations
 db
  db:seed           Seed the database with records
  db:wipe           Drop all tables, views, and types
 make
  make:action       Create a new action class
  make:controller   Create a new controller class
  make:docs         Create documentation
  make:factory      Create a new model factory
  make:middleware   Create a new middleware class
  make:migration    Create a new migration file
  make:model        Create a new model class
  make:seeder       Create a new seeder class
  make:test         Create a new phpunit feature test class
 migrate
  migrate:fresh     Drop all tables and re-run all migrations
  migrate:install   Create the migration repository
  migrate:refresh   Reset and re-run all migrations
  migrate:reset     Rollback all database migrations
  migrate:rollback  Rollback the last database migration
  migrate:status    Show the status of each migration
 model
  model:prune       Prune models that are no longer needed
 schema
  schema:dump       Dump the given database schema
```

WIP:
 - autogenerate skeleton from openapi spec
