<?php

namespace ksoftm\system\internal;

use PDO;
use PDOStatement;

class DResult
{
    /** @var null|string $data last inserted id. */
    protected ?string $insertId = null;

    /** @var null|int $data effected row count. */
    protected ?int $rowCount = null;

    /** @var null|int $data the next rowset in a multi-rowset statement. */
    protected ?bool $nextRowSet = null;

    /** @var array|false $data associative array of data result. */
    protected array|false $data = false;

    /** @var array|false $data associative array of data result. */
    protected PDO|false $pdo = false;

    /** @var array|false $data associative array of data result. */
    protected PDOStatement|false $pdoStatement = false;

    /**
     * Class constructor.
     */
    public function __construct(
        string $insertId,
        int $rowCount,
        bool $nextRowSet,
        array|false $data,
        PDO $pdo,
        PDOStatement $pdoStatement
    ) {
        $this->insertId = $insertId;
        $this->rowCount = $rowCount;
        $this->nextRowSet = $nextRowSet;
        $this->data = $data;
        $this->pdo = $pdo;
        $this->pdoStatement = $pdoStatement;
    }

    /**
     * last inserted id.
     *
     * @return integer
     */
    public function getInsertedID(): int
    {
        return $this->insertId;
    }

    /**
     * effected row count in int.
     *
     * @return integer
     */
    public function rowCount(): int
    {
        return $this->rowCount;
    }

    /**
     * the next rowset in a multi-rowset statement.
     *
     * @return boolean
     */
    public function getNextRowset(): bool
    {
        return $this->nextRowSet;
    }

    /**
     * associative array of data result.
     *
     * @return array|false
     */
    public function getData(): array|false
    {
        return $this->data;
    }

    public function getPDO(): PDO|false
    {
        return $this->pdo;
    }

    public function getPDOStatement(): PDOStatement|false
    {
        return $this->pdoStatement;
    }
}
