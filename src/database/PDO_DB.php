<?php

namespace ksoftm\system\database;

use PDO;
use Exception;
use PDOException;
use ksoftm\system\internal\IDataDrive;

/**
 * This is the main PDO Data connection
 *
 * @author KsoftM
 */
class PDO_DB
{

    /**
     * this static function will return the PDO instance
     *
     * @param IDataDrive $dataDrive
     *
     * @return PDO|null
     */

    public static function connect(IDataDrive $dataDrive): ?PDO
    {
        $handler = null;
        $conn = $dataDrive->getConnectionString();
        $confidential = $dataDrive->getConfidential();
        if (!empty($conn) && !empty($confidential)) {
            $options = [
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];
            try {
                $handler = new PDO(
                    $conn,
                    $confidential['username'],
                    $confidential['password'],
                    $options
                );
            } catch (PDOException $exc) {
                throw new Exception('PDO connection failed.');
            }
        }
        return $handler;
    }
}
