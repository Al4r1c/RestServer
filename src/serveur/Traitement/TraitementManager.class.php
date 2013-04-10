<?php
namespace Serveur\Traitement;

use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use Serveur\GestionErreurs\Exceptions\MainException;
use Serveur\Lib\ObjetReponse;
use Serveur\Requete\RequeteManager;
use Serveur\Traitement\Authorization\AuthorizationManager;
use Serveur\Traitement\Data\AbstractDatabase;
use Serveur\Traitement\Data\DatabaseConfig;
use Serveur\Traitement\Ressource\AbstractRessource;

class TraitementManager
{
    /**
     * @var callable
     */
    private $_ressourceFactory;

    /**
     * @var callable
     */
    private $_databaseFactory;

    /**
     * @var DatabaseConfig
     */
    private $_databaseConfig;

    /**
     * @var AuthorizationManager
     */
    private $_authManager;

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
     * @param callable $callableDatabaseFactory
     * @throws ArgumentTypeException
     */
    public function setDatabaseFactory($callableDatabaseFactory)
    {
        if (!is_callable($callableDatabaseFactory)) {
            throw new ArgumentTypeException(1000, 500, __METHOD__, 'callable', $callableDatabaseFactory);
        }

        $this->_databaseFactory = $callableDatabaseFactory;
    }

    /**
     * @param callable $databaseConfig
     * @throws ArgumentTypeException
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
     * @param AuthorizationManager $authManager
     * @throws ArgumentTypeException
     */
    public function setAuthManager($authManager)
    {
        if (!$authManager instanceof AuthorizationManager) {
            throw new ArgumentTypeException(
                1000, 500, __METHOD__, 'Serveur\Traitement\Data\DatabaseConfig', $authManager
            );
        }

        $this->_authManager = $authManager;
    }

    /**
     * @param string $nomRessource
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
     * @param string $nomDriver
     * @throws MainException
     * @return AbstractDatabase
     */
    public function recupererNouvelleInstanceConnexion($nomDriver)
    {
        if (isNull($this->_databaseFactory)) {
            throw new MainException(30002, 500);
        }

        return call_user_func($this->_databaseFactory, $nomDriver);
    }


    /**
     * @param RequeteManager $requete
     * @throws MainException
     * @return ObjetReponse
     */
    public function traiterRequeteEtRecupererResultat($requete)
    {
        // Regardé si autorisé (se baser sur le fichier authorized.yaml. Fichier vide = all, sinon filtrer)
        $nomRessource = $requete->getUriVariable(0);

        if (($ressourceObjet = $this->recupererNouvelleInstanceRessource($nomRessource)) !== false) {
            if (($dbConn = $this->recupererNouvelleInstanceConnexion($this->_databaseConfig->getDriver())) !== false
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
        } elseif (isNull($nomRessource)) {
            $objetReponse = new ObjetReponse();
            $objetReponse->setErreurHttp(400);
        } else {
            $objetReponse = new ObjetReponse();
            $objetReponse->setErreurHttp(404);
        }

        return $objetReponse;
    }
}