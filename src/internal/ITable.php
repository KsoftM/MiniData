<?php

namespace ksoftm\system\internal;

use Closure;
use ksoftm\system\internal\DResult;

interface ITable
{
    /**
     * execute the query
     *
     * @param string $query
     * @param array $data
     *
     * @return array|false
     */
    static function query(string $query, array $data = null): DResult|false;

    /**
     * insert data using query
     *
     * @param string $table
     * @param \Closure $callback
     *
     * @return void
     */
    static function insert(string $table, ?Closure $query): DResult|false;

    /**
     * select data using query
     *
     * @param string $table
     * @param \Closure $callback
     *
     * @return void
     */
    static function select(string $table, ?Closure $query): DResult|false;

    /**
     * update data using query
     *
     * @param string $table
     * @param \Closure $callback
     *
     * @return void
     */
    static function update(string $table, ?Closure $query): DResult|false;

    /**
     * update data using query
     *
     * @param string $table
     * @param \Closure $callback
     *
     * @return void
     */
    static function delete(string $table, ?Closure $query): DResult|false;

    /**
     * check it is in transaction
     * 
     * @return boolean
     */
    static function inTransaction(): bool;

    /**
     * transaction for query
     *
     * @param Closure $transaction
     *
     * @return boolean
     */
    static function transaction(Closure $transaction): bool;

    /**
     * start the transaction
     *
     * @return boolean
     */
    static function beginTransaction(): bool;

    /**
     * commit the transaction
     *
     * @return boolean
     */
    static function commit(): bool;

    /**
     * rollback the transaction
     *
     * @return boolean
     */
    static function rollback(): bool;
}
