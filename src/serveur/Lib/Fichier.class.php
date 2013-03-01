<?php
	namespace Serveur\Lib;

	class Fichier {
		/** @var \Serveur\Lib\FileSystem */
		private $fileSystemInstance;
		private $nomFichier;
		private $repertoireFichier;

		public function getFileSystem() {
			return $this->fileSystemInstance;
		}

		public function setFileSystem(\Serveur\Lib\FileSystem $fileSystem) {
			$this->fileSystemInstance = $fileSystem;
		}

		public function getNomFichier() {
			return $this->nomFichier;
		}

		public function getRepertoireFichier() {
			return $this->repertoireFichier;
		}

		public function getCheminCompletFichier() {
			return $this->repertoireFichier . $this->nomFichier;
		}

		public function setNomFichier($nom) {
			if(isNull($nom)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10200, 500);
			}

			if(substr_count($nom, '.') < 1) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10201, 500, $nom);
			}

			$this->nomFichier = $nom;
		}

		public function setRepertoireFichier($chemin) {
			if(isNull($chemin)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10202, 500);
			}

			$this->repertoireFichier = $this->fileSystemInstance->relatifToAbsolu($chemin);
		}

		public function setFichierParametres($nomFichier, $cheminAcces) {
			$this->setNomFichier($nomFichier);
			$this->setRepertoireFichier($cheminAcces);
		}

		public function fichierExiste() {
			return $this->fileSystemInstance->fichierExiste($this->getCheminCompletFichier());
		}

		public function dossierExiste() {
			return $this->fileSystemInstance->dossierExiste($this->getRepertoireFichier());
		}

		public function creerFichier($droit = '0777') {
			if(!$this->dossierExiste()) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10204, 500, $this->repertoireFichier);
			}

			if(!$this->fichierExiste()) {
				if(!$this->fileSystemInstance->creerFichier($this->getCheminCompletFichier(), $droit)) {
					throw new \Serveur\Exceptions\Exceptions\MainException(10205, 500, $this->getCheminCompletFichier());
				}
			}

			return true;
		}

		public function chargerFichier() {
			if(!$this->fichierExiste()) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10203, 50, $this->getCheminCompletFichier());
			}

			return $this->fileSystemInstance->chargerFichier($this->getCheminCompletFichier());
		}
	}