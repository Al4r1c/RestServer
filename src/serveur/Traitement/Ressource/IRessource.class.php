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
         * @param array $filters
         * @return ObjetReponse
         */
        public function deleteAll($filters);
    }