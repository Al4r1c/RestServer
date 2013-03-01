<?php
	namespace Serveur\Lib;

	class FileSystem {
		/**
		 * @var string
		 */
		private $basePath;

		/**
		 * @var string
		 */
		private $os;

		/**
		 * @param string $osName
		 * @param string $basePath
		 */
		public function initialiser($osName, $basePath) {
			$this->setOs($osName);
			$this->setBasePath($basePath);
		}

		/**
		 * @param string $os
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
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
				throw new \Serveur\Exceptions\Exceptions\MainException(10100, 500, $os);
			}
		}

		/**
		 * @param string $basePath
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function setBasePath($basePath) {
			if(!$this->isAbsolutePath($basePath)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10101, 500, $basePath);
			}

			if(!$this->dossierExiste($basePath)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10102, 500, $basePath);
			}

			$this->basePath = $basePath;
		}

		/**
		 * @param string $cheminVersFichier
		 * @return bool
		 */
		public function fichierExiste($cheminVersFichier) {
			return file_exists($cheminVersFichier);
		}

		/**
		 * @param string $cheminDossier
		 * @return bool
		 */
		public function dossierExiste($cheminDossier) {
			return is_dir($cheminDossier);
		}

		/**
		 * @param string $nomFichier
		 * @return string
		 */
		public function getExtension($nomFichier) {
			$fichierDecoupe = explode(".", $nomFichier);

			return end($fichierDecoupe);
		}

		/**
		 * @param string $cheminDemande
		 * @return string
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function getDroits($cheminDemande) {
			if(!$this->fichierExiste($cheminDemande) && !$this->dossierExiste($cheminDemande)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10103, 500, $cheminDemande);
			}

			return substr(sprintf('%o', fileperms($cheminDemande)), -4);
		}

		/**
		 * @param string $urlFichier
		 * @param string $droit
		 * @return bool
		 */
		public function creerFichier($urlFichier, $droit = '0777') {
			if(!$leFichier = @fopen($urlFichier, 'wb')) {
				trigger_error_app(E_USER_NOTICE, 10104, $urlFichier);

				return false;
			}

			fclose($leFichier);

			chmod($urlFichier, intval($droit, 8));

			return true;
		}

		/**
		 * @param string $cheminVersFichier
		 * @return mixed
		 * @throws \Serveur\Exceptions\Exceptions\MainException
		 */
		public function chargerFichier($cheminVersFichier) {
			if(!$this->fichierExiste($cheminVersFichier)) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10105, 50, $cheminVersFichier);
			}

			/** @var $chargeur \Serveur\Lib\FichierChargement\AbstractChargeurFichier */
			if(false === $chargeur = $this->getChargeurClass(ucfirst($this->getExtension($cheminVersFichier)))) {
				throw new \Serveur\Exceptions\Exceptions\MainException(10106, 500, $this->getExtension($cheminVersFichier), $cheminVersFichier);
			}

			return $chargeur->chargerFichier($cheminVersFichier);
		}

		/**
		 * @param string $className
		 * @return bool
		 */
		protected function getChargeurClass($className) {
			if(class_exists($nomChargeur = '\\' . __NAMESPACE__ . '\\FichierChargement\\' . $className)) {
				return new $nomChargeur();
			} else {
				return false;
			}
		}

		/**
		 * @return string
		 */
		public function getDirectorySeparateur() {
			if($this->os == 'Windows') {
				$separateurChemin = '\\';
			} else {
				$separateurChemin = '/';
			}

			return $separateurChemin;
		}

		/**
		 * @param string $chemin
		 * @return string
		 */
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

		/**
		 * @param string $chemin
		 * @param string $separateurChemin
		 * @return string
		 */
		protected function ajouterBaseSiBesoin($chemin, $separateurChemin) {
			if(!startsWith($chemin, $this->basePath)) {
				$chemin = $this->basePath . $separateurChemin . $chemin;
			}

			return $chemin;
		}

		/**
		 * @param string $chemin
		 * @return bool
		 */
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