<?php
    namespace Logging\I18n;

    class I18nManager {
        /**
         * @var string
         */
        private $langueDefaut;

        /**
         * @var string[]
         */
        private $languesDisponibles;

        /**
         * @param string $langueDefaut
         * @param array $languesDispo
         */
        public function setConfig($langueDefaut, array $languesDispo) {
            $this->setLangueDefaut($langueDefaut);
            $this->setLangueDispo($languesDispo);
        }

        /**
         * @param string $langueDefaut
         * @throws \Exception
         */
        public function setLangueDefaut($langueDefaut) {
            if (isNull($langueDefaut)) {
                throw new \Exception('Default language is not set properly.');
            }

            $this->langueDefaut = $langueDefaut;
        }

        /**
         * @param array $languesDispo
         * @throws \Exception
         */
        public function setLangueDispo(array $languesDispo) {
            if (isNull($languesDispo)) {
                throw new \Exception('No available language set.');
            }

            $this->languesDisponibles = $languesDispo;
        }

        /**
         * @return \Serveur\Lib\XMLParser\XMLParser
         * @throws \Exception
         * */
        public function getFichierTraduction() {
            if (array_key_exists(strtoupper($this->langueDefaut), $this->languesDisponibles)) {
                $nomFichierLangueDefaut = $this->languesDisponibles[strtoupper($langueDefautUtilisee = $this->langueDefaut)];
            } else {
                $nomFichierLangueDefaut = reset($this->languesDisponibles);
            }

            $fichierTraductionParDefaut = $this->getFichier($nomFichierLangueDefaut);

            if ($fichierTraductionParDefaut->fichierExiste() && $this->recupererXmlParserDepuisFichier($fichierTraductionParDefaut)->isValide()) {
                return $this->recupererXmlParserDepuisFichier($fichierTraductionParDefaut);
            } else {
                if (($langueChoisiAleatoirement = $this->getUneTraductionAleatoire()) !== false) {
                    return $this->recupererXmlParserDepuisFichier(reset($langueChoisiAleatoirement));
                } else {
                    throw new \Exception('No valid translation file set or found.');
                }
            }
        }

        /**
         * @return \Serveur\Lib\Fichier
         */
        protected function getFichier($nomFichier) {
            $fichier = \Serveur\Utils\FileManager::getFichier();
            $fichier->setFichierParametres($nomFichier . '.xml', '/public/i18n');

            return $fichier;
        }

        /**
         * @return array|bool
         */
        private function getUneTraductionAleatoire() {
            foreach ($this->languesDisponibles as $uneLangueDispo => $classeLangue) {
                $traductionDisponible = $this->getFichier($classeLangue);

                if ($traductionDisponible->fichierExiste() && $this->recupererXmlParserDepuisFichier($traductionDisponible)->isValide()) {
                    return array($uneLangueDispo => $traductionDisponible);
                }
            }

            return false;
        }

        /**
         * @return \Serveur\Lib\XMLParser\XMLParser
         */
        private function recupererXmlParserDepuisFichier(\Serveur\Lib\Fichier $fichier) {
            return $fichier->chargerFichier();
        }
    }