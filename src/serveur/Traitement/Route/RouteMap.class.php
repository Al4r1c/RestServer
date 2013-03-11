<?php
    namespace Serveur\Traitement\Route;

    use Serveur\GestionErreurs\Exceptions\MainException;
    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;

    class RouteMap
    {
        /**
         * @var array
         */
        private $_routesListe;

        /**
         * @param $fichierMapping \Serveur\Lib\Fichier
         * @throws ArgumentTypeException
         */
        public function chargerFichierMapping($fichierMapping)
        {
            if (!$fichierMapping instanceof \Serveur\Lib\Fichier) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Lib\Fichier', $fichierMapping);
            }

            $this->setRoutesListe($fichierMapping->chargerFichier());
        }

        /**
         * @return array
         */
        public function getRoutesListe()
        {
            return $this->_routesListe;
        }

        /**
         * @param string $nomRoute
         * @return string|null
         */
        public function getUneRoute($nomRoute)
        {
            if (array_key_exists($nomRoute, $this->_routesListe)) {
                return $this->_routesListe[$nomRoute];
            } else {
                return null;
            }
        }

        /**
         * @param string $nouvellesRoutes
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function setRoutesListe($nouvellesRoutes)
        {
            if (!is_array($nouvellesRoutes)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $nouvellesRoutes);
            }

            if (!$this->isValideRouteMap($nouvellesRoutes) && !empty($nouvellesRoutes)) {
                throw new MainException(30100, 500);
            }

            $this->_routesListe = $nouvellesRoutes;
        }

        private function isValideRouteMap($nouvellesRoutes)
        {
            return (bool)preg_grep('#^/{1}[a-z]+$#i', array_keys($nouvellesRoutes));
        }

    }