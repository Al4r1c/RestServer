<?php
namespace AlaroxRestServeur\Serveur\Traitement;

use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException;
use AlaroxRestServeur\Serveur\Lib\ObjetReponse;
use AlaroxRestServeur\Serveur\Requete\RequeteManager;
use AlaroxRestServeur\Serveur\Traitement\Authorization\AuthorizationManager;
use AlaroxRestServeur\Serveur\Traitement\Data\AbstractDatabase;
use AlaroxRestServeur\Serveur\Traitement\Data\DatabaseConfig;
use AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager;
use AlaroxRestServeur\Serveur\Traitement\Ressource\AbstractRessource;

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
            throw new ArgumentTypeException(500,  'callable', $callableFactoryRessource);
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
            throw new ArgumentTypeException(500,  'callable', $callableDatabaseFactory);
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
                500,  'AlaroxRestServeur\Serveur\Traitement\Data\DatabaseConfig', $databaseConfig
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
                500,  'AlaroxRestServeur\Serveur\Traitement\Data\DatabaseConfig', $authManager
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
        return call_user_func($this->_ressourceFactory, $nomRessource);
    }

    /**
     * @param string $nomDriver
     * @throws MainException
     * @return AbstractDatabase
     */
    public function recupererNouvelleInstanceConnexion($nomDriver)
    {
        return call_user_func($this->_databaseFactory, $nomDriver);
    }

    /**
     * @param RequeteManager $requete
     * @throws MainException
     * @return ObjetReponse
     */
    public function traiterRequeteEtRecupererResultat($requete)
    {
        foreach (get_object_vars($this) as $clef => $unAttribut) {
            if (empty($unAttribut)) {
                throw new MainException(30000, 500, $clef);
            }
        }

        if ($this->_authManager->hasExpired($requete->getDateRequete()) === true) {
            $objetReponse = new ObjetReponse();
            $objetReponse->setErreurHttp(410);
        } elseif (
            ($this->_authManager->isAuthActivated()) === true && $this->_authManager->authentifier($requete) === false
        ) {
            $objetReponse = new ObjetReponse();
            $objetReponse->setErreurHttp(401);
        } else {
            if (($ressourceObjet =
                $this->recupererNouvelleInstanceRessource($nomRessource = $requete->getUriVariable(0))) !== false
            ) {
                if (($dbConn = $this->recupererNouvelleInstanceConnexion($this->_databaseConfig->getDriver())) !== false
                ) {
                    $dbConn->ouvrirConnectionDepuisFichier($this->_databaseConfig);
                    $ressourceObjet->setConnectionDatabase($dbConn);

                    switch (strtoupper($requete->getMethode())) {
                        case 'GET':
                            $objetReponse =
                                $ressourceObjet->doGet(
                                    $requete->getUriVariables(),
                                    $this->formaliserFiltres($requete->getParametres())
                                );
                            break;
                        case 'POST':
                            $objetReponse =
                                $ressourceObjet->doPost(
                                    $requete->getUriVariables(),
                                    $this->formaliserFiltres($requete->getParametres())
                                );
                            break;
                        case 'PUT':
                            $objetReponse =
                                $ressourceObjet->doPut(
                                    $requete->getUriVariables(),
                                    $this->formaliserFiltres($requete->getParametres())
                                );
                            break;
                        case 'DELETE':
                            $objetReponse =
                                $ressourceObjet->doDelete(
                                    $requete->getUriVariables()
                                );
                            break;
                    }
                } else {
                    throw new MainException(30001, 500, $this->_databaseConfig->getDriver());
                }
            } elseif (isNull($nomRessource)) {
                $objetReponse = new ObjetReponse();
                $objetReponse->setErreurHttp(400);
            } else {
                $objetReponse = new ObjetReponse();
                $objetReponse->setErreurHttp(404);
            }
        }

        return $objetReponse;
    }

    /**
     * @param array $tabParametres
     * @return ParametresManager
     */
    private function formaliserFiltres($tabParametres)
    {
        $donnesRequeteManager = new ParametresManager();

        $donnesRequeteManager->parseTabParametres($tabParametres);

        return $donnesRequeteManager;
    }
}