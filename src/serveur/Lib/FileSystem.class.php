<?php
    namespace Serveur\Lib;

    use Serveur\Exceptions\Exceptions\MainException;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class FileSystem {
        /**
         * @var string
         */
        private $_basePath;

        /**
         * @var string
         */
        private $_os;

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
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setOs($os) {
            if (!is_string($os)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $os);
            }

            $os = strtolower($os);

            if (substr($os, 0, 7) == 'windows') {
                $this->_os = 'Windows';
            } elseif (substr($os, 0, 3) == 'mac') {
                $this->_os = 'Mac';
            } elseif ($os == 'linux') {
                $this->_os = 'Linux';
            } elseif (substr($os, 0, 7) == 'freebsd') {
                $this->_os = 'FreeBSD';
            } else {
                throw new MainException(10100, 500, $os);
            }
        }

        /**
         * @param string $basePath
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setBasePath($basePath) {
            if (!is_string($basePath)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $basePath);
            }

            if (!$this->isAbsolutePath($basePath)) {
                throw new MainException(10101, 500, $basePath);
            }

            if (!$this->dossierExiste($basePath)) {
                throw new MainException(10102, 500, $basePath);
            }

            $this->_basePath = $basePath;
        }

        /**
         * @param string $cheminVersFichier
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return bool
         */
        public function fichierExiste($cheminVersFichier) {
            if (!is_string($cheminVersFichier)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $cheminVersFichier);
            }

            return file_exists($cheminVersFichier);
        }

        /**
         * @param string $cheminDossier
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return bool
         */
        public function dossierExiste($cheminDossier) {
            if (!is_string($cheminDossier)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $cheminDossier);
            }

            return is_dir($cheminDossier);
        }

        /**
         * @param string $nomFichier
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return string|null
         */
        public function getExtension($nomFichier) {
            if (!is_string($nomFichier)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $nomFichier);
            }

            if (substr_count($nomFichier, '.') < 1) {
                return null;
            } else {
                $fichierDecoupe = explode(".", $nomFichier);

                return end($fichierDecoupe);
            }
        }

        /**
         * @param string $cheminDemande
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         * @return string
         */
        public function getDroits($cheminDemande) {
            if (!is_string($cheminDemande)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $cheminDemande);
            }

            if (!$this->fichierExiste($cheminDemande) && !$this->dossierExiste($cheminDemande)) {
                throw new MainException(10103, 500, $cheminDemande);
            }

            return substr(sprintf('%o', fileperms($cheminDemande)), -4);
        }

        /**
         * @param string $urlFichier
         * @param string $droit
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return bool
         */
        public function creerFichier($urlFichier, $droit = '0777') {
            if (!is_string($urlFichier)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $urlFichier);
            }

            if (!is_string($droit) && !is_int($droit)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string|int', $droit);
            }

            if (!$leFichier = @fopen($urlFichier, 'wb')) {
                trigger_error_app(E_USER_NOTICE, 10104, $urlFichier);

                return false;
            }

            fclose($leFichier);

            chmod($urlFichier, intval($droit, 8));

            return true;
        }

        /**
         * @param string $cheminVersFichier
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         * @return mixed
         */
        public function chargerFichier($cheminVersFichier) {
            if (!is_string($cheminVersFichier)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $cheminVersFichier);
            }

            if (!$this->fichierExiste($cheminVersFichier)) {
                throw new MainException(10105, 50, $cheminVersFichier);
            }

            /** @var $chargeur \Serveur\Lib\FichierChargement\AbstractChargeurFichier */
            if (false === $chargeur = $this->getChargeurClass(ucfirst($this->getExtension($cheminVersFichier)))) {
                throw new MainException(10106, 500, $this->getExtension($cheminVersFichier), $cheminVersFichier);
            }

            return $chargeur->chargerFichier($cheminVersFichier);
        }

        /**
         * @param string $className
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return bool
         */
        protected function getChargeurClass($className) {
            if (class_exists($nomChargeur = '\\' . __NAMESPACE__ . '\\FichierChargement\\' . $className)) {
                return new $nomChargeur();
            } else {
                return false;
            }
        }

        /**
         * @return string
         */
        public function getDirectorySeparateur() {
            if ($this->_os == 'Windows') {
                $separateurChemin = '\\';
            } else {
                $separateurChemin = '/';
            }

            return $separateurChemin;
        }

        /**
         * @param string $chemin
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return string
         */
        public function relatifToAbsolu($chemin) {
            if (!is_string($chemin)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $chemin);
            }

            $separateurChemin = $this->getDirectorySeparateur();
            $chemin = $this->ajouterBaseSiBesoin($chemin, $separateurChemin);

            if (preg_match('(^[a-z]{3,}://)S', $chemin)) {
                $tabUrl = explode('://', $chemin);
                $streamUrl = $tabUrl[0].'://';
                $chemin = $tabUrl[1];
            }

            $chemin = str_replace(array('/', '\\'), $separateurChemin, $chemin);
            $parts = array_filter(explode($separateurChemin, $chemin), 'strlen');
            $absolutes = array();
            foreach ($parts as $uneParti) {
                if ('.' == $uneParti) {
                    continue;
                }

                if ('..' == $uneParti) {
                    array_pop($absolutes);
                } else {
                    $absolutes[] = $uneParti;
                }
            }

            if (isset($streamUrl)) {
                $prefixe = $streamUrl;
            } else {
                $prefixe = ($this->_os == 'Windows' ? '' : '/');
            }

            return $prefixe . implode($separateurChemin, $absolutes) . $separateurChemin;
        }

        /**
         * @param string $chemin
         * @param string $separateurChemin
         * @throws \Serveur\Exceptions\Exceptions\MainException
         * @return string
         */
        protected function ajouterBaseSiBesoin($chemin, $separateurChemin) {
            if (!startsWith($chemin, $this->_basePath)) {
                $chemin = $this->_basePath . $separateurChemin . $chemin;
            }

            return $chemin;
        }

        /**
         * @param string $chemin
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @return bool
         */
        protected function isAbsolutePath($chemin) {
            if (!is_string($chemin)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $chemin);
            }

            if (preg_match('(^[a-z]{3,}://)S', $chemin)) {
                return true;
            }

            if ($this->_os == 'Windows') {
                if (preg_match('@^[A-Z]:\\\\@i', $chemin) || preg_match('@^\\\\\\\\[A-Z]+\\\\[^\\\\]@i', $chemin)) {
                    return true;
                }
            } else {
                if ($chemin[0] == '/') {
                    return true;
                }
            }

            return false;
        }
    }