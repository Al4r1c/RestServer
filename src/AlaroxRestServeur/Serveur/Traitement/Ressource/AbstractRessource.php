<?php
namespace AlaroxRestServeur\Serveur\Traitement\Ressource;

use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use AlaroxRestServeur\Serveur\Lib\ObjetReponse;
use AlaroxRestServeur\Serveur\Traitement\Data\AbstractDatabase;
use AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager;

abstract class AbstractRessource implements IRessource
{
    /**
     * @var AbstractDatabase
     */
    private $_connectionDatabase;

    /**
     * @return AbstractDatabase
     */
    public function getConnectionDatabase()
    {
        return $this->_connectionDatabase;
    }

    /**
     * @param AbstractDatabase $dbConnection
     * @throws ArgumentTypeException
     */
    public function setConnectionDatabase($dbConnection)
    {
        if (!$dbConnection instanceof AbstractDatabase) {
            throw new ArgumentTypeException(
                500, '\\AlaroxRestServeur\\Serveur\\Traitement\\Data\\AbstractDatabase', $dbConnection
            );
        }

        $this->_connectionDatabase = $dbConnection;
    }

    /**
     * @param array $dataUri
     * @param ParametresManager $parametres
     * @return ObjetReponse
     */
    public function doGet($dataUri, $parametres)
    {
        if (!isNull($dataUri[1])) {
            return $this->getOne($dataUri[1], $parametres->getLazyLoad());
        } else {
            return $this->getAll($parametres);
        }
    }

    /**
     * @param array $dataUri
     * @param ParametresManager $parametres
     * @return ObjetReponse
     */
    public function doPost($dataUri, $parametres)
    {
        if (!isNull($dataUri[1])) {
            return $this->updateOne($dataUri[1], $parametres);
        } else {
            return $this->createOne($parametres);
        }
    }

    /**
     * @param array $dataUri
     * @param ParametresManager $parametres
     * @return ObjetReponse
     */
    public function doPut($dataUri, $parametres)
    {
        if (!isNull($dataUri[2])) {
            if (isNull($dataUri[3])) {
                return $this->putCollection($dataUri[1], $dataUri[2], $parametres);
            } else {
                return $this->putOneInCollection($dataUri[1], $dataUri[2], $dataUri[3]);
            }
        } else {
            return $this->createOrUpdateIdempotent($dataUri[1], $parametres);
        }
    }

    /**
     * @param array $dataUri
     * @return ObjetReponse
     */
    public function doDelete($dataUri)
    {
        if (!isNull($dataUri[1])) {
            if (!isNull($dataUri[2])) {
                if (isNull($dataUri[3])) {
                    return $this->deleteCollection($dataUri[1], $dataUri[2]);
                } else {
                    return $this->deleteInCollection($dataUri[1], $dataUri[2], $dataUri[3]);
                }
            } else {
                return $this->deleteOne($dataUri[1]);
            }
        } else {
            return $this->deleteAll();
        }
    }

    /**
     * @return ObjetReponse
     */
    protected function forbidden()
    {
        $objetReponse = new ObjetReponse();
        $objetReponse->setErreurHttp(403);

        return $objetReponse;
    }

    /**
     * @param string $id
     * @param mixed $lazyLoad
     * @return ObjetReponse
     */
    public function getOne($id, $lazyLoad)
    {
        return $this->getConnectionDatabase()->recupererId($id, $lazyLoad);
    }

    /**
     * @param ParametresManager $filters
     * @return ObjetReponse
     */
    public function getAll($filters)
    {
        return $this->getConnectionDatabase()->recuperer($filters);
    }

    /**
     * @param ParametresManager $data
     * @return ObjetReponse
     */
    public function createOne($data)
    {
        return $this->getConnectionDatabase()->inserer($data);
    }

    /**
     * @param string $id
     * @param ParametresManager $data
     * @return ObjetReponse
     */
    public function updateOne($id, $data)
    {
        return $this->getConnectionDatabase()->mettreAJour($id, $data);
    }

    /**
     * @param string $id
     * @param ParametresManager $data
     * @return ObjetReponse
     */
    public function createOrUpdateIdempotent($id, $data)
    {
        return $this->getConnectionDatabase()->insererIdempotent($id, $data);
    }

    /**
     * @param string $id
     * @return ObjetReponse
     */
    public function deleteOne($id)
    {
        return $this->getConnectionDatabase()->supprimerId($id);
    }

    /**
     * @return ObjetReponse
     */
    public function deleteAll()
    {
        return $this->getConnectionDatabase()->supprimer();
    }

    /**
     * @param string $id
     * @param string $collectionName
     * @param ParametresManager $listeObjects
     * @return ObjetReponse
     */
    public function putCollection($id, $collectionName, $listeObjects)
    {
        return $this->getConnectionDatabase()->setCollection($id, $collectionName, $listeObjects);
    }

    /**
     * @param string $id
     * @param string $collectionName
     * @param string $idObject
     * @return ObjetReponse
     */
    public function putOneInCollection($id, $collectionName, $idObject)
    {
        return $this->getConnectionDatabase()->ajouterDansCollection($id, $collectionName, $idObject);
    }

    /**
     * @param string $id
     * @param string $collectionName
     * @return ObjetReponse
     */
    public function deleteCollection($id, $collectionName)
    {
        return $this->getConnectionDatabase()->supprimerCollection($id, $collectionName);
    }

    /**
     * @param string $id
     * @param string $collectionName
     * @param string $idObjetDansCollection
     * @return ObjetReponse
     */
    public function deleteInCollection($id, $collectionName, $idObjetDansCollection)
    {
        return $this->getConnectionDatabase()->supprimerDansCollection($id, $collectionName, $idObjetDansCollection);
    }
}