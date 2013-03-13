<?php
    namespace Serveur\Reponse\Renderers;

    class Plain extends AbstractRenderer
    {
        /**
         * @param array $donnees
         * @return string
         */
        protected function genererRendu(array $donnees)
        {
            return $this->arrayToString($donnees);
        }

        /**
         * @param array $donnees
         * @param int $level
         * @return string
         */
        private function arrayToString(array $donnees, $level = 0)
        {
            $valeurs = '';

            foreach ($donnees as $clef => $valeur) {
                for ($i = 0; $i < $level; $i++) {
                    $valeurs .= "\t";
                }

                if (is_array($valeur)) {
                    $valeurs .= $clef . " => \n" . $this->arrayToString($valeur, ($level + 1));
                } else {
                    $valeurs .= $clef . " => " . $valeur . "\n";
                }
            }

            return $valeurs;
        }
    }