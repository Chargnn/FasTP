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

    /**
     * Convert epoch to Y-m-d H:i:s date
     * @param $epoch
     * @return string
     * @throws \Exception
     */
    public static function formatDate($epoch){
        $dt = new \DateTime("@$epoch");
        return $dt->format('Y-m-d H:i:s');
    }

    /**
     * Check to see if given path is a directory
     * @param $path
     * @param $conn
     * @return bool
     */
    public static function isDir($conn, $path){
        return ftp_size($conn, $path) === -1;
    }

    /**
     * Check to see if file ends with given extension
     * @param $file
     * @param $extension
     * @return bool
     */
    public static function isFileExtension($file, $extension){
        return ends_with($file, $extension);
    }

    /**
     * Get file content to string
     * @param $ftp
     * @param $filename
     * @return bool|string
     */
    public static function getFileToString($conn, $file) {
        $temp = fopen('php://temp', 'r+');
        ftp_pasv($conn, true);
        if (@ftp_fget($conn, $temp, $file, FTP_BINARY, 0)) {
            rewind($temp);
            return stream_get_contents($temp);
        }
        else {
            return false;
        }
    }

    /**
     * Search a file recursively through all the ftp directories
     * @param $conn
     * @param $path
     * @param $file
     * @return mixed
     */
    public static function searchFile($conn, $search, $dirs = []){
        if($dirs){
            $dir = $dirs[0];
            unset($dirs[0]);
            ftp_chdir($conn, $dir);

            $files = ftp_nlist($conn, $dir);
            foreach($files as $file){
                if($file === $search)
                    return $dir;

                if(Ftp::isDir($conn, $file)){
                    $dirs[] = $file;
                }
            }
        } else {
            $files = ftp_nlist($conn, '/');
            foreach($files as $file){
                if($file === $search)
                    return '/';

                if(Ftp::isDir($conn, $file)){
                    $dirs[] = $file;
                }
            }
        }

        self::searchFile($conn, $search, $dirs);
    }

    /** protected to prevent cloning */
    protected function __clone(){}

    /** protected to prevent instantiation from outside of the class */
    protected function __construct(){}
}