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
                    $dbConnection
                );
            }

            $this->_connectionDatabase = $dbConnection;
        }

        /**
         * @param array $dataUri
         * @param array $parametres
         * @return ObjetReponse
         */
        public function doGet($dataUri, $parametres)
        {
            if (!isNull($dataUri[1])) {
                return $this->getOne($dataUri[1]);
            } else {
                return $this->getAll($parametres);
            }
        }

        /**
         * @param array $dataUri
         * @param array $parametres
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
         * @param array $parametres
         * @return ObjetReponse
         */
        public function doPut($dataUri, $parametres)
        {
            if (!isNull($dataUri[2])) {
                if (isNull($dataUri[3])) {
                    return $this->putCollection($dataUri[1], $dataUri[2], $parametres);
                } else {
                    return $this->putInCollection($dataUri[1], $dataUri[2], $dataUri[3]);
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
    }