<?php
	namespace Serveur\Lib;

	use DOMDocument;
	use Serveur\Exceptions\Exceptions\FichierException;

	class Fichier {
		private $nom;
		private $cheminAcces;
		private $extension;

		public function __construct($nom, $cheminAcces) {
			$this->setNom($nom);
			$this->setCheminAcces($cheminAcces);
			$this->setExtension($nom);
		}

		public function setNom($nom) {
			$this->nom = $nom;
		}

		public function getNom() {
			return $this->nom;
		}

		public function setCheminAcces($chemin) {
			$this->cheminAcces = BASE_PATH . $chemin;
		}

		public function getCheminAcces() {
			return $this->cheminAcces;
		}

		public function getLocationFichier() {
			return $this->cheminAcces . DIRECTORY_SEPARATOR . $this->nom;
		}

		public function setExtension($nomFichier) {
			$this->extension = getFichierExtension($nomFichier);
		}

		public function getExtension() {
			return $this->extension;
		}

		public function creer($droit = '0777') {
			if (!$this->existe($this->getLocationFichier())) {
				$leFichier = fopen($this->getLocationFichier(), "wb");
				fclose($leFichier);

				if (!$this->existe($this->getLocationFichier())) {
					throw new FichierException(10101, 500, $this->getLocationFichier());
				}

				chmod($this->getLocationFichier(), intval($droit, 8));
			}
		}

		public function charger() {
			$this->verifierExistence();

			if ($this->extension === 'php') {
				return include $this->getLocationFichier();
			} elseif($this->extension == 'yaml') {
				return \Spyc::YAMLLoad($this->getLocationFichier());
			} elseif($this->extension == 'xml') {
				$valeurBufferPrecedente = libxml_use_internal_errors(true);
				$domObjet = new DomDocument();

				$domObjet->load($this->getLocationFichier());
				if(!$domObjet->validate()) {
					$domObjet = false;
				}

				libxml_clear_errors();
				libxml_use_internal_errors($valeurBufferPrecedente);

				return $domObjet;
			} else {
				throw new FichierException(10102, 500, $this->extension, $this->getLocationFichier());
			}
		}

		public function verifierExistence() {
			if(!$this->existe()) {
				throw new FichierException(10100, 50, $this->getLocationFichier());
			}
		}

		public function existe() {
			return file_exists($this->getLocationFichier());
		}
	}