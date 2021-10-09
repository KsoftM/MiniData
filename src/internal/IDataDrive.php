<?php

namespace ksoftm\system\internal;


interface IDataDrive
{

    /**
     * this function will return the string of sql connection link
     *
     * @return string
     */
    static function getConnectionString(): string;


    /**
     * this function will return connection configuration
     *
     * @return array
     */
    static function getConfidential(): array;

    /**
     * this function will return the current database
     *
     * @return string
     */
    function getDatabase(): string;
}
