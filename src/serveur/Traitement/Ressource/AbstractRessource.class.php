<?php
    namespace Serveur\Traitement\Ressource;

    use Serveur\Lib\ObjetReponse;

    abstract class AbstractRessource
    {
        /**
         * @param int $id
         * @param array $donnees
         * @param string $champs
         * @return ObjetReponse
         */
        public function doGet($id, $donnees, $champs)
        {
            if (!isNull($champs)) {
                $champs = explode(',', $champs);
            }

            if (!isNull($id)) {
                return $this->recuperer($id, $champs);
            } elseif (isNull($id) && !isNull($donnees)) {
                return $this->rechercher($donnees, $champs);
            } else {
                return $this->recupererCollection($champs);
            }
        }

        /**
         * @param int $id
         * @param array $donnees
         * @return ObjetReponse
         */
        public function doPut($id, $donnees)
        {
            if (isNull($id)) {
                return $this->missingArgument();
            } else {
                return $this->mettreAJour($id, $donnees);
            }
        }

        /**
         * @param array $donnees
         * @return ObjetReponse
         */
        public function doPost($donnees)
        {
            return $this->creer($donnees);
        }

        /**
         * @param int $id
         * @return ObjetReponse
         */
        public function doDelete($id)
        {
            if (isNull($id)) {
                return $this->missingArgument();
            } else {
                return $this->supprimer($id);
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
         * @return ObjetReponse
         */
        protected function missingArgument()
        {
            $objetReponse = new ObjetReponse();
            $objetReponse->setErreurHttp(400);

            return $objetReponse;
        }

        /**
         * @param array $donnees
         * @return ObjetReponse
         */
        protected abstract function creer($donnees);

        /**
         * @param array $champs
         * @return ObjetReponse
         */
        protected abstract function recupererCollection($champs);

        /**
         * @param int $id
         * @param array $champs
         * @return ObjetReponse
         */
        protected abstract function recuperer($id, $champs);

        /**
         * @param int $id
         * @param array $donnees
         * @return ObjetReponse
         */
        protected abstract function mettreAJour($id, $donnees);

        /**
         * @param int $id
         * @return ObjetReponse
         */
        protected abstract function supprimer($id);

        /**
         * @param array $filtres
         * @param array $champs
         * @return ObjetReponse
         */
        protected abstract function rechercher($filtres, $champs);
    }