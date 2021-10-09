<?php

namespace ksoftm\system\internal;

use Closure;
use ksoftm\system\internal\DResult;

/**
 * interface for schema
 */
interface ISchema
{
    /**
     * create new table using query
     *
     * @param string $table
     * @param \Closure $callback
     *
     * @return DResult|false
     */
    static function Create(string $table, Closure $callback): DResult|false;

    /**
     * create new table if not exists using query
     *
     * @param string $table
     * @param \Closure $callback
     *
     * @return DResult|false
     */
    static function CreateIfNotExists(string $table, Closure $callback): DResult|false;

    /**
     * drop table using query
     *
     * @param string $table
     * @param \Closure $callback
     *
     * @return DResult|false
     */
    static function Drop(string $table, Closure $callback = null): DResult|false;

    /**
     * drop table if exists using query
     *
     * @param string $table
     * @param \Closure $callback
     *
     * @return array|false
     */
    static function DropIfExists(string $table, Closure $callback = null): DResult|false;

    /**
     * alter the table using query
     *
     * @param string $table
     * @param Closure $callback
     *
     * @return DResult|false
     */
    static function Alter(string $table, Closure $callback): DResult|false;
}
