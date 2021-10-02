<?php

namespace Mimo\Console;

use Illuminate\Database\Connection;

class DumpCommand extends \Illuminate\Database\Console\DumpCommand
{
    /**
     * Create a schema state instance for the given connection.
     *
     * @param  \Illuminate\Database\Connection  $connection
     * @return mixed
     */
    protected function schemaState(Connection $connection)
    {
        return $connection->getSchemaState()
            ->withMigrationTable($connection->getTablePrefix().config('database.migrations', 'migrations'))
            ->handleOutputUsing(function ($type, $buffer) {
                $this->output->write($buffer);
            });
    }
}
