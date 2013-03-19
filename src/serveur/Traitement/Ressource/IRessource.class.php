<?php
    namespace Serveur\Traitement\Ressource;

    use Serveur\Lib\ObjetReponse;

    interface IRessource
    {
        /**
         * @param string $id
         * @return ObjetReponse
         */
        public function getOne($id);

        /**
         * @param array $filters
         * @return ObjetReponse
         */
        public function getAll($filters);

        /**
         * @param array $data
         * @return ObjetReponse
         */
        public function createOne($data);

        /**
         * @param string $id
         * @param array $data
         * @return ObjetReponse
         */
        public function updateOne($id, $data);

        /**
         * @param string $id
         * @param array $data
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
         * @param array $listeObjects
         * @return ObjetReponse
         */
        public function putCollection($id, $collectionName, $listeObjects);

        /**
         * @param string $id
         * @param string $collectionName
         * @param string $idObject
         * @return ObjetReponse
         */
        public function putInCollection($id, $collectionName, $idObject);

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