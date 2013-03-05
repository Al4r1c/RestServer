<?php
    namespace Serveur\Lib\XMLParser;

    use Serveur\Lib\XMLParser\XMLElement;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class XMLParser {
        /**
         * @var string
         */
        private $_contenuInitial;

        /**
         * @var array|XMLElement
         */
        private $_donneesParsees;

        /**
         * @var string[]
         */
        private $_erreur;

        /**
         * @return string
         */
        public function getContenuInitial() {
            return $this->_contenuInitial;
        }

        /**
         * @return \Serveur\Lib\XMLParser\XMLElement
         */
        public function getDonneesParsees() {
            return $this->_donneesParsees;
        }

        /**
         * @return string
         */
        public function getErreurMessage() {
            if ($this->isValide()) {
                return null;
            } else {
                return sprintf('XML error at line %d column %d: %s', $this->_erreur['line'], $this->_erreur['column'], $this->_erreur['message']);
            }
        }

        /**
         * @param $clefConfig
         * @return XMLElement[]
         */
        public function getConfigValeur($clefConfig) {
            if ($valeur = $this->rechercheValeurTableauMultidim(explode('.', strtolower($clefConfig)), $this->_donneesParsees->getChildren())) {
                return $valeur;
            } else {
                return null;
            }
        }

        /**
         * @param string $contenuXml
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         */
        public function setContenuInitial($contenuXml) {
            if (!is_string($contenuXml)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $contenuXml);
            }

            $this->_contenuInitial = $contenuXml;
        }

        /**
         * @return bool
         */
        public function isValide() {
            return empty($this->_erreur);
        }

        public function parse() {
            $parser = xml_parser_create();

            xml_set_object($parser, $this);
            xml_set_element_handler($parser, 'tagDebutXML', 'tagFinXML');
            xml_set_character_data_handler($parser, 'valeurXML');
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

            $lignes = explode("\n", $this->_contenuInitial);
            foreach ($lignes as $uneLigne) {
                if (trim($uneLigne) == '') {
                    continue;
                }

                $donnee = $uneLigne . "\n";

                if (!xml_parse($parser, $donnee)) {
                    $this->_donneesParsees = null;
                    $this->_erreur = array('line' => xml_get_current_line_number($parser), 'column' => xml_get_current_column_number($parser), 'message' => xml_error_string(xml_get_error_code($parser)));
                }
            }
            unset($GLOBALS['temporaire']);
        }

        /**
         * @param $parser
         * @param string $nom
         * @param string[] $attributs
         */
        private function tagDebutXML($parser, $nom, $attributs) {
            $GLOBALS['temporaire'][] = $nom;

            $this->_donneesParsees[$nom]['element'] = strtolower($nom);
            $this->_donneesParsees[$nom]['attr'] = array_map('strtolower', $attributs);
            $this->_donneesParsees[$nom]['children'] = array();
        }

        /**
         * @param $parser
         * @param string $nom
         */
        private function tagFinXML($parser, $nom) {
            global $temporaire;

            if (end($temporaire) == $nom) {
                $tempName = $nom;

                array_pop($temporaire);

                $nouveauLast = end($temporaire);

                $nouvelElement = new XMLElement();
                $nouvelElement->setDonnees($this->_donneesParsees[$tempName]);

                if (count($temporaire) > 0) {
                    $this->_donneesParsees[$nouveauLast]['children'][] = $nouvelElement;
                    unset($this->_donneesParsees[$tempName]);
                } else {
                    $this->_donneesParsees = $nouvelElement;
                }
            }
        }

        /**
         * @param $parser
         * @param string $valeur
         */
        private function valeurXML($parser, $valeur) {
            if (trim($valeur) != '') {
                end($this->_donneesParsees);
                if (!isset($this->_donneesParsees[key($this->_donneesParsees)]['data'])) {
                    $this->_donneesParsees[key($this->_donneesParsees)]['data'] = trim(str_replace("\n", '', $valeur));
                } else {
                    $this->_donneesParsees[key($this->_donneesParsees)]['data'] .= trim(str_replace("\n", '', $valeur));
                }
                $this->_donneesParsees[key($this->_donneesParsees)]['children'] = false;
            }
        }

        /**
         * @param $tabKey string[]
         * @param $arrayValues XMLElement[]
         * @return string|bool
         * */
        private function rechercheValeurTableauMultidim(array $tabKey, array $arrayValues) {
            if (count($tabKey) == 1) {
                $tabResult = array();

                if (preg_match_all('#^[a-z]+(\[[a-z]+=[a-z0-9]+\]){1}$#', $tabKey[0])) {
                    $tabClef = explode('[', $tabKey[0]);
                    $clef = $tabClef[0];
                    $filtres = explode('=', $tabClef[1]);
                    $filtres[1] = substr($filtres[1], 0, -1);
                } else {
                    $clef = $tabKey[0];
                }

                foreach ($arrayValues as $unElement) {
                    if ($unElement->getNom() === $clef) {
                        if (isset($filtres) && $unElement->getAttribut($filtres[0]) !== strtolower($filtres[1])) {
                            continue;
                        }

                        $tabResult[] = $unElement;
                    }
                }

                return $tabResult;
            } else {
                foreach ($arrayValues as $unElement) {
                    if ($unElement->getNom() === $tabKey[0]) {
                        array_shift($tabKey);

                        return $this->rechercheValeurTableauMultidim($tabKey, $unElement->getChildren());
                    }
                }

                return false;
            }
        }
    }