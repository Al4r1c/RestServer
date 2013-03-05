<?php
    namespace Serveur\Rest;

    use Serveur\Utils\Tools;
    use Serveur\Exceptions\Exceptions\MainException;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class HeaderManager {
        /**
         * @var array
         */
        private $_headers = array();

        /**
         * @param string $champ
         * @param string $valeur
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function ajouterHeader($champ, $valeur) {
            if (!is_string($champ)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $champ);
            }

            if (!is_string($valeur)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $valeur);
            }

            if (!Tools::isValideHeader($champ)) {
                throw new MainException(20400, 500);
            }

            $this->_headers[$champ] = $valeur;
        }

        public function envoyerHeaders() {
            foreach ($this->_headers as $champHeader => $valeurHeader) {
                header($champHeader . ': ' . $valeurHeader, true);
            }
        }
    }