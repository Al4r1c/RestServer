<?php
    namespace Serveur\Reponse\Renderers;

    class Json extends AbstractRenderer
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