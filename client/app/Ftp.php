<?php

namespace App;

class Ftp
{
    /** Keep ftp connexion */
    protected static $instance = null;

    /**
     * Create instance
     * @param array|null $connValues
     * @return null
     */
    public static function instance(array $connValues = null){
        if (static::$instance === null && $connValues !== null){
            static::$instance = ftp_connect($connValues['host'], $connValues['port'] ?: 21);
        }
        return static::$instance;
    }

    /**
     * Format bytes to mb, gb ...
     * @param $bytes
     * @param int $precision
     * @return string
     */
    public static function formatSize($bytes, $precision = 2){
        $units = array('B', 'KiB', 'MiB', 'GiB', 'TiB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /** protected to prevent cloning */
    protected function __clone(){}

    /** protected to prevent instantiation from outside of the class */
    protected function __construct(){}
}