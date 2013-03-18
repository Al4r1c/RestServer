<?php
    namespace Serveur\Traitement\Data;

    use Serveur\Lib\ObjetReponse;

    interface IDatabaseActions
    {
        /**
         * @param DatabaseConfig $databaseInformations
         */
        public function ouvrirConnectionDepuisFichier($databaseInformations);

        /**
         * @param resource $connection
         * @return bool
         */
        public function fermerConnection($connection);

        /**
         * @param string $table
         * @param array $filtres
         * @return ObjetReponse
         */
        public function recuperer($table, $filtres = array());

        /**
         * @param string $table
         * @param array $champs
         * @return ObjetReponse
         */
        public function inserer($table, $champs);

        /**
         * @param string $table
         * @param string $idObjet
         * @param array $champs
         * @return ObjetReponse
         */
        public function insererIdempotent($table, $idObjet, $champs);

        /**
         * @param string $table
         * @param array $champs
         * @param array $filtres
         * @return ObjetReponse
         */
        public function mettreAJour($table, $champs, $filtres = array());

        /**
         * @param string $table
         * @param $filtres
         * @return ObjetReponse
         */
        public function supprimer($table, $filtres);
    }