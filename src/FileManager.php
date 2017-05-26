<?php

namespace ParserSeo;

use Exception;

class FileManager
{
    const FILE_DIR = 'files/';

    /**
     * Return full file name for domain.
     *
     * @param $domain
     *
     * @return string
     */
    public function getFileName($domain)
    {
        $filePath = __DIR__.'/../'.self::FILE_DIR.$domain.'.csv';

        return $filePath;
    }

    /**
     * Try create file and throw Exception if error given.
     *
     * @throws Exception
     */
    public function checkDirPermission()
    {
        $filename = $this->getFileName('test');

        $file = fopen($filename, 'w+');
        if ($file === false) {
            throw new Exception('Directory '.dirname($filename)." haven't permission for read and write.");
        }
        fclose($file);
        unlink($filename);
    }

    /**
     * Create csv file with domain name and write into it pages info.
     *
     * @param array $arrayOfObjects
     * @param $domain
     *
     * @return bool|string
     */
    public function saveArrayOfObjectsToFile(array $arrayOfObjects, $domain)
    {
        if (empty($domain) || empty($arrayOfObjects)) {
            return false;
        }

        $filePath = $this->getFileName($domain);
        $file = fopen($filePath, 'w');

        $headerArray = array_keys(get_object_vars($arrayOfObjects[0]));
        fputcsv($file, $headerArray);

        foreach ($arrayOfObjects as $object) {
            $objectValues = array_values(get_object_vars($object));
            fputcsv($file, $objectValues);
        }

        fclose($file);

        return $filePath;
    }

    /**
     * Check if exists file report for domain and return file path or false.
     *
     * @param $domain
     *
     * @return bool|string
     */
    public function findFileByDomain($domain)
    {
        $filename = $this->getFileName($domain);
        if (file_exists($filename)) {
            return $filename;
        }

        return false;
    }
}