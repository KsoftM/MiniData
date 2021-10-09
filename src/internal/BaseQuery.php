<?php

namespace ksoftm\system\internal;

use Closure;
use ksoftm\system\database\QueryBuilder;
use ksoftm\system\utils\datatype\Dictionary;

/**
 * This class is the base for RawQuery and Query
 */
abstract class BaseQuery
{
    public Dictionary $argument;
    //<<----------->> Attributes <<----------->>//

    protected string $mode = QueryBuilder::SELECT_QUERY_MODE;

    //<<-----X----->> Attributes <<-----X----->>//

    /**
     * Class constructor.
     */
    public function __construct(Closure $callback = null, $mode)
    {
        $this->argument = new Dictionary();
        $this->mode = $mode;

        if (!is_null($callback)) {
            $callback($this);
        }
    }

    public static function new(Closure $callback = null, $mode): BaseQuery
    {
        return new BaseQuery($callback, $mode);
    }

    /**
     * get the query mode for the build
     *
     * @return string|null
     */
    public function getQueryMode(): ?string
    {
        return $this->mode;
    }

    /**
     * option for query.
     * only one query is allowed. 
     * the first one is replaced by second one.
     * 
     * @example test $query->option('if exist');
     * @example test $query->option('if not exist');
     *
     * @param string|array $option
     *
     * @return self
     */
    public function option(string|array $option): self
    {
        if (empty($option)) return $this;

        if (is_string($option) && strpos($option, ',')) {
            $option = explode(',', $option);
        }

        if (is_array($option)) {
            foreach ($option as $value) {
                $this->argument->add(QueryBuilder::OPTION_TYPE, [$this->getQueryMode() => trim($value)]);
                return $this;
            }
        }

        $this->argument->add(QueryBuilder::OPTION_TYPE, [$this->getQueryMode() => trim($option)]);
        return $this;
    }

    /**
     * if exists option for query
     *
     * @return self
     */
    public function ifExists(): self
    {
        $this->option('if exists');
        return $this;
    }

    /**
     * if exists option for query
     *
     * @return self
     */
    public function ifNotExists(): self
    {
        $this->option('if not exists');
        return $this;
    }
}
