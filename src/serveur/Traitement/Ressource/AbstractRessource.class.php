<?php
    namespace Serveur\Traitement\Ressource;

    abstract class AbstractRessource
    {
        /**
         * @param int $id
         * @param array $donnees
         * @return \Serveur\Lib\ObjetReponse
         */
        public function doGet($id, $donnees)
        {
            if (!isNull($id)) {
                return $this->getSingle($id);
            } elseif (!isNull($donnees)) {
                return $this->search($donnees);
            } else {
                return $this->getAll();
            }
        }

        /**
         * @param int $id
         * @param array $donnees
         * @return \Serveur\Lib\ObjetReponse
         */
        public function doPut($id, $donnees)
        {
            if (isNull($id)) {
                return $this->missingArgument();
            } else {
                return $this->update($id, $donnees);
            }
        }

        /**
         * @param array $donnees
         * @return \Serveur\Lib\ObjetReponse
         */
        public function doPost($donnees)
        {
            return $this->create($donnees);
        }

        /**
         * @param int $id
         * @return \Serveur\Lib\ObjetReponse
         */
        public function doDelete($id)
        {
            if (isNull($id)) {
                return $this->missingArgument();
            } else {
                return $this->delete($id);
            }
        }

        /**
         * @return \Serveur\Lib\ObjetReponse
         */
        protected function forbidden()
        {
            $objetReponse = new \Serveur\Lib\ObjetReponse();
            $objetReponse->setErreurHttp(403);

            return $objetReponse;
        }

        /**
         * @return \Serveur\Lib\ObjetReponse
         */
        protected function missingArgument()
        {
            $objetReponse = new \Serveur\Lib\ObjetReponse();
            $objetReponse->setErreurHttp(400);

            return $objetReponse;
        }

        /**
         * @param array $donnees
         * @return \Serveur\Lib\ObjetReponse
         */
        protected abstract function create($donnees);

        /**
         * @return \Serveur\Lib\ObjetReponse
         */
        protected abstract function getAll();

        /**
         * @param int $id
         * @return \Serveur\Lib\ObjetReponse
         */
        protected abstract function getSingle($id);

        /**
         * @param int $id
         * @param array $donnees
         * @return \Serveur\Lib\ObjetReponse
         */
        protected abstract function update($id, $donnees);

        /**
         * @param int $id
         * @return \Serveur\Lib\ObjetReponse
         */
        protected abstract function delete($id);

        /**
         * @param array $filtres
         * @return \Serveur\Lib\ObjetReponse
         */
        protected abstract function search($filtres);
    }