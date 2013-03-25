<?php
namespace Conteneur;

use AlaroxFileManager\AlaroxFile;
use Serveur\GestionErreurs\ErreurManager;
use Serveur\GestionErreurs\Handler\ErreurHandler;
use Serveur\Reponse\Config\Config;
use Serveur\Reponse\Header\Header;
use Serveur\Reponse\ReponseManager;
use Serveur\Requete\RequeteManager;
use Serveur\Requete\Server\Server;
use Serveur\Traitement\Data\DatabaseConfig;
use Serveur\Traitement\TraitementManager;

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

    private function buildConteneur()
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

        $conteneur['DatabaseFactory'] = function () {
            return function ($nomDriverDatabase) {
                $nomClasseDatabase =
                    '\\Serveur\\Traitement\\Data\\Drivers\\Database' . ucfirst(strtolower($nomDriverDatabase));

                if (class_exists($nomClasseDatabase)) {
                    return new $nomClasseDatabase();
                } else {
                    return false;
                }
            };
        };

        $conteneur['DatabaseConfig'] = function () {
            $alaroxFileManager = new AlaroxFile();
            $fichier = $alaroxFileManager->getFile(BASE_PATH . '/config/databaseConfig.yaml');

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

        $conteneur['Config'] = function () {
            $alaroxFileManager = new AlaroxFile();
            $fichier = $alaroxFileManager->getFile(BASE_PATH . '/config/config.yaml');

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