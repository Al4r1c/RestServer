<?php
namespace AlaroxRestServeur\Serveur\Traitement\Ressource;

use AlaroxRestServeur\Serveur\Lib\ObjetReponse;
use AlaroxRestServeur\Serveur\Traitement\DonneeRequete\ParametresManager;

interface IRessource
{
    /**
     * @param string $id
     * @param boolean $lazyLoad
     * @return ObjetReponse
     */
    public function getOne($id, $lazyLoad);

    /**
     * @param ParametresManager $filters
     * @return ObjetReponse
     */
    public function getAll($filters);

    /**
     * @param ParametresManager $data
     * @return ObjetReponse
     */
    public function createOne($data);

    /**
     * @param string $id
     * @param ParametresManager $data
     * @return ObjetReponse
     */
    public function updateOne($id, $data);

    /**
     * @param string $id
     * @param ParametresManager $data
     * @return ObjetReponse
     */
    public function createOrUpdateIdempotent($id, $data);

    /**
     * @param string $id
     * @return ObjetReponse
     */
    public function deleteOne($id);

    /**
     * @return ObjetReponse
     */
    public function deleteAll();

    /**
     * @param string $id
     * @param string $collectionName
     * @param ParametresManager $listeObjects
     * @return ObjetReponse
     */
    public function putCollection($id, $collectionName, $listeObjects);

    /**
     * @param string $id
     * @param string $collectionName
     * @param string $idObject
     * @return ObjetReponse
     */
    public function putOneInCollection($id, $collectionName, $idObject);

    /**
     * @param string $id
     * @param string $collectionName
     * @return ObjetReponse
     */
    public function deleteCollection($id, $collectionName);

    /**
     * @param string $id
     * @param string $collectionName
     * @param string $idObject
     * @return ObjetReponse
     */
    public function deleteInCollection($id, $collectionName, $idObject);
}