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

    /** protected to prevent cloning */
    protected function __clone(){}

    /** protected to prevent instantiation from outside of the class */
    protected function __construct(){}
}