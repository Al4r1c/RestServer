<?php
    namespace Serveur\Lib\XMLParser;

    use Serveur\GestionErreurs\Exceptions\ArgumentTypeException;

    class XMLElement
    {

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
         * @return string
         */
        public function getNom()
        {
            return $this->_nom;
        }

        /**
         * @return \string[]
         */
        public function getAttributs()
        {
            return $this->_attributs;
        }

        /**
         * @param $attribut
         * @return null|string
         */
        public function getAttribut($attribut)
        {
            if (array_key_exists(strtolower($attribut), $this->_attributs)) {
                return $this->_attributs[strtolower($attribut)];
            } else {
                return null;
            }
        }

        /**
         * @return array|XMLElement[]
         */
        public function getChildren()
        {
            if ($this->_children === false) {
                return array();
            }

            return $this->_children;
        }

        /**
         * @return string
         */
        public function getValeur()
        {
            return $this->_valeur;
        }

        /**
         * @param string $nom
         * @throws ArgumentTypeException
         */
        public function setNom($nom)
        {
            if (!is_string($nom)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $nom);
            }

            $this->_nom = $nom;
        }

        /**
         * @param string[] $attributs
         * @throws ArgumentTypeException
         */
        public function setAttributs($attributs)
        {
            if (!is_array($attributs)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array', $attributs);
            }

            $this->_attributs = $attributs;
        }

        /**
         * @param XMLElement[]|bool $children
         * @throws ArgumentTypeException
         */
        public function setChildren($children)
        {
            if (!is_array($children) && !is_bool($children)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'array|bool', $children);
            }

            if (is_array($children)) {
                foreach ($children as $unFils) {
                    if (!$unFils instanceof XMLElement) {
                        throw new ArgumentTypeException(
                            1000, 500, __METHOD__, '\Serveur\Lib\XMLParser\XMLElement', $unFils
                        );
                    }
                }
            }

            $this->_children = $children;
        }

        /**
         * @param string $valeur
         * @throws ArgumentTypeException
         */
        public function setValeur($valeur)
        {
            if (!is_string($valeur)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $valeur);
            }

            $this->_valeur = $valeur;
        }

        /**
         * @param array $donnees
         */
        public function setDonnees($donnees)
        {
            $this->setNom($donnees['element']);
            $this->setAttributs($donnees['attr']);
            $this->setChildren($donnees['children']);
            if (isset($donnees['data'])) {
                $this->setValeur($donnees['data']);
            }
        }
    }