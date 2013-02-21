<?php
	namespace Serveur\Lib;

	use Serveur\Exceptions\Exceptions\FichierException;

	class Fichier {
		private $nom;
		private $cheminAcces;
		private $extension;
		private $basePath;

		public function getNom() {
			return $this->nom;
		}

		public function getCheminAcces() {
			return $this->cheminAcces;
		}

		public function getLocationFichier() {
			return $this->cheminAcces . '/' . $this->nom;
		}

		public function getExtension() {
			return $this->extension;
		}

		public function getDroits() {
			if ($this->fichierExiste()) {
				return substr(sprintf('%o', fileperms($this->getLocationFichier())), -4);
			} else {
				throw new FichierException(10103, 50, $this->getLocationFichier());
			}
		}

		public function setBasePath($basePath) {
			if (isNull($basePath)) {
				throw new FichierException(10100, 500);
			}

			$this->basePath = rtrim($basePath, '/');
		}

		public function setFichierConfig($nom, $cheminAcces, $isCheminRelatif = true) {
			$this->setNom($nom);
			$this->setCheminAcces($cheminAcces, $isCheminRelatif);
		}

		public function setNom($nom) {
			if (isNull($nom)) {
				throw new FichierException(10101, 500);
			}

			if (substr_count($nom, '.') < 1) {
				throw new FichierException(10102, 500, $nom);
			}

			$this->nom = $nom;
			$this->extension = getFichierExtension($nom);
		}

		public function setCheminAcces($chemin, $isCheminRelatif = true) {
			$this->cheminAcces = ($isCheminRelatif ? $this->basePath . '/' . trim($chemin, '/') : rtrim($chemin, '/'));
		}

		public function fichierExiste() {
			return file_exists($this->getLocationFichier());
		}

		public function dossierExiste() {
			return is_dir($this->cheminAcces);
		}

		public function creerFichier($droit = '0777') {
			if ($this->dossierExiste()) {
				if (!$this->fichierExiste()) {
					$this->creer($this->getLocationFichier(), $droit);
				}
			} else {
				throw new FichierException(10105, 500, $this->cheminAcces);
			}
		}

		protected function creer($urlFichier, $droit) {
			if($leFichier = fopen($this->getLocationFichier(), "wb")) {
				fclose($leFichier);

				chmod($urlFichier, intval($droit, 8));
			} else {
				throw new FichierException(10106, 500, $urlFichier);
			}
		}

		public function chargerFichier() {
			if ($this->fichierExiste()) {
				/** @var $chargeur \Serveur\Lib\FichierChargement\AbstractChargeurFichier */
				$chargeur = $this->getChargeurClass(ucfirst($this->extension));

				return $chargeur->chargerFichier($this->getLocationFichier());
			} else {
				throw new FichierException(10103, 50, $this->getLocationFichier());
			}
		}

		protected function getChargeurClass($className) {
			if(class_exists($nomChargeur = '\\'.SERVER_NAMESPACE.'\Lib\\FichierChargement\\'.$className)) {
				return new $nomChargeur();
			} else {
				throw new FichierException(10104, 500, $this->extension, $this->getLocationFichier());
			}
		}
	}