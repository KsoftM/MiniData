<?php

namespace ksoftm\system\database;

use ksoftm\system\internal\BaseQuery;
use ksoftm\system\utils\datatype\Dictionary;
use ksoftm\system\utils\datatype\ListData;

/**
 * This class ues to create, insert, update, delete, join, ect... the table
 */
class Query extends BaseQuery
{
    /**
     * field query for selection
     * 
     * @example test $query->field('users.id');
     * @example test $query->field('users.id, employee.id');
     * @example test $query->field('id, name, age');
     * @example test $query->field(['user.id, staff.id']);
     *
     * @param string|array $field
     *
     * @return Query
     */
    public function field(string|array $field): Query
    {
        if (empty($field)) return $this;

        if (is_string($field) && strstr($field, ',')) {
            $field = explode(',', $field);
        }

        if (is_array($field)) {
            foreach ($field as $value) {
                $this->field($value);
            }
            return $this;
        }
        $d = $this->argument->getValue(QueryBuilder::FIELD_TYPE) ?: new ListData;
        if ($d instanceof ListData) {
            $d->add($field);
            $this->argument->add(QueryBuilder::FIELD_TYPE, $d);
        }
        return $this;
    }

    /**
     * set query for the insert and update
     * 
     * @example test $query->set(['first_name' => "sam"]);
     * @example test $query->set(['age' => 12, 'name' => 'sam']);
     *
     * @param string|array $field
     *
     * @return Query
     */
    public function set(array $field): Query
    {
        if (empty($field)) return $this;

        // $query->set(['first_name' => "sam"]);
        // $query->set(['age' => 12, 'name' => 'sam']);
        if (is_array($field)) {
            $tmp = [];
            $out = [];

            foreach ($field as $key => $value) {
                if (is_array($value)) {
                    $this->set($value);
                } else {
                    $k = ':' . bin2hex(random_bytes(3));
                    if (!is_numeric($key)) {
                        $out[] = trim($key) . " = $k";
                    } else {
                        $out[] = $k;
                    }
                    $tmp[$k] = is_string($value) ? trim($value) : $value;
                }
            }
            $d = $this->argument->getValue(QueryBuilder::SET_TYPE) ?: new ListData;
            if ($d instanceof ListData) {
                if (!empty($tmp) && !empty($out)) {
                    $d->add([implode(', ', $out), $tmp]);

                    if (!empty($out) && !empty($tmp)) {
                        $this->argument->add(
                            QueryBuilder::SET_TYPE,
                            $d
                        );
                    }
                }
            }
        }


        return $this;
    }

    /**
     * order the queries by asc or desc
     * 
     * @example test $query->orderBy('id');
     * @example test $query->orderBy('id desc');
     * 
     * @example test $query->orderBy('id, name');
     * @example test $query->orderBy('id, name desc');
     * 
     * @example test $query->orderBy(['id asc, name asc']);
     * @example test $query->orderBy(['id asc, name']);
     * 
     * @example test $query->orderBy('first_name', false);
     * @example test $query->orderBy('id', true);
     *
     * @param string|array $order
     * @param bool|string $asc
     *
     * @return Query
     */
    public function orderBy(string|array $order, bool|string $asc = null): Query
    {
        // $query->orderBy('id, name');
        // $query->orderBy('id, name desc');
        if (is_string($order) && strpos($order, ',') != false) {
            $order = explode(',', $order);
        }

        if (is_array($order) && empty($asc)) {
            foreach ($order as $value) {
                $this->orderBy(trim($value));
            }
            return $this;
        }


        // $query->orderBy('id');
        // $query->orderBy('id desc');
        if (is_string($order) && strpos(trim($order), ' ') != false && empty($asc)) {
            [$od, $sort] = explode(' ', trim($order));
            if (in_array($sort, ['asc', 'desc'], true)) {
                $this->orderBy(trim($od), trim($sort));
            }
            return $this;
        }

        $asc = (is_bool($asc) && $asc === true) ? 'asc' : 'desc';
        // $asc = (empty($asc)) ? 'asc' : $asc;

        $d = $this->argument->getValue(QueryBuilder::ORDER_TYPE) ?: new Dictionary;
        if ($d instanceof Dictionary) {
            $d->add(trim($order), trim($asc));
            $this->argument->add(QueryBuilder::ORDER_TYPE, $d);
        }

        return $this;
    }

    /**
     * limit and offset for query. 
     * only one query is allowed. 
     * the first query is replaced by the second one.
     *
     * @example test $query->limit(10, 20); 
     * @example test $query->limit(10); 
     *
     * @param integer $count
     * @param integer $offset
     *
     * @return Query
     */
    public function limit(int $count, int $offset = null): Query
    {
        if (is_null($count) || $count <= 0) {
            $count = 1;
        }

        $this->argument->add(
            QueryBuilder::LIMIT_TYPE,
            [$count, $offset]
        );

        return $this;
    }

    /**
     * where conditions for query, use "?" to specify the parameters
     * 
     * @example test $query->where('id = ?', [10]);
     * @example test $query->where('name like ?', ['sam']);
     * @example test $query->where('age > ? and first_name = ? or last_name = ?', [12,'sam','will']);
     *
     * @param string $condition
     * @param array $value
     *
     * @return Query
     */
    public function where(string $condition, array $value): Query
    {
        return $this->condition($condition, $value, QueryBuilder::WHERE_TYPE);
    }

    /**
     * having conditions for query, use "?" to specify the parameters
     * 
     * @example test $query->having('id = ?', [10]);
     * @example test $query->having('name like ?', ['sam']);
     * 
     *
     * @param string $condition
     * @param array $value
     *
     * @return Query
     */
    public function having(string $condition, array $value): Query
    {
        return $this->condition($condition, $value, QueryBuilder::HAVING_TYPE);
    }

    /**
     * make conditions query
     *
     * @param string $condition
     * @param array $value
     * @param string $kind
     *
     * @return Query
     */
    protected function condition(
        string $condition,
        array $value = null,
        string $kind = null
    ): Query {

        if (empty($condition)) return $this;

        $tmp = null;

        if (strpos($condition, '?') != false && !empty($value)) {
            foreach ($value as $key => $value) {
                $key = $key . bin2hex(random_bytes(3));
                $condition = implode(":$key", explode('?', $condition, 2));
                $tmp[":$key"] = $value;
            }
        }

        $this->argument->add($kind, ["$kind $condition", $tmp]);

        return $this;
    }


    /**
     * group selection for quey 
     * 
     * @example test $query->group('id');
     * @example test $query->group('id,name');
     * @example test $query->group(['id', 'name']);
     *
     * @param string|array $fields
     *
     * @return Query
     */
    public function group(string|array $fields): Query
    {
        if (empty($fields)) return $this;

        if (is_string($fields) && strpos($fields, ',')) {
            $fields = explode(',', $fields);
        }

        if (is_array($fields)) {
            foreach ($fields as $value) {
                $this->argument->add(QueryBuilder::GROUP_TYPE, trim($value));
            }
            return $this;
        }

        $this->argument->add(QueryBuilder::GROUP_TYPE, trim($fields));

        return $this;
    }

    /**
     * join for query
     * 
     * @example test $query->join('branch');
     * @example test $query->join('branch.id');
     * 
     * @example test $query->join('branch, employee');
     * @example test $query->join('branch.id, employee.id');
     * 
     * @example test $query->join('branch','employee');
     * @example test $query->join('branch.id','employee.id');
     *
     * @param string $foreignKey
     * @param string|null $mainKey
     * @param string|null $joinType
     *
     * @return Query
     */
    public function join(string $foreignKey, string $mainKey = null, string $joinType = null): Query
    {

        $tmp['joinType'] = $joinType ?? '';

        if (empty($foreignKey)) return $this;

        if (empty($mainKey)) {

            $foreignKey  = trim($foreignKey);

            // $query->join('branch, employee');
            // $query->join('branch.id, employee.id');
            if (strstr($foreignKey, ',')) {
                $foreignKey = explode(',', $foreignKey);
            }

            if (is_array($foreignKey)) {
                $this->join($foreignKey[0], $foreignKey[1]);
                return $this;
            }

            // $query->join('branch');
            // $query->join('branch.id');
            if (is_string($foreignKey)) {
                if (strpos($foreignKey, '.')) {
                    $tmp['table'] = explode('.', $foreignKey)[0];
                    $tmp['mainKey'] = $foreignKey;
                } else {
                    $tmp['mainTable'] = $foreignKey;
                    $tmp['mainKey'] = "$foreignKey.id";
                }
                $tmp['foreignKey'] = null;
            }
        } else {
            // $query->join('branch', 'employee');
            // $query->join('branch.id', 'employee.id');

            $foreignKey  = trim($foreignKey);
            $mainKey  = trim($mainKey);

            $tmp['mainTable'] = explode('.', $foreignKey)[0];

            $tmp['foreignKey'] = strpos($foreignKey, '.') ? $foreignKey : "$foreignKey.id";
            $tmp['mainKey'] = strpos($mainKey, '.') ? $mainKey : "$mainKey.id";
        }

        $this->argument->add(QueryBuilder::JOIN_TYPE, $tmp);

        return $this;
    }
}
