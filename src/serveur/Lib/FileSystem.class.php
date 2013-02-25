<?php
	namespace Serveur\Lib;

	use Serveur\Exceptions\Exceptions\FileSystemException;

	class FileSystem {

		private $basePath;
		private $os;

		public function initialiser($osName, $basePath) {
			$this->setOs($osName);
			$this->setBasePath($basePath);
		}

		public function setOs($os) {
			$os = strtolower($os);

			if(substr($os, 0, 7) == 'windows') {
				$this->os = 'Windows';
			} elseif(substr($os, 0, 3) == 'mac') {
				$this->os = 'Mac';
			} elseif($os == 'linux') {
				$this->os = 'Linux';
			} elseif(substr($os, 0, 7) == 'freebsd') {
				$this->os = 'FreeBSD';
			} else {
				throw new FileSystemException(10100, 500, $os);
			}
		}

		public function setBasePath($basePath) {
			if(!$this->isAbsolutePath($basePath)) {
				throw new FileSystemException(10101, 500, $basePath);
			}

			if(!$this->dossierExiste($basePath)) {
				throw new FileSystemException(10102, 500, $basePath);
			}

			$this->basePath = $basePath;
		}

		public function fichierExiste($cheminVersFichier) {
			return file_exists($cheminVersFichier);
		}

		public function dossierExiste($cheminDossier) {
			return is_dir($cheminDossier);
		}

		public function getExtension($nomFichier) {
			$fichierDecoupe = explode(".", $nomFichier);

			return end($fichierDecoupe);
		}

		public function getDroits($cheminDemande) {
			if($this->fichierExiste($cheminDemande) || $this->dossierExiste($cheminDemande)) {
				return substr(sprintf('%o', fileperms($cheminDemande)), -4);
			} else {
				throw new FileSystemException(10103, 500, $cheminDemande);
			}
		}

		public function creerFichier($urlFichier, $droit = '0777') {
			if($leFichier = @fopen($urlFichier, 'wb')) {
				fclose($leFichier);

				chmod($urlFichier, intval($droit, 8));

				return true;
			} else {
				throw new FileSystemException(10104, 500, $urlFichier);
			}
		}

		public function chargerFichier($cheminVersFichier) {
			if(!$this->fichierExiste($cheminVersFichier)) {
				throw new FileSystemException(10105, 50, $cheminVersFichier);
			}

			/** @var $chargeur \Serveur\Lib\FichierChargement\AbstractChargeurFichier */
			if(false === $chargeur = $this->getChargeurClass(ucfirst($this->getExtension($cheminVersFichier)))) {
				throw new FileSystemException(10106, 500, $this->getExtension($cheminVersFichier), $cheminVersFichier);
			}

			return $chargeur->chargerFichier($cheminVersFichier);
		}

		protected function getChargeurClass($className) {
			if(class_exists($nomChargeur = '\\' . __NAMESPACE__ . '\\FichierChargement\\' . $className)) {
				return new $nomChargeur();
			} else {
				return false;
			}
		}

		public function getDirectorySeparateur() {
			if($this->os == 'Windows') {
				$separateurChemin = '\\';
			} else {
				$separateurChemin = '/';
			}

			return $separateurChemin;
		}

		public function relatifToAbsolu($chemin) {
			$separateurChemin = $this->getDirectorySeparateur();
			$chemin = $this->ajouterBaseSiBesoin($chemin, $separateurChemin);

			$chemin = str_replace(array('/', '\\'), $separateurChemin, $chemin);
			$parts = array_filter(explode($separateurChemin, $chemin), 'strlen');
			$absolutes = array();
			foreach($parts as $uneParti) {
				if('.' == $uneParti) {
					continue;
				}

				if('..' == $uneParti) {
					array_pop($absolutes);
				} else {
					$absolutes[] = $uneParti;
				}
			}

			return ($this->os == 'Windows' ? '' : '/') . implode($separateurChemin, $absolutes) . $separateurChemin;
		}

		protected function ajouterBaseSiBesoin($chemin, $separateurChemin) {
			if(!startsWith($chemin, $this->basePath)) {
				$chemin = $this->basePath . $separateurChemin . $chemin;
			}

			return $chemin;
		}

		protected function isAbsolutePath($chemin) {
			if(preg_match('(^[a-z]{3,}://)S', $chemin)) {
				return true;
			}

			if($this->os == 'Windows') {
				if(preg_match('@^[A-Z]:\\\\@i', $chemin) || preg_match('@^\\\\\\\\[A-Z]+\\\\[^\\\\]@i', $chemin)) {
					return true;
				}
			} else {
				if($chemin[0] == '/') {
					return true;
				}
			}

			return false;
		}
	}