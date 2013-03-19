<?php
namespace Serveur\Traitement;

use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Lib\ObjetReponse;
use Serveur\Requete\RequeteManager;
use Serveur\Traitement\Data\DatabaseConfig;
use Serveur\Traitement\Data\DatabaseFactory;
use Serveur\Traitement\Ressource\AbstractRessource;

class TraitementManager
{
    /**
     * @var callable
     */
    private $_ressourceFactory;

    /**
     * @var DatabaseFactory
     */
    private $_databaseFactory;

    /**
     * @var DatabaseConfig
     */
    private $_databaseConfig;

    /**
     * @param callable $callableFactoryRessource
     * @throws ArgumentTypeException
     */
    public function setRessourceFactory($callableFactoryRessource)
    {
        if (!is_callable($callableFactoryRessource)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'callable', $callableFactoryRessource);
        }

        $this->_ressourceFactory = $callableFactoryRessource;
    }

    /**
     * @param DatabaseFactory $databaseFactory
     * @throws ArgumentTypeException
     */
    public function setDatabaseFactory($databaseFactory)
    {
        if (!$databaseFactory instanceof DatabaseFactory) {
            throw new ArgumentTypeException(
                1000, 500, __METHOD__, 'Serveur\Traitement\Data\DatabaseFactory', $databaseFactory
            );
        }

        $this->_databaseFactory = $databaseFactory;
    }

    /**
     * @param callable $databaseConfig
     * @throws \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
     */
    public function setDatabaseConfig($databaseConfig)
    {
        if (!$databaseConfig instanceof DatabaseConfig) {
            throw new ArgumentTypeException(
                1000, 500, __METHOD__, 'Serveur\Traitement\Data\DatabaseConfig', $databaseConfig
            );
        }

        $this->_databaseConfig = $databaseConfig;
    }

    /**
     * @param $nomRessource
     * @return AbstractRessource
     * @throws MainException
     */
    public function recupererNouvelleInstanceRessource($nomRessource)
    {
        if (isNull($this->_ressourceFactory)) {
            throw new MainException(30001, 500);
        }

        return call_user_func($this->_ressourceFactory, $nomRessource);
    }

    /**
     * @param RequeteManager $requete
     * @throws MainException
     * @return ObjetReponse
     */
    public function traiterRequeteEtRecupererResultat($requete)
    {
        if (($ressourceObjet = $this->recupererNouvelleInstanceRessource($requete->getUriVariable(0))) !== false) {
            if (($dbConn = $this->_databaseFactory->getConnexionDatabase($this->_databaseConfig->getDriver())) !== false
            ) {
                $dbConn->ouvrirConnectionDepuisFichier($this->_databaseConfig);
                $ressourceObjet->setConnectionDatabase($dbConn);

                switch (strtoupper($requete->getMethode())) {
                    case 'GET':
                        $objetReponse = $ressourceObjet->doGet($requete->getUriVariables(), $requete->getParametres());
                        break;
                    case 'POST':
                        $objetReponse = $ressourceObjet->doPost($requete->getUriVariables(), $requete->getParametres());
                        break;
                    case 'PUT':
                        $objetReponse = $ressourceObjet->doPut($requete->getUriVariables(), $requete->getParametres());
                        break;
                    case 'DELETE':
                        $objetReponse =
                            $ressourceObjet->doDelete($requete->getUriVariables(), $requete->getParametres());
                        break;
                }
            } else {
                throw new MainException(30000, 500, $this->_databaseConfig->getDriver());
            }
        } else {
            $objetReponse = new ObjetReponse();
            $objetReponse->setErreurHttp(404);
        }

        return $objetReponse;
    }
}