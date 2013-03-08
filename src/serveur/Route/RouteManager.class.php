<?php
    namespace Serveur\Route;

    use Serveur\Exceptions\Exceptions\MainException;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class RouteManager
    {
        /**
         * @var array
         */
        private $_routesListe;

        /**
         * @param $fichierMapping \Serveur\Lib\Fichier
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         */
        public function chargerFichierMapping($fichierMapping) {
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
            if(array_key_exists($nomRoute, $this->_routesListe)) {
                return $this->_routesListe[$nomRoute];
            } else {
                return null;
            }
        }

        /**
         * @param string $nouvellesRoutes
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setRoutesListe($nouvellesRoutes)
        {
            if (!is_array($nouvellesRoutes)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $nouvellesRoutes);
            }

            if (!$this->isValideRouteMap($nouvellesRoutes)) {
                throw new MainException(30100, 500);
            }

            $this->_routesListe = $nouvellesRoutes;
        }

        private function isValideRouteMap($nouvellesRoutes)
        {
            return (bool)preg_grep('#^/{1}[a-z]+$#i', array_keys($nouvellesRoutes));
        }

    }