<?php
namespace AlaroxRestServeur\Conteneur;

use AlaroxFileManager\AlaroxFile;
use AlaroxRestServeur\Serveur\GestionErreurs\ErreurManager;
use AlaroxRestServeur\Serveur\GestionErreurs\Handler\ErreurHandler;
use AlaroxRestServeur\Serveur\Reponse\Config\Config;
use AlaroxRestServeur\Serveur\Reponse\Header\Header;
use AlaroxRestServeur\Serveur\Reponse\ReponseManager;
use AlaroxRestServeur\Serveur\Requete\compressor\Compressor;
use AlaroxRestServeur\Serveur\Requete\compressor\CompressorFactory;
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
     * @param array $driverList
     */
    public function buildConteneur($arrayConfig, $driverList)
    {
        $conteneur = new \Pimple();

        $conteneur['RequeteManager'] = function ($c) {
            $requeteManager = new RequeteManager();
            $requeteManager->setServer($c['Server']);
            $requeteManager->setCompressor($c['Compressor']);

            return $requeteManager;
        };


        $conteneur['Compressor'] = function () {
            $compressor = new Compressor();
            $compressor->setCompressorFactory(new CompressorFactory());

            return $compressor;
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

        $conteneur['DatabaseFactory'] = function () use ($driverList) {
            return function ($nomDriverDatabase) use ($driverList) {
                if (array_key_exists($nomDriverDatabase, $driverList)) {
                    return $driverList[$nomDriverDatabase];
                } else {
                    return false;
                }
            };
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
                if (class_exists(
                    $nomVue =
                        '\\AlaroxRestServeur\\Serveur\\Reponse\\Renderers\\' . ucfirst(strtolower($nomClasseRendu))
                )
                ) {
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