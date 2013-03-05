<?php
    namespace Serveur\Lib\XMLParser;

    class XMLElement {

        /**
         * @var string
         */
        private $_nom;

        /**
         * @var string[]
         */
        private $_attributs = array();

        /**
         * @var XMLElement[]|bool
         */
        private $_children = array();

        /**
         * @var string
         */
        private $_valeur;

        /**
         * @param array $donnees
         */
        public function setDonnees($donnees) {
            $this->setNom($donnees['element']);
            $this->setAttributs($donnees['attr']);
            $this->setChildren($donnees['children']);
            if (isset($donnees['data'])) {
                $this->setValeur($donnees['data']);
            }
        }

        /**
         * @return string
         */
        public function getNom() {
            return $this->_nom;
        }

        /**
         * @return \string[]
         */
        public function getAttributs() {
            return $this->_attributs;
        }

        /**
         * @param $attribut
         * @return null|string
         */
        public function getAttribut($attribut) {
            if (array_key_exists(strtolower($attribut), $this->_attributs)) {
                return $this->_attributs[strtolower($attribut)];
            } else {
                return null;
            }
        }

        /**
         * @return bool|XMLElement[]
         */
        public function getChildren() {
            if ($this->_children === false) {
                return array();
            }

            return $this->_children;
        }

        /**
         * @return string
         */
        public function getValeur() {
            return $this->_valeur;
        }

        /**
         * @param string $nom
         */
        public function setNom($nom) {
            $this->_nom = $nom;
        }

        /**
         * @param string[] $attributs
         */
        public function setAttributs($attributs) {
            $this->_attributs = $attributs;
        }

        /**
         * @param XMLElement[]|bool $children
         */
        public function setChildren($children) {
            $this->_children = $children;
        }

        /**
         * @param string $valeur
         */
        public function setValeur($valeur) {
            $this->_valeur = $valeur;
        }
    }