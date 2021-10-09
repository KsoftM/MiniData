<?php

namespace ksoftm\system;

use Closure;
use Exception;
use PDOException;
use ksoftm\system\database\Query;
use ksoftm\system\internal\ITable;
use ksoftm\system\internal\DResult;
use ksoftm\system\database\QueryBuilder;

class DB implements ITable
{

    //<<----------->> query transaction isolation level <<----------->>//

    public const TRANSACTION_READ_UNCOMMITTED = "read uncommitted";
    public const TRANSACTION_READ_COMMITTED = "read committed";
    public const TRANSACTION_REPEATABLE_READ = "repeatable read";
    public const TRANSACTION_READ_SERIALIZABLE = "serializable";


    //<<-----X----->> query transaction isolation level <<-----X----->>//


    protected static Query $query;


    public static function query(string $query, array $data = null): DResult|false
    {
        return QueryBuilder::query($query, $data);
    }

    public static function insert(string $table, ?Closure $callback = null): DResult|false
    {
        self::$query = new Query($callback, QueryBuilder::INSERT_QUERY_MODE);

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::INSERT_QUERY_MODE);
    }

    public static function select(string $table, ?Closure $callback = null): DResult|false
    {
        self::$query = new Query($callback, QueryBuilder::SELECT_QUERY_MODE);

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::SELECT_QUERY_MODE);
    }

    public static function update(string $table, ?Closure $callback = null): DResult|false
    {
        self::$query = new Query($callback, QueryBuilder::UPDATE_QUERY_MODE);

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::UPDATE_QUERY_MODE);
    }

    public static function delete(string $table, ?Closure $callback = null): DResult|false
    {
        self::$query = new Query($callback, QueryBuilder::DELETE_QUERY_MODE);

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::DELETE_QUERY_MODE);
    }

    public static function transaction(Closure $transaction): bool
    {
        try {
            DB::beginTransaction();

            if (!is_null($transaction)) {
                $transaction();
            }

            DB::commit();
        } catch (PDOException $e) {
            return false;
        }

        return true;
    }

    public static function inTransaction(): bool
    {
        return MDQ::getPDO()->inTransaction();
    }

    public static function beginTransaction(string $isolationLevel = DB::TRANSACTION_READ_SERIALIZABLE): bool
    {
        if (in_array($isolationLevel, [
            DB::TRANSACTION_READ_UNCOMMITTED,
            DB::TRANSACTION_READ_COMMITTED,
            DB::TRANSACTION_REPEATABLE_READ,
            DB::TRANSACTION_READ_SERIALIZABLE,
        ])) {
            DB::query("set transaction isolation level $isolationLevel");
            MDQ::getPDO()->beginTransaction();
            return true;
        } else {
            throw new Exception("Unknown transaction level is passed in the 'beginTransaction' function on DB.");
        }

        return false;
    }

    public static function commit(): bool
    {
        return MDQ::getPDO()->commit();
    }

    public static function setAutoCommit(bool $autoCommit = true): bool
    {
        $autoCommit = $autoCommit == true ?: 0;
        return DB::query("set auto_commit = $autoCommit");
    }

    public static function rollback(): bool
    {
        return MDQ::getPDO()->rollBack();
    }
}
