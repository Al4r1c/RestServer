<?php
	namespace Serveur\Lib\XMLParser;

	class XMLElement {

		private $nom;
		private $attributs;
		private $children;
		private $valeur;

		public function setDonnees(array $donnees) {
			$this->setNom($donnees['element']);
			$this->setAttributs($donnees['attr']);
			$this->setChildren($donnees['children']);
			if(isset($donnees['data'])) {
				$this->setValeur($donnees['data']);
			}
		}

		public function getNom() {
			return $this->nom;
		}

		public function getAttributs() {
			return $this->attributs;
		}

		public function getAttribut($attribut) {
			if(array_key_exists(strtolower($attribut), $this->attributs)) {
				return $this->attributs[strtolower($attribut)];
			} else {
				return null;
			}
		}

		public function getChildren() {
			if($this->children === false) {
				return array();
			}

			return $this->children;
		}

		public function getValeur() {
			return $this->valeur;
		}

		public function setNom($nom) {
			$this->nom = $nom;
		}

		public function setAttributs($attributs) {
			$this->attributs = $attributs;
		}

		public function setChildren($children) {
			$this->children = $children;
		}

		public function setValeur($valeur) {
			$this->valeur = $valeur;
		}
	}