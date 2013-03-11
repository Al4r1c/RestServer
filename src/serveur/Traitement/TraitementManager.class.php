<?php
    namespace Serveur\Traitement;

    use Serveur\GestionErreurs\Exceptions\MainException;
    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;

    class TraitementManager
    {
        /**
         * @var \Serveur\Traitement\Route\RouteMap
         */
        private $_routeMap;

        /**
         * @param \Serveur\Traitement\Route\RouteMap $routeMap
         * @throws \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         */
        public function setRouteMap($routeMap)
        {
            if (!$routeMap instanceof \Serveur\Traitement\Route\RouteMap) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Traitement\Route\RouteMap', $routeMap);
            }

            $this->_routeMap = $routeMap;
        }

        /**
         * @return \Serveur\Traitement\Route\RouteMap
         */
        public function getRouteMap()
        {
            return $this->_routeMap;
        }
    }