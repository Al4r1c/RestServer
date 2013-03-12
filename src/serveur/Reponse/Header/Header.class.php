<?php
    namespace Serveur\Reponse\Header;

    use Serveur\Utils\Tools;
    use Serveur\GestionErreurs\Exceptions\MainException;
    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;

    class Header
    {
        /**
         * @var array
         */
        private $_headers = array();

        /**
         * @param string $champ
         * @param string $valeur
         * @throws ArgumentTypeException
         * @throws MainException
         */
        public function ajouterHeader($champ, $valeur)
        {
            if (!is_string($champ)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $champ);
            }

            if (!is_string($valeur)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $valeur);
            }

            if (!Tools::isValideHeader($champ)) {
                throw new MainException(40100, 500, $champ);
            }

            $this->_headers[$champ] = $valeur;
        }

        public function envoyerHeaders()
        {
            foreach ($this->_headers as $champHeader => $valeurHeader) {
                header($champHeader . ': ' . $valeurHeader, true);
            }
        }
    }