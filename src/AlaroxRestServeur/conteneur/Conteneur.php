<?php
namespace AlaroxRestServeur\Conteneur;

use AlaroxFileManager\AlaroxFile;
use AlaroxRestServeur\Serveur\GestionErreurs\ErreurManager;
use AlaroxRestServeur\Serveur\GestionErreurs\Handler\ErreurHandler;
use AlaroxRestServeur\Serveur\Reponse\Config\Config;
use AlaroxRestServeur\Serveur\Reponse\Header\Header;
use AlaroxRestServeur\Serveur\Reponse\ReponseManager;
use AlaroxRestServeur\Serveur\Requete\RequeteManager;
use AlaroxRestServeur\Serveur\Requete\Server\Server;
use AlaroxRestServeur\Serveur\Traitement\Authorization\AuthorizationManager;
use AlaroxRestServeur\Serveur\Traitement\Data\DatabaseConfig;
use AlaroxRestServeur\Serveur\Traitement\TraitementManager;

class Conteneur
{
    /**
     * @var string[]
     */
    protected $_conteneur;

    /**
     * @param array $arrayConfig
     */
    public function buildConteneur($arrayConfig)
    {
        $conteneur = new \Pimple();

        $conteneur['RequeteManager'] = function ($c) {
            $restRequete = new RequeteManager();
            $restRequete->setServer($c['Server']);

            return $restRequete;
        };

        $conteneur['Server'] = function () {
            $server = new Server();
            $server->setVarServeur($_SERVER);

            return $server;
        };


        $conteneur['TraitementManager'] = function ($c) {
            $traitementManager = new TraitementManager();
            $traitementManager->setRessourceFactory($c['FactoryRessource']);
            $traitementManager->setDatabaseFactory($c['DatabaseFactory']);
            $traitementManager->setDatabaseConfig($c['DatabaseConfig']);
            $traitementManager->setAuthManager($c['AuthManager']);

            return $traitementManager;
        };

        $conteneur['AuthManager'] = function () use ($arrayConfig) {
            $alaroxFileManager = new AlaroxFile();
            $fichier = $alaroxFileManager->getFile($arrayConfig['authorizationFile']);

            $authManager = new AuthorizationManager();
            $authManager->chargerFichierAuthorisations($fichier);

            return $authManager;
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

        $conteneur['DatabaseFactory'] = function () {
            return function ($nomDriverDatabase) {
                $nomClasseDatabase =
                    '\\AlaroxRestServeur\\Drivers\\MongoDB\\\Database' . ucfirst(strtolower($nomDriverDatabase));

                if (class_exists($nomClasseDatabase)) {
                    return new $nomClasseDatabase();
                } else {
                    return false;
                }
            };
        };

        $conteneur['DatabaseConfig'] = function () use ($arrayConfig) {
            $alaroxFileManager = new AlaroxFile();
            $fichier = $alaroxFileManager->getFile($arrayConfig['configDatabase']);

            $dbConfig = new DatabaseConfig();
            $dbConfig->recupererInformationFichier($fichier);

            return $dbConfig;
        };


        $conteneur['ReponseManager'] = function ($c) {
            $restReponse = new ReponseManager();
            $restReponse->setConfig($c['Config']);
            $restReponse->setHeader($c['Header']);
            $restReponse->setRenderFactory($c['RenderFactory']);

            return $restReponse;
        };

        $conteneur['Config'] = function () use ($arrayConfig) {
            $alaroxFileManager = new AlaroxFile();
            $fichier = $alaroxFileManager->getFile($arrayConfig['configMain']);

            $configurationManager = new Config();
            $configurationManager->chargerConfiguration($fichier);

            return $configurationManager;
        };

        $conteneur['Header'] = function () {
            return new Header();
        };

        $conteneur['RenderFactory'] = function () {
            return function ($nomClasseRendu) {
                if (class_exists($nomVue = '\\Serveur\\Reponse\\Renderers\\' . ucfirst(strtolower($nomClasseRendu)))) {
                    return new $nomVue();
                } else {
                    return false;
                }
            };
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
     * @return string[]
     */
    public function getConteneur()
    {
        return $this->_conteneur;
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