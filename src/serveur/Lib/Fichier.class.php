<?php
	namespace Serveur\Lib;

	class Fichier {
		/**
		 * @var \Serveur\Lib\FileSystem
		 */
		private $fileSystemInstance;

		/*
		 * var string
		 */
		private $nomFichier;

		/**
		 * @var string
		 */
		private $repertoireFichier;

		public function getFileSystem() {
			return $this->fileSystemInstance;
		}

		/**
		 * @param \Serveur\Lib\FileSystem $fileSystem
		 */
		public function setFileSystem($fileSystem) {
			if(!$fileSystem instanceof \Serveur\Lib\FileSystem) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Lib\FileSystem', $fileSystem);
			}

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

		/**
		 * @param string $nom
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setNomFichier($nom) {
			if(!is_string($nom)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'string', $nom);
			}

			if(isNull($nom)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10200, 500);
			}

			if(substr_count($nom, '.') < 1) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10201, 500, $nom);
			}

			$this->nomFichier = $nom;
		}

		/**
		 * @param string $chemin
		 * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setRepertoireFichier($chemin) {
			if(!is_string($chemin)) {
				throw new \Serveur\Exceptions\Exceptions\ArgumentTypeException(1000, 500, __METHOD__, 'string', $chemin);
			}

			if(isNull($chemin)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10202, 500);
			}

			$this->repertoireFichier = $this->fileSystemInstance->relatifToAbsolu($chemin);
		}

		/**
		 * @param string $nomFichier
		 * @param string $cheminAcces
		 */
		public function setFichierParametres($nomFichier, $cheminAcces) {
			$this->setNomFichier($nomFichier);
			$this->setRepertoireFichier($cheminAcces);
		}

		/**
		 * @return bool
		 */
		public function fichierExiste() {
			return $this->fileSystemInstance->fichierExiste($this->getCheminCompletFichier());
		}

		/**
		 * @return bool
		 */
		public function dossierExiste() {
			return $this->fileSystemInstance->dossierExiste($this->getRepertoireFichier());
		}

		/**
		 * @param string $droit
		 * @return bool
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
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

		/**
		 * @return mixed
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function chargerFichier() {
			if(!$this->fichierExiste()) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10203, 50, $this->getCheminCompletFichier());
			}

			return $this->fileSystemInstance->chargerFichier($this->getCheminCompletFichier());
		}
	}