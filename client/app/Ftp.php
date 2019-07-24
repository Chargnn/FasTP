<?php

namespace App;

class Ftp
{
    /**
     * Create connexion
     * @param array|null $connValues
     * @return null
     */
    public static function connect(array $connValues = null){
        return ftp_connect($connValues['host'], $connValues['port'] ?: 21);
    }

    public static function disconnect($conn){
        ftp_close($conn);
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
    public static function searchFile($conn, $search){
        $dirs = Ftp::recursiveListDirectories($conn, '/');

        foreach($dirs as $dir){
            try {
                ftp_chdir($conn, $dir);
                $files = ftp_nlist($conn, $dir);

                foreach($files as $file){
                    if($file === $search){
                        return $dir;
                    }
                }
            } catch(\Exception $e){

            }
        }
    }

    /**
     * Get all files recursively on ftp server
     * @param $conn
     * @param $path
     * @return array
     */
    public static function recursiveListDirectories($conn, $path)
    {
        $files = ftp_nlist($conn, $path);

        $result = array();

        foreach ($files as $file)
        {
            $filepath = $path . '/' . $file;

            if (Ftp::isDir($conn, $file) && $file !== '.' && $file !== '..')
            {
                $result = array_merge($result, self::recursiveListDirectories($conn, $filepath));
            }
            else
            {
                if(!in_array($path, $result))
                    $result[] = $path;
            }
        }

        return $result;
    }
}