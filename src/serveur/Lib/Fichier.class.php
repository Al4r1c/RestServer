<?php
    namespace Serveur\Lib;

    use Serveur\Exceptions\Exceptions\MainException;
    use Serveur\Exceptions\Exceptions\ArgumentTypeException;

    class Fichier {
        /**
         * @var \Serveur\Lib\FileSystem
         */
        private $_fileSystemInstance;

        /*
         * var string
         */
        private $_nomFichier;

        /**
         * @var string
         */
        private $_repertoireFichier;

        public function getFileSystem() {
            return $this->_fileSystemInstance;
        }

        /**
         * @param \Serveur\Lib\FileSystem $fileSystem
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         */
        public function setFileSystem($fileSystem) {
            if (!$fileSystem instanceof \Serveur\Lib\FileSystem) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, '\Serveur\Lib\FileSystem', $fileSystem);
            }

            $this->_fileSystemInstance = $fileSystem;
        }

        public function getNomFichier() {
            return $this->_nomFichier;
        }

        public function getRepertoireFichier() {
            return $this->_repertoireFichier;
        }

        public function getCheminCompletFichier() {
            return $this->_repertoireFichier . $this->_nomFichier;
        }

        /**
         * @param string $nom
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setNomFichier($nom) {
            if (!is_string($nom)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $nom);
            }

            if (isNull($nom)) {
                throw new MainException(10200, 500);
            }

            if (substr_count($nom, '.') < 1) {
                throw new MainException(10201, 500, $nom);
            }

            $this->_nomFichier = $nom;
        }

        /**
         * @param string $chemin
         * @throws \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function setRepertoireFichier($chemin) {
            if (!is_string($chemin)) {
                throw new ArgumentTypeException(1000, 500, __METHOD__, 'string', $chemin);
            }

            if (isNull($chemin)) {
                throw new MainException(10202, 500);
            }

            $this->_repertoireFichier = $this->_fileSystemInstance->relatifToAbsolu($chemin);
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
            return $this->_fileSystemInstance->fichierExiste($this->getCheminCompletFichier());
        }

        /**
         * @return bool
         */
        public function dossierExiste() {
            return $this->_fileSystemInstance->dossierExiste($this->getRepertoireFichier());
        }

        /**
         * @param string $droit
         * @return bool
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function creerFichier($droit = '0777') {
            if (!$this->dossierExiste()) {
                throw new MainException(10204, 500, $this->_repertoireFichier);
            }

            if (!$this->fichierExiste()) {
                if (!$this->_fileSystemInstance->creerFichier($this->getCheminCompletFichier(), $droit)) {
                    throw new MainException(10205, 500, $this->getCheminCompletFichier());
                }
            }

            return true;
        }

        /**
         * @return mixed
         * @throws \Serveur\Exceptions\Exceptions\MainException
         */
        public function chargerFichier() {
            if (!$this->fichierExiste()) {
                throw new MainException(10203, 50, $this->getCheminCompletFichier());
            }

            return $this->_fileSystemInstance->chargerFichier($this->getCheminCompletFichier());
        }
    }