<?php
    namespace Serveur\Utils;

    class Constante
    {

        private static $_extension = 'php';

        /**
         * @param string $nomConfig
         * @return mixed
         */
        public static function chargerConfig($nomConfig)
        {
            $fichier = FileManager::getFichier();
            $fichier->setFichierParametres($nomConfig . '.' . self::$_extension, '/public/constantes');

            return $fichier->chargerFichier();
        }
    }