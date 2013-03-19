<?php
namespace Serveur\Utils;

use Serveur\Lib\Fichier;
use Serveur\Lib\FileSystem;

class FileManager
{
    /**
     * @return \Serveur\Lib\Fichier
     */
    public static function getFichier()
    {
        $fileSystem = new FileSystem();
        $fileSystem->initialiser(php_uname('s'), BASE_PATH);

        $fichier = new Fichier();
        $fichier->setFileSystem($fileSystem);

        return $fichier;
    }
}