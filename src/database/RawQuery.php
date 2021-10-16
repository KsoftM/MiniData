<?php

namespace ksoftm\system\database;

use ksoftm\system\internal\Column;
use ksoftm\system\internal\BaseQuery;
use ksoftm\system\database\QueryBuilder;
use ksoftm\system\utils\datatype\ListData;

/**
 * This class ues to create, insert, update, delete, join, ect... the table
 */
class RawQuery extends BaseQuery
{
    //<<----------->> DATA TYPES FOR RAW QUERY <<----------->>//

    public const DATA_TYPE_TINYTEXT = 'TINYTEXT';
    public const DATA_TYPE_MEDIUMTEXT = 'MEDIUMTEXT';
    public const DATA_TYPE_VARCHAR = 'VARCHAR';
    public const DATA_TYPE_TEXT = 'TEXT';
    public const DATA_TYPE_LONGTEXT = 'LONGTEXT';

    public const DATA_TYPE_JSON = 'JSON';

    public const DATA_TYPE_BOOLEAN = 'BOOLEAN';

    public const DATA_TYPE_TINYINT = 'TINYINT';
    public const DATA_TYPE_SMALLINT = 'SMALLINT';
    public const DATA_TYPE_MEDIUMINT = 'MEDIUMINT';
    public const DATA_TYPE_INTEGER = 'INT';
    public const DATA_TYPE_BIGINT = 'BIGINT';

    public const DATA_TYPE_FLOAT = 'FLOAT';
    public const DATA_TYPE_DOUBLE = 'DOUBLE';
    public const DATA_TYPE_DECIMAL = 'DECIMAL';

    public const DATA_TYPE_DATE = 'DATE';
    public const DATA_TYPE_TIME = 'TIME';
    public const DATA_TYPE_YEAR = 'YEAR';
    public const DATA_TYPE_DATETIME = 'DATETIME';
    public const DATA_TYPE_TIMESTAMP = 'TIMESTAMP';

    public const ALTER_DROP_COLUMN_TYPE = 'DROP';


    //<<-----X----->> DATA TYPES FOR RAW QUERY <<-----X----->>//

    /**
     * set column into the arguments
     *
     * @param \ksoftm\system\internal\Column $column
     *
     * @return void
     */
    protected function setColumns(Column $column): void
    {
        $cols = $this->argument->getValue(QueryBuilder::COLUMN_TYPE) ?: new ListData;
        if ($cols instanceof ListData) {
            $cols->add($column);
        }

        $this->argument->add(QueryBuilder::COLUMN_TYPE, $cols);
    }

    /**
     * ID of the schema query to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function id(string $name = 'id'): ?Column
    {
        return $this->integer($name)->autoIncrement()->primaryKey();
    }

    /**
     * tiny text data type to create table
     *
     * @param string $name
     * @param integer $length
     *
     * @return \ksoftm\system\internal\Column
     */
    public function tinyText(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_TINYTEXT);
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * medium text data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function mediumText(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_MEDIUMTEXT);
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * string of the schema query to create table
     *
     * @param string $name
     * @param integer $length
     *
     * @return \ksoftm\system\internal\Column
     */
    public function string(string $name, int $length = 254): ?Column
    {
        if (empty($length) || $length <= 0) {
            $length = 254;
        }

        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_VARCHAR . "($length)");
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * text data type to create table
     *
     * @param string $name
     * @param integer $length
     *
     * @return \ksoftm\system\internal\Column
     */
    public function text(string $name, int $length = 254): ?Column
    {
        if (empty($length) || $length <= 0) {
            $length = 254;
        }

        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_TEXT . "($length)");
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * long text data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function longText(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_LONGTEXT);
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    public function jsonText(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_JSON);
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * tiny size integer data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function boolean(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_BOOLEAN);
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * tiny size integer data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function tinyInteger(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_TINYINT);
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * small size integer data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function smallInteger(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_SMALLINT);
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * medium size integer data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function mediumInteger(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_MEDIUMINT);
            $this->setColumns($tmp);
        }
        return $tmp ?? null;
    }

    /**
     * integer for the schema query to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function integer(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_INTEGER);
            $this->setColumns($tmp);
        }

        return $tmp ?? null;
    }

    /**
     * big integer data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function bigInteger(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_BIGINT);
            $this->setColumns($tmp);
        }

        return $tmp ?? null;
    }

    /**
     * floating data maker
     *
     * @param string $name
     * @param integer $total
     * @param integer $decimals
     *
     * @return \ksoftm\system\internal\Column
     */
    protected function floatingValue(string $type, string $name, int $total = null, int $decimals = null): ?Column
    {
        if (empty($type)) return null;

        if (
            is_numeric($total) && $total <= 0
        ) {
            $total = 7;
        }
        if (is_numeric($decimals) && $decimals <= 0) {
            $decimals = 3;
        }

        if (!empty($name)) {
            $tmp = new Column(
                $name,
                (empty($total) || empty($decimals)) ? "$type" : "$type($total,$decimals)"
            );
            $this->setColumns($tmp);
        }

        return $tmp;
    }

    /**
     * float point data type to create table
     *
     * @param string $name
     * @param integer $total
     * @param integer $decimals
     *
     * @return \ksoftm\system\internal\Column
     */
    public function float(string $name, int $total = null, int $decimals = null): ?Column
    {
        return $this->floatingValue(self::DATA_TYPE_FLOAT, $name, $total, $decimals);
    }

    /**
     * double data type to create table
     *
     * @param string $name
     * @param integer $total
     * @param integer $decimals
     *
     * @return \ksoftm\system\internal\Column
     */
    public function double(string $name, int $total = null, int $decimals = null): ?Column
    {
        return $this->floatingValue(self::DATA_TYPE_DOUBLE, $name, $total, $decimals);
    }

    /**
     * decimal data type to create table
     *
     * @param string $name
     * @param integer|null $total
     * @param integer|null $decimals
     *
     * @return \ksoftm\system\internal\Column|null
     */
    public function decimal(string $name, int $total = null, int $decimals = null): ?Column
    {
        return $this->floatingValue(self::DATA_TYPE_DECIMAL, $name, $total, $decimals);
    }

    /**
     * data data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function date(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_DATE);
            $this->setColumns($tmp);
        }
        return $tmp;
    }

    /**
     * time data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function time(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_TIME);
            $this->setColumns($tmp);
        }
        $this->setColumns($tmp);
        return $tmp;
    }

    /**
     * year data type to create table
     *
     * @param string $name
     * @param [type] $length
     *
     * @return \ksoftm\system\internal\Column
     */
    public function year(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_YEAR);
            $this->setColumns($tmp);
        }
        return $tmp;
    }

    /**
     * datetime data type to create table
     *
     * @param string $name
     * @param [type] $length
     *
     * @return \ksoftm\system\internal\Column
     */
    public function datetime(string $name, int $length = null): ?Column
    {
        if (is_numeric($length) && $length <= 0) {
            $length = 5;
        }

        if (!empty($name)) {
            $tmp = new Column(
                $name,
                empty($length) ? self::DATA_TYPE_DATETIME : self::DATA_TYPE_DATETIME . "($length)"
            );
            $this->setColumns($tmp);
        }

        return $tmp;
    }

    /**
     * timestamp data type to create table
     *
     * @param string $name
     *
     * @return \ksoftm\system\internal\Column
     */
    public function timestamp(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, self::DATA_TYPE_TIMESTAMP);
            $this->setColumns($tmp);
        }

        return $tmp;
    }

    /**
     * created_at and updated_at timestamp data type to create table
     *
     * @param string $created
     * @param string $updated
     *
     * @return void
     */
    public function timestamps($created = "created_time", $updated = "updated_time"): void
    {
        if (!empty($created)) {
            $this->timestamp($created)->default('CURRENT_TIMESTAMP', false)->nullable();
        }

        if (!empty($updated)) {
            $this->timestamp($updated)->default('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', false)->nullable();
        }
    }


    /*<<----------->> alteration raw query in future update <<----------->>
    
    
    public function dropColumn(string $name): ?Column
    {
        if (!empty($name)) {
            $tmp = new Column($name, QueryBuilder::COLUMN_TYPE);
            $this->setColumns($tmp);
        }

        return $tmp;
    }

    public function renameColumn(string $to, string $from): ?Column
    {
        return null;
    }

    public function dropPrimaryKey(string $name): ?Column
    {
        // ALTER TABLE `sakila`.`address` 
        // DROP FOREIGN KEY `fk_address_city`;
        // ALTER TABLE `sakila`.`address` 
        // DROP INDEX `idx_fk_city_id` ;
        // ;
        return null;
    }

    public function dropForeignKey(string $name): ?Column
    {
        return null;
    }

    public function dropIndex(string $name): ?Column
    {
        return null;
    }

    public function dropKey(string $name): ?Column
    {
        return null;
    }

    public function dropUnique(string $name): ?Column
    {
        return null;
    }


    // alteration for add and drop for:
    //     drop column
    //     renameColumn
    //         drop primaryKey
    //         drop foreign key
    //         drop unique
    //         drop index

    // $table->dropForeign('posts_user_id_foreign');
    // $table->dropIndex('geo_state_index');
    // $table->dropPrimary('users_id_primary');
    // $table->dropUnique('users_email_unique');
    // $table->enum('choices', ['foo', 'bar'])->nullable()->default(['foo', 'bar']);
    // $table->increments('id');
    // $table->renameColumn('from', 'to');
    // $table->rememberToken();
    // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');


    public function dropColum(string $name): void
    {
        if (!empty($name)) {
            $tmp = new Column($name,
                self::ALTER_DROP_COLUMN_TYPE
            );
            $this->setColumns($tmp);
        }
    }

    
    <<-----X----->> alteration raw query in future update <<-----X----->>*/
}
