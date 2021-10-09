<?php

namespace ksoftm\system;

use PDO;
use ksoftm\system\database\PDO_DB;
use ksoftm\system\internal\IDataDrive;
use ksoftm\system\database\QueryBuilder;

/**
 * Mini Data Query
 */


class MDQ
{
    public static IDataDrive $dataDrive;
    private static ?PDO $pdo = null;

    // /**
    //  * Class constructor.
    //  */
    // private function __construct()
    // {
    // }

    public static function Config(IDataDrive $dataDrive, $engin, $charSet, $collation)
    {
        self::$dataDrive = $dataDrive;

        QueryBuilder::Config($dataDrive->getDatabase(), $engin, $charSet, $collation);
    }

    public static function getPDO(): PDO
    {
        if (empty(self::$pdo)) {
            self::$pdo = PDO_DB::connect(self::$dataDrive);
        }

        return self::$pdo;
    }
}
