<?php

namespace ksoftm\system\database\connection;

use ksoftm\system\internal\IDataDrive;

class MySQLDataDrive implements IDataDrive
{
    protected static ?string $host = null;
    protected static ?string $port = null;
    protected static ?string $username = null;
    protected static ?string $password = null;
    protected static ?string $database = null;

    /**
     * class construct
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param int $port
     * @param string $database
     */
    public function __construct(string $host, string  $username, string  $password, int $port, string  $database)
    {
        self::$host = $host;
        self::$username = $username;
        self::$password = $password;
        self::$port = $port;
        self::$database = $database;
    }

    public static function getConnectionString(): string
    {
        return sprintf(
            "mysql:host=%s;port=%d;dbname=%s",
            self::$host,
            self::$port,
            self::$database,
        );
    }

    public static function getConfidential(): array
    {
        return ['username' => self::$username ?? '', 'password' => self::$password ?? ''];
    }

    public function getDatabase(): string
    {
        return self::$database;
    }
}
