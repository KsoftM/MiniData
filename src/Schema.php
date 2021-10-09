<?php

namespace ksoftm\system;

use Closure;
use ksoftm\system\internal\DResult;
use ksoftm\system\internal\ISchema;
use ksoftm\system\database\RawQuery;
use ksoftm\system\database\QueryBuilder;

class Schema implements ISchema
{
    protected static RawQuery $query;

    //<<----------->> Table <<----------->>//


    public static function Create(string $table, Closure $callback): DResult|false
    {
        self::$query = new RawQuery($callback, QueryBuilder::TABLE_CREATE_MODE);

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::TABLE_CREATE_MODE);
    }

    public static function CreateIfNotExists(string $table, Closure $callback): DResult|false
    {
        self::$query = new RawQuery($callback, QueryBuilder::TABLE_CREATE_MODE);
        self::$query->ifNotExists();

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::TABLE_CREATE_MODE);
    }

    public static function Drop(string $table, Closure $callback = null): DResult|false
    {
        self::$query = new RawQuery($callback, QueryBuilder::TABLE_DROP_MODE);

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::TABLE_DROP_MODE);
    }

    public static function DropIfExists(string $table, Closure $callback = null): DResult|false
    {
        self::$query = new RawQuery($callback, QueryBuilder::TABLE_DROP_MODE);
        self::$query->ifExists();

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::TABLE_DROP_MODE);
    }

    public static function Alter(string $table, Closure $callback): DResult|false
    {
        //TODO: in the future
        self::$query = new RawQuery($callback, QueryBuilder::TABLE_ALTER_MODE);

        echo '<pre>';
        var_dump('this future in development.');
        echo '</pre>';
        exit;

        return QueryBuilder::Build(self::$query, $table, QueryBuilder::TABLE_ALTER_MODE);
    }
    //<<-----X----->> Table <<-----X----->>//
}


/*


ALTER TABLE 'bericht' 
DROP FOREIGN KEY 'your_foreign_key';

ALTER TABLE 'bericht'
ADD CONSTRAINT 'your_foreign_key' FOREIGN KEY ('column_foreign_key') REFERENCES 'other_table' ('column_parent_key') ON UPDATE CASCADE ON DELETE SET NULL;


*/