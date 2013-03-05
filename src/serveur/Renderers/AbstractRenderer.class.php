<?php
    namespace Serveur\Renderers;

    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    abstract class AbstractRenderer {
        /**
         * @param array $donnees
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return string
         */
        public function render($donnees) {
            if (!is_array($donnees)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $donnees);
            }

            return $this->genererRendu($donnees);
        }

        /**
         * @param array $donnees
         * @return string
         */
        abstract protected function genererRendu(array $donnees);
    }