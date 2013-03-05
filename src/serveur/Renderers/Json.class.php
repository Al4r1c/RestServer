<?php
    namespace Serveur\Renderers;

    class Json extends \Serveur\Renderers\AbstractRenderer {
        /**
         * @param array $donnees
         * @return string
         */
        protected function genererRendu(array $donnees) {
            return json_encode($donnees);
        }
    }