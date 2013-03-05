<?php
    namespace Serveur\Lib\XMLParser;

    use Serveur\Lib\XMLParser\XMLElement;

    class XMLParser {
        /**
         * @var string
         */
        private $_donneesSourcedata;

        /**
         * @var array|XMLElement
         */
        private $_donneesParsees;

        /**
         * @var string[]
         */
        private $_erreur;

        /**
         * @param string $contenuXml
         */
        public function setContenu($contenuXml) {
            $this->_donneesSourcedata = $contenuXml;
            $this->parse($contenuXml);
        }

        /**
         * @return bool
         */
        public function isValide() {
            return empty($this->_erreur);
        }

        /**
         * @return string
         */
        public function getErreurMessage() {
            return sprintf('XML error at line %d column %d: %s', $this->_erreur['line'], $this->_erreur['column'], $this->_erreur['message']);
        }

        /**
         * @param string $contenuXml
         */
        private function parse($contenuXml) {
            $parser = xml_parser_create();

            xml_set_object($parser, $this);
            xml_set_element_handler($parser, 'tagDebutXML', 'tagFinXML');
            xml_set_character_data_handler($parser, 'valeurXML');
            xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, false);

            $lignes = explode("\n", $contenuXml);
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
         * @param $tabKey string[]
         * @param $arrayValues XMLElement[]
         * @return string|bool
         * */
        public function rechercheValeurTableauMultidim(array $tabKey, array $arrayValues) {
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