<?php
    namespace Serveur\Reponse\Renderers;

    class Json extends \Serveur\Reponse\Renderers\AbstractRenderer
    {
        /**
         * @param array $donnees
         * @return string
         */
        protected function genererRendu(array $donnees)
        {
            return json_encode($donnees);
        }
    }