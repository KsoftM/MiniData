<?php

namespace ksoftm\system\database;

use PDO;
use ksoftm\system\MDQ;
use ksoftm\system\internal\DResult;
use ksoftm\system\internal\BaseQuery;
use ksoftm\system\internal\Column;
use ksoftm\system\utils\datatype\Dictionary;
use ksoftm\system\utils\datatype\ListData;

class QueryBuilder
{

    //<<----------->> CONSTANCE <<----------->>//

    /**
     * Name of the default value of the field
     * 
     * @var string
     */
    protected const DEFAULT_FIELD = '*';


    //<<----------->> BUILDER MODE <<----------->>//

    //* CRUD
    // public const RAW_QUERY_MODE = "RAW QUERY";
    public const SELECT_QUERY_MODE = "SELECT";
    public const INSERT_QUERY_MODE = "INSERT";
    public const UPDATE_QUERY_MODE = "UPDATE";
    public const DELETE_QUERY_MODE = "DELETE";


    //* TABLE
    public const TABLE_CREATE_MODE = "CREATE TABLE";
    public const TABLE_DROP_MODE = "DROP TABLE";
    public const TABLE_ALTER_MODE = "ALTER TABLE";

    //<<-----X----->> BUILDER MODE <<-----X----->>//

    //<<----------->> kinds <<----------->>//

    public const FIELD_TYPE = "FIELD";
    public const FIELD_VALUE_TYPE = "FIELD_VALUES";
    public const SET_TYPE = "SET";
    public const OPTION_TYPE = "OPTION";
    public const ORDER_TYPE = "ORDER";
    public const LIMIT_TYPE = "LIMIT";
    public const WHERE_TYPE = "WHERE";
    public const HAVING_TYPE = "HAVING";
    public const GROUP_TYPE = "GROUP";
    public const COLUMN_TYPE = "COLUMN";
    public const JOIN_TYPE = "JOIN";
    // public const QUERY_TYPE = "QUERY";

    public const PRIMARY_KEY_TYPE = "PRIMARY_KEY";
    public const FOREIGN_KEY_TYPE = "FOREIGN_KEY_TYPE";


    //<<-----X----->> kinds <<-----X----->>//


    //<<-----X----->> CONSTANCE <<-----X----->>//



    //<<----------->> IMPORTANT QUERY CONTENT <<----------->>//

    /**
     * Name of the table
     * 
     * @var string
     */
    protected static string $table;

    /**
     * Name of the database
     * 
     * @var string
     */
    protected static string $database;

    /**
     * SQL Engine name 
     * 
     * @var string
     */
    protected static string $engine;

    /**
     * SQL Charset type
     * 
     * @var string
     */
    protected static string $charset;

    /**
     * SQL Collation type
     * 
     * @var string
     */
    protected static string $collation;

    /**
     * selected query for the current query build.
     *
     * @var string
     */
    protected static ?string $template = null;

    //<<-----X----->> IMPORTANT QUERY CONTENT <<-----X----->>//

    //<<----------->> configuration <<----------->>//

    public static function Config($database, $engine, $charset, $collation)
    {
        if (!empty($database)) {
            self::$database = $database;
        }
        if (!empty($engine)) {
            self::$engine = $engine;
        }
        if (!empty($charset)) {
            self::$charset = $charset;
        }
        if (!empty($collation)) {
            self::$collation = $collation;
        }
    }

    private static function tableEnginContent(): string
    {
        return !empty(self::$engine) && !empty(self::$charset) && !empty(self::$collation)
            ? sprintf(
                "ENGINE = %s DEFAULT CHARACTER SET = %s COLLATE = %s",
                self::$engine,
                self::$charset,
                self::$collation
            )
            : "";
    }

    private static function schemaEnginContent(): string
    {
        return !empty(self::$charset) && !empty(self::$collation)
            ? sprintf(
                "DEFAULT CHARACTER SET %s COLLATE %s ",
                self::$charset,
                self::$collation
            ) : "";
    }

    //<<-----X----->> configuration <<-----X----->>//

    /**
     * make raw query
     *
     * @param string $condition
     * @param array $value
     *
     * @return DResult|false
     */
    public static function query(string $query, array $data = null): DResult|false
    {
        if (empty($query)) return false;

        $pdo = MDQ::getPDO();

        $tmp = null;

        if (
            strpos($query, '?') != false && !empty($data)
        ) {

            foreach ($data as $key => $value) {
                if (strpos($key, ':') == false) {
                    $key = $key . bin2hex(random_bytes(3));
                    $query = implode(":$key", explode('?', $query, 2));
                }
                $tmp[":$key"] = $value;
            }
            $data = !empty($tmp) ? [$tmp] : $data;
        }

        // prepare the base sql fore the pdo
        $pdoStatement = $pdo->prepare($query);

        if (!empty($data)) {
            // check the $returnData have parameter values and bind them
            array_map(function ($data) use (&$pdoStatement) {
                if (is_array($data)) {
                    foreach ($data as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $key => $value) {
                                $pdoStatement->bindValue($key, $value);
                            }
                            continue;
                        }
                        $pdoStatement->bindValue($key, $value);
                    }
                }
            }, $data);
        }

        $pdoStatement->execute();

        $fetchData = $pdoStatement->fetchAll(PDO::FETCH_ASSOC);

        return new DResult(
            $pdo->lastInsertId(),
            $pdoStatement->rowCount(),
            $pdoStatement->nextRowset(),
            $fetchData,
            $pdo,
            $pdoStatement
        );
    }


    public static function Build(
        BaseQuery $query,
        string $table,
        $mode
    ): DResult|false {
        if (!empty($table) && preg_match('/^[.a-zA-Z0-9_]*$/', $table)) {
            self::$table = $table;
        }

        self::setMode($mode);

        if (preg_match_all('/[{][\s\w|\W]*([^}]*)[}]/miU', self::$template, $syntax)) {

            if (
                !empty($syntax) && !is_null($syntax) && $syntax != false
            ) {

                //* map the array to make base query
                $returnData = self::templateMapping($query, $syntax);
                echo '<pre>';
                var_dump(self::$template);
                echo '</pre>';
                // exit;
                // * build the query data
                return self::query(self::$template, $returnData);
            }
        }
        return false;
    }

    protected static function setMode($mode)
    {
        $db_table = sprintf(
            '`%s`.`%s`',
            self::$database,
            self::$table
        );

        switch ($mode) {
                // create table
            case QueryBuilder::TABLE_CREATE_MODE:
                self::$template = sprintf(
                    "create table {%s} %s ({%s}) %s",
                    QueryBuilder::OPTION_TYPE,
                    $db_table,
                    QueryBuilder::COLUMN_TYPE,
                    self::tableEnginContent()
                );
                break;
                // drop table
            case QueryBuilder::TABLE_DROP_MODE:
                self::$template = sprintf(
                    "drop table {%s} %s",
                    QueryBuilder::OPTION_TYPE,
                    $db_table,
                );
                break;
                // select table data
            case QueryBuilder::SELECT_QUERY_MODE:
                self::$template = sprintf(
                    "select {%s} {%s} from %s {%s} {%s} {%s} {%s} {%s} {%s}",
                    QueryBuilder::OPTION_TYPE,
                    QueryBuilder::FIELD_TYPE,
                    $db_table,
                    QueryBuilder::JOIN_TYPE,
                    QueryBuilder::WHERE_TYPE,
                    QueryBuilder::GROUP_TYPE,
                    QueryBuilder::HAVING_TYPE,
                    QueryBuilder::ORDER_TYPE,
                    QueryBuilder::LIMIT_TYPE
                );
                break;
                // insert table data
            case QueryBuilder::INSERT_QUERY_MODE:
                self::$template = sprintf(
                    "insert {%s} into %s {%s}",
                    QueryBuilder::OPTION_TYPE,
                    $db_table,
                    QueryBuilder::SET_TYPE
                );
                break;
                // delete table data
            case QueryBuilder::DELETE_QUERY_MODE:
                self::$template = sprintf(
                    "delete from %s {%s} {%s}",
                    $db_table,
                    QueryBuilder::WHERE_TYPE,
                    QueryBuilder::HAVING_TYPE,
                );
                break;
                // update table data
            case QueryBuilder::UPDATE_QUERY_MODE:
                self::$template = sprintf(
                    "update %s {%s} {%s}",
                    $db_table,
                    QueryBuilder::SET_TYPE,
                    QueryBuilder::WHERE_TYPE,
                );
                break;
            default:
                self::$template = null;
                break;
        }

        return self::$template;
    }


    public static function templateMapping(BaseQuery $query, array $syntax): array
    {
        return array_map(function ($type, $rawType) use ($query) {
            $data = self::loadRender($type, $query);

            if (is_string($data)) {
                self::$template = str_replace($rawType, $data ?? '', self::$template);
                return true;
            }

            if (is_null($data)) {
                self::$template = str_replace($rawType, '', self::$template);
                return false;
            }

            if (is_array($data)) {
                self::$template = str_replace(
                    $rawType,
                    $data[0] ?? '',
                    self::$template
                );
                return $data[1];
            }
        }, $syntax[1], $syntax[0]);
    }

    private static function loadRender(string $type, BaseQuery $query): null|array|string
    {
        if ($type == QueryBuilder::SET_TYPE) {
            return self::setRender($query, $query->getQueryMode());
        } elseif ($type == QueryBuilder::FIELD_TYPE) {
            if (
                $query->getQueryMode() == QueryBuilder::SELECT_QUERY_MODE &&
                is_null(self::fieldRender($query))
            ) {
                return QueryBuilder::DEFAULT_FIELD;
            }
            return self::fieldRender($query);
        } elseif ($type == QueryBuilder::GROUP_TYPE) {
            return self::groupRender($query);
        } elseif ($type == QueryBuilder::LIMIT_TYPE) {
            return self::limitRender($query);
        } elseif ($type == QueryBuilder::ORDER_TYPE) {
            return self::orderRender($query);
        } elseif ($type == QueryBuilder::WHERE_TYPE) {
            return self::whereRender($query);
        } elseif ($type == QueryBuilder::HAVING_TYPE) {
            return self::havingRender($query);
        } elseif ($type == QueryBuilder::OPTION_TYPE) {
            return self::optionRender($query);
        } elseif ($type == QueryBuilder::JOIN_TYPE) {
            return self::joinRender($query);
        } elseif ($type == QueryBuilder::COLUMN_TYPE) {
            return self::columnsRender($query);
        }

        return null;
    }

    protected static function setRender(BaseQuery $query, $mode): ?array
    {
        // get the correct arguments form the query
        $data = $query->argument->getValue(QueryBuilder::SET_TYPE);


        if ($data instanceof ListData) {
            $data = $data->getEach(fn ($val) => $val);

            // get the parameter value set
            $param = array_map(fn ($set) => $set[1], $data);



            // make final process depend on the mode [insert,update]
            if ($mode == QueryBuilder::INSERT_QUERY_MODE) {
                $tmp = array_map(fn ($set) => $set[0], $data);

                $res = array_map(
                    function ($value) {
                        if (strpos($value, ',')) {
                            return explode(',', $value);
                        }
                        return [$value];
                    },
                    $tmp
                );


                $dta = [];
                $name = new ListData;
                foreach ($res as $val) {
                    if (is_array($val)) {
                        $tmp = [];
                        foreach ($val as $value) {
                            if (!empty($value) && is_string($value)) {
                                if (strpos($value, '=')) {
                                    $name->add(trim(explode('=', $value)[0]));
                                    $tmp[] = trim(explode('=', $value)[1]);
                                } else {
                                    $tmp[] = trim($value);
                                }
                            }
                        }
                        $dta[] = sprintf('(%s)', implode(', ', $tmp));
                    }
                }

                $name = $name->getEach(fn ($val) => $val);

                $output = [
                    trim(
                        sprintf(
                            '%s value %s',
                            !empty($name) ? sprintf('(%s)', implode(', ', $name)) : '',
                            trim(implode(',', $dta))
                        )
                    ),
                    $param
                ];
            } elseif ($mode == QueryBuilder::UPDATE_QUERY_MODE) {
                $tmp = array_map(fn ($set) => $set[0], $data);
                $output = [
                    sprintf('set %s', implode(', ', $tmp)),
                    $param
                ];
            }
        }
        return $output ?? null;
    }

    protected static function fieldRender(BaseQuery $query): ?string
    {
        $output = $query->argument->getValue(QueryBuilder::FIELD_TYPE);

        if ($output instanceof ListData) {
            $output = implode(', ', $output->getEach(fn ($v) => $v));

            return !empty(trim($output)) ? trim($output) : null;
        }

        return null;
    }

    protected static function groupRender(BaseQuery $query): ?string
    {
        $output = $query->argument->getValue(QueryBuilder::GROUP_TYPE);

        if (!empty($output)) {
            return trim('group by ' . implode(', ', is_array($output) ?: [$output]));
        }
        return null;
    }

    protected static function limitRender(BaseQuery $query): ?string
    {
        [$count, $limit] = $query->argument->getValue(QueryBuilder::LIMIT_TYPE);

        $output = '';

        if (!is_null($count) && !empty($count)) {
            $output = "limit $count";
            if (!empty($limit)) {
                $output .= ", $count";
            }
        }

        return !empty(trim($output)) ? trim($output) : null;
    }

    protected static function orderRender(BaseQuery $query): ?string
    {
        $output = $query->argument->getValue(QueryBuilder::ORDER_TYPE);
        if ($output instanceof Dictionary) {
            // $output = array_map(fn ($field) => implode(
            //     ' ',
            //     $field
            // ), array_values($output));

            $output = $output->getEach(function ($key, $val) {
                return "$key $val";
            });

            $output = implode(', ', $output);

            return !empty(trim($output)) ? trim("order by $output") : null;
        }

        return null;
    }

    private static function condition(
        BaseQuery $query,
        string $type
    ): ?array {
        return !empty($query->argument->getValue($type)) ?
            $query->argument->getValue($type) : null;
    }

    private static function whereRender(BaseQuery $query): ?array
    {
        return self::condition($query, QueryBuilder::WHERE_TYPE);
    }

    private static function havingRender(BaseQuery $query): ?array
    {
        return self::condition($query, QueryBuilder::HAVING_TYPE);
    }

    protected static function optionRender(BaseQuery $query): ?string
    {
        $output = $query->argument->getValue(QueryBuilder::OPTION_TYPE);

        if (!empty($output)) {
            if (array_key_exists($query->getQueryMode(), $output)) {
                return trim($output[$query->getQueryMode()]);
            }
        }

        return null;
    }

    public static function joinRender(BaseQuery $query): string
    {
        // select employee.id,employee.first_name,branch.branch_name
        // from employee -- all data
        // [left] join [branch] on [employee.id] = [branch.mgr_id];

        $template = "{joinType} join {mainTable} on {foreignKey} = {mainKey}";

        $d = [
            'joinType' => $joinType,
            'mainTable' => $mainTable,
            'foreignKey' => $foreignKey,
            'mainKey' => $mainKey
        ] = $query->argument->getValue(QueryBuilder::JOIN_TYPE);

        if (!empty($d)) {
            if (!empty($joinType)) {
                $template = str_replace('{joinType}', $joinType, $template);
            } else {
                $template = str_replace('{joinType}', '', $template);
            }

            if (!empty($mainTable)) {
                $template = str_replace('{mainTable}', $mainTable, $template);
            }

            if (!empty($foreignKey)) {
                $template = str_replace('{foreignKey}', $foreignKey, $template);
            } else {
                $template = str_replace('{foreignKey}', self::$table . '.id', $template);
            }

            if (!empty($mainKey)) {
                $template = str_replace('{mainKey}', $mainKey, $template);
            }
        } else {
            $template = '';
        }
        return trim($template);
    }

    public static function columnsRender(BaseQuery $query): string
    {
        $coll = $query->argument->getValue(QueryBuilder::COLUMN_TYPE);

        if ($coll instanceof ListData) {

            $coll = $coll->getEach(function (Column $list) {
                $cols[] = self::colRender($list);

                // get all the primary key id
                $pKey = $list->argument->getValue('primaryKey');
                if (!empty($pKey)) {
                    $primaryKey[] = trim($pKey);
                }

                // get all the key id
                $nKey = $list->argument->getValue('key');
                if (!empty($nKey)) {
                    $normalKey[] = trim($nKey);
                }

                // get all the index
                $iKey = $list->argument->getValue('index');
                if (!empty($iKey)) {
                    $indexKey[] = trim($iKey);
                }

                //  create table branch (
                //  id int primary key,
                //  branch_name varchar(40),
                //  mgr_id int,
                //  mgr_start_date date,
                //  foreign key(mgr_id) references employee(id) on delete set null
                // );
                $f = $list->argument->getValue('foreignKey');

                if ($f instanceof Dictionary) {
                    $fKey = $f->getValue('foreignKey');

                    if (strpos($fKey, '.')) {
                        $fKey = explode('.', $fKey);
                        $fKey = sprintf('%s(%s)', $fKey[0], $fKey[1]);
                    } else {
                        $fKey  =  sprintf('%s(%s)', $fKey, $list->argument->getValue('name')[0]);
                    }
                    $f->removeKey('foreignKey');
                    $f->add('foreignKey', $fKey);
                    $foreignKey = $f;
                }

                if (!empty($primaryKey)) {
                    $cols[] = sprintf("primary key (%s)", implode(',', $primaryKey));
                }

                if (!empty($normalKey)) {
                    foreach ($normalKey as $value) {
                        $cols[] = sprintf("key %s (%s)", "idx_fk_$value", $value);
                    }
                }

                if (!empty($indexKey)) {
                    foreach ($indexKey as $value) {
                        $cols[] = sprintf("index %s (%s)", "idx_$value", $value);
                    }
                }

                // KEY `idx_fk_country_id` (`country_id`), CONSTRAINT `fk_city_country` 
                if (!empty($foreignKey)) {
                    $cols[] = sprintf(
                        "foreign key(%s) references %s.%s %s %s",
                        $foreignKey->getValue('mainKey'),
                        self::$database,
                        $foreignKey->getValue('foreignKey'),
                        $foreignKey->getValue('onDelete'),
                        $foreignKey->getValue('onUpdate'),

                    );
                }
                return PHP_EOL . implode("," . PHP_EOL, $cols);
            });
            return implode(",", $coll);
        }
        return '';
    }

    private static function colRender(Column $col)
    {
        // $template = '{name} {datatype} {nullable}{default}{autoIncrement}{unsigned}{unique}';
        $template = '{name} {datatype} {unsigned}{nullable}{autoIncrement}{unique}{default}';

        $tmp = $col->argument->getValue('name');
        if (!empty($tmp)) {
            $template = str_replace('{name}', "`$tmp`", $template);
        } else {
            $template = str_replace('{name}', '', $template);
        }

        $type = $tmp = $col->argument->getValue('datatype');
        if (!empty($tmp)) {
            $template = str_replace('{datatype}', $tmp, $template);
        } else {
            $template = str_replace('{datatype}', '', $template);
        }

        $tmp = $col->argument->getValue('unique');
        if (!empty($tmp)) {
            $template = str_replace(
                '{unique}',
                " $tmp ",
                $template
            );
        } else {
            $template = str_replace('{unique}', '', $template);
        }

        $tmp = $col->argument->getValue('autoIncrement');
        if (!empty($tmp)) {
            $template = str_replace('{autoIncrement}', " $tmp ", $template);
        } else {
            $template = str_replace('{autoIncrement}', '', $template);
        }

        $tmp = $col->argument->getValue('unsigned');
        $numeric = [
            RawQuery::DATA_TYPE_TINYINT,
            RawQuery::DATA_TYPE_SMALLINT,
            RawQuery::DATA_TYPE_MEDIUMINT,
            RawQuery::DATA_TYPE_INTEGER,
            RawQuery::DATA_TYPE_BIGINT,
            RawQuery::DATA_TYPE_FLOAT,
            RawQuery::DATA_TYPE_DOUBLE,
            RawQuery::DATA_TYPE_DECIMAL
        ];
        if (!empty($tmp) && in_array($type, $numeric)) {
            $template = str_replace('{unsigned}', " $tmp ", $template);
        } else {
            $template = str_replace('{unsigned}', '', $template);
        }

        $tmp = $col->argument->getValue('nullable');
        if (!empty($tmp)) {
            $template = str_replace('{nullable}', " $tmp ", $template);
        } else {
            $template = str_replace('{nullable}', 'not null', $template);
        }

        $tmp = $col->argument->getValue('default');
        if (!empty($tmp)) {
            $template = str_replace('{default}', " default $tmp ", $template);
        } else {
            $template = str_replace('{default}', '', $template);
        }

        return trim($template);
    }
}
