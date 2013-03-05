<?php
    namespace Serveur\Lib\XMLParser;

    class XMLElement {

        /**
         * @var string
         */
        private $nom;

        /**
         * @var string[]
         */
        private $attributs;

        /**
         * @var XMLElement[]|bool
         */
        private $children;

        /**
         * @var string
         */
        private $valeur;

        /**
         * @param array $donnees
         */
        public function setDonnees($donnees) {
            $this->setNom($donnees['element']);
            $this->setAttributs($donnees['attr']);
            $this->setChildren($donnees['children']);
            if(isset($donnees['data'])) {
                $this->setValeur($donnees['data']);
            }
        }

        /**
         * @return string
         */
        public function getNom() {
            return $this->nom;
        }

        /**
         * @return \string[]
         */
        public function getAttributs() {
            return $this->attributs;
        }

        /**
         * @param $attribut
         * @return null|string
         */
        public function getAttribut($attribut) {
            if(array_key_exists(strtolower($attribut), $this->attributs)) {
                return $this->attributs[strtolower($attribut)];
            } else {
                return null;
            }
        }

        /**
         * @return bool|XMLElement[]
         */
        public function getChildren() {
            if($this->children === false) {
                return array();
            }

            return $this->children;
        }

        /**
         * @return string
         */
        public function getValeur() {
            return $this->valeur;
        }

        /**
         * @param string $nom
         */
        public function setNom($nom) {
            $this->nom = $nom;
        }

        /**
         * @param string[] $attributs
         */
        public function setAttributs($attributs) {
            $this->attributs = $attributs;
        }

        /**
         * @param XMLElement[]|bool $children
         */
        public function setChildren($children) {
            $this->children = $children;
        }

        /**
         * @param string $valeur
         */
        public function setValeur($valeur) {
            $this->valeur = $valeur;
        }
    }