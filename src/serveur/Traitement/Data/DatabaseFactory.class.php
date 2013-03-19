<?php
    namespace Serveur\Traitement\Data;

    class DatabaseFactory
    {
        /**
         * @param $nomDriverDatabase
         * @return AbstractDatabase
         */
        public function getConnexionDatabase($nomDriverDatabase)
        {
            if (
                class_exists($nomClasseDatabase =
                '\\Serveur\\Traitement\\Data\\Drivers\\Database' . ucfirst(strtolower($nomDriverDatabase))
                )
            ) {
                return new $nomClasseDatabase();
            } else {
                return false;
            }
        }
    }