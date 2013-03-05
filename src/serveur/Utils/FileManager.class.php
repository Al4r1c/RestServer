<?php
    namespace Serveur\Utils;

    use Serveur\Lib\Fichier;

    class FileManager {
        /**
         * @return \Serveur\Lib\Fichier
         */
        public static function getFichier() {
            $fileSystem = new \Serveur\Lib\FileSystem();
            $fileSystem->initialiser(php_uname('s'), BASE_PATH);

            $fichier = new Fichier();
            $fichier->setFileSystem($fileSystem);

            return $fichier;
        }
    }