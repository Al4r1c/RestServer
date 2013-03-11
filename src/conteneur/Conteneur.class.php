<?php
    namespace Conteneur;

    class Conteneur
    {
        /**
         * @var string[]
         */
        protected $_conteneur;

        public function __construct()
        {
            $this->buildConteneur();
        }

        /**
         * @return string[]
         */
        public function getConteneur()
        {
            return $this->_conteneur;
        }

        private function buildConteneur()
        {
            $conteneur = new \Pimple();

            $conteneur['RequeteManager'] = function ($c) {
                $restRequete = new \Serveur\Requete\RequeteManager();
                $restRequete->setServer($c['Server']);

                return $restRequete;
            };

            $conteneur['Server'] = function () {
                $server = new \Serveur\Requete\Server\Server();
                $server->setVarServeur($_SERVER);

                return $server;
            };


            $conteneur['TraitementManager'] = function ($c) {
                $traitementManager = new \Serveur\Traitement\TraitementManager();
                $traitementManager->setRouteMap($c['RouteMap']);

                return $traitementManager;
            };

            $conteneur['RouteMap'] = function () {
                $fichier = \Serveur\Utils\FileManager::getFichier();
                $fichier->setFichierParametres('routemap.yaml', '/config');

                $routeManager = new \Serveur\Traitement\Route\RouteMap();
                $routeManager->chargerFichierMapping($fichier);

                return $routeManager;
            };


            $conteneur['ReponseManager'] = function ($c) {
                $restReponse = new \Serveur\Reponse\ReponseManager();
                $restReponse->setConfig($c['Config']);
                $restReponse->setHeader($c['Header']);

                return $restReponse;
            };

            $conteneur['Config'] = function () {
                $fichier = \Serveur\Utils\FileManager::getFichier();
                $fichier->setFichierParametres('config.yaml', '/config');
                $configurationManager = new \Serveur\Reponse\Config\Config();
                $configurationManager->chargerConfiguration($fichier);

                return $configurationManager;
            };

            $conteneur['Header'] = function () {
                return new \Serveur\Reponse\Header\Header();
            };


            $conteneur['ErreurManager'] = $conteneur->share(
                function ($c) {
                    $errorManager = new \Serveur\GestionErreurs\ErreurManager();
                    $errorManager->setErrorHandler($c['ErreurHandler']);

                    return $errorManager;
                }
            );

            $conteneur['ErreurHandler'] = function () {
                return new \Serveur\GestionErreurs\Handler\ErreurHandler();
            };

            $this->_conteneur = $conteneur;
        }

        /**
         * @return \Serveur\Requete\RequeteManager
         */
        public function getRequeteManager()
        {
            return $this->_conteneur['RequeteManager'];
        }

        public function getTraitementManager()
        {
            return $this->_conteneur['TraitementManager'];
        }

        /**
         * @return \Serveur\Reponse\ReponseManager
         */
        public function getReponseManager()
        {
            return $this->_conteneur['ReponseManager'];
        }

        /**
         * @return \Serveur\GestionErreurs\ErreurManager
         */
        public function getErrorManager()
        {
            return $this->_conteneur['ErreurManager'];
        }
    }