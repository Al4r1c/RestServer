<?php
    namespace Serveur\Traitement\Ressource;

    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
    use Serveur\Lib\ObjetReponse;
    use Serveur\Traitement\Data\AbstractDatabase;

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
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Traitement\Data\AbstractDatabase',
                    $dbConnection);
            }

            $this->_connectionDatabase = $dbConnection;
        }

        /**
         * @param string $id
         * @param array $parametres
         * @return ObjetReponse
         */
        public function doGet($id, $parametres)
        {
            if (!isNull($id)) {
                return $this->getOne($id);
            } else {
                return $this->getAll($parametres);
            }
        }

        /**
         * @param string $id
         * @param array $parametres
         * @return ObjetReponse
         */
        public function doPut($id, $parametres)
        {
            return $this->createOrUpdateIdempotent($id, $parametres);
        }

        /**
         * @param string $id
         * @param array $parametres
         * @return ObjetReponse
         */
        public function doPost($id, $parametres)
        {
            if (isNull($id)) {
                return $this->createOne($parametres);
            } else {
                return $this->updateOne($id, $parametres);
            }
        }

        /**
         * @param string $id
         * @param array $parametres
         * @return ObjetReponse
         */
        public function doDelete($id, $parametres)
        {
            if (!isNull($id)) {
                return $this->deleteOne($id);
            } else {
                return $this->deleteAll($parametres);
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
    }