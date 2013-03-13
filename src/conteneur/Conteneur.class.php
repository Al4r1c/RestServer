<?php
    namespace Conteneur;

    use Serveur\GestionErreurs\ErreurManager;
    use Serveur\GestionErreurs\Handler\ErreurHandler;
    use Serveur\Reponse\ReponseManager;
    use Serveur\Requete\RequeteManager;
    use Serveur\Traitement\TraitementManager;
    use Serveur\Utils\FileManager;

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
                $restRequete = new RequeteManager();
                $restRequete->parseServer($c['Server']);

                return $restRequete;
            };

            $conteneur['Server'] = function () {
                $server = new \Serveur\Requete\Server\Server();
                $server->setVarServeur($_SERVER);

                return $server;
            };


            $conteneur['TraitementManager'] = function ($c) {
                $traitementManager = new TraitementManager();
                $traitementManager->setFactoryRessource($c['FactoryRessource']);

                return $traitementManager;
            };

            $conteneur['FactoryRessource'] = function () {
                return function ($nomRessourceDemandee) {
                    if (class_exists($classeRessource = '\\Ressource\\' . ucfirst(strtolower($nomRessourceDemandee)))) {
                        return new $classeRessource();
                    } else {
                        return false;
                    }
                };
            };


            $conteneur['ReponseManager'] = function ($c) {
                $restReponse = new ReponseManager();
                $restReponse->setConfig($c['Config']);
                $restReponse->setHeader($c['Header']);

                return $restReponse;
            };

            $conteneur['Config'] = function () {
                $fichier = FileManager::getFichier();
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
                    $errorManager = new ErreurManager();
                    $errorManager->setErrorHandler($c['ErreurHandler']);

                    return $errorManager;
                }
            );

            $conteneur['ErreurHandler'] = function () {
                return new ErreurHandler();
            };

            $this->_conteneur = $conteneur;
        }

        /**
         * @return RequeteManager
         */
        public function getRequeteManager()
        {
            return $this->_conteneur['RequeteManager'];
        }

        /**
         * @return TraitementManager
         */
        public function getTraitementManager()
        {
            return $this->_conteneur['TraitementManager'];
        }

        /**
         * @return ReponseManager
         */
        public function getReponseManager()
        {
            return $this->_conteneur['ReponseManager'];
        }

        /**
         * @return ErreurManager
         */
        public function getErrorManager()
        {
            return $this->_conteneur['ErreurManager'];
        }
    }