<?php
    namespace Logging\Displayer;

    use Logging\Displayer\AbstractDisplayer;

    class Logger extends AbstractDisplayer {
        /**
         * @var \Serveur\Lib\Fichier
         */
        private $_fichierLogErreur;

        /**
         * @var \Serveur\Lib\Fichier
         */
        private $_fichierLogAcces;

        /**
         * @param \Serveur\Lib\Fichier $fichierLogAcces
         * @throws \InvalidArgumentException
         */
        public function setFichierLogAcces($fichierLogAcces) {
            if (!$fichierLogAcces instanceof \Serveur\Lib\Fichier) {
                throw new \InvalidArgumentException('Object "\Serveur\Lib\Fichier" required.');
            }

            $this->_fichierLogAcces = $fichierLogAcces;
        }

        /**
         * @param \Serveur\Lib\Fichier $fichierLogErreur
         * @throws \InvalidArgumentException
         */
        public function setFichierLogErreur($fichierLogErreur) {
            if (!$fichierLogErreur instanceof \Serveur\Lib\Fichier) {
                throw new \InvalidArgumentException('Object "\Serveur\Lib\Fichier" required.');
            }

            $this->_fichierLogErreur = $fichierLogErreur;
        }

        /**
         * @param \Serveur\Rest\RestRequete $restRequete
         * @param \Serveur\Rest\RestReponse $restReponse
         * @throws \InvalidArgumentException
         * @throws \Exception
         */
        protected function ecrireMessageAcces($restRequete, $restReponse) {
            if (!$restRequete instanceof \Serveur\Rest\RestRequete) {
                throw new \InvalidArgumentException(sprintf('Invalid argument type %s.', get_class($restRequete)));
            }

            if (!$restReponse instanceof \Serveur\Rest\RestReponse) {
                throw new \InvalidArgumentException(sprintf('Invalid argument type %s.', get_class($restReponse)));
            }

            if (!($this->_fichierLogAcces instanceof \Serveur\Lib\Fichier) || !$this->_fichierLogAcces->fichierExiste()
            ) {
                throw new \Exception('Invalid log access file or file not found.');
            }

            $this->_fichierLogAcces->ecrireDansFichier($restRequete->getDateRequete()->format('d-m-Y H:i:s') . ": \n");
            $this->_fichierLogAcces->ecrireDansFichier(
                "\t" . $this->traduireMessageEtRemplacerVariables("{trad.remoteIp}: " . $restRequete->getIp()) . "\n");
            $this->_fichierLogAcces->ecrireDansFichier("\t" .
                $this->traduireMessageEtRemplacerVariables(
                    "{trad.method}: " . $restRequete->getMethode() . " -- URI: /" .
                    implode('/', $restRequete->getUriVariables()) . "") . "\n");
            $this->_fichierLogAcces->ecrireDansFichier(
                "\t" . $this->traduireMessageEtRemplacerVariables("{trad.arguments}:") . "\n");
            foreach ($restRequete->getParametres() as $clefParam => $unParam) {
                $this->_fichierLogAcces->ecrireDansFichier("\t\t" . $clefParam . " => " . $unParam . "\n");
            }
            $this->_fichierLogAcces->ecrireDansFichier("\t" .
                $this->traduireMessageEtRemplacerVariables(
                    "{trad.reponseCode}: " . $restReponse->getStatus() . " - {trad.reponseFormat}: " .
                    $restReponse->getFormatRetour()) . "\n");
        }

        /**
         * @param \Serveur\Exceptions\Types\AbstractTypeErreur $uneErreur
         * @throws \InvalidArgumentException
         * @throws \Exception
         */
        protected function ecrireMessageErreur($uneErreur) {
            if ($uneErreur instanceof \Serveur\Exceptions\Types\Error) {
                $message = '{trad.fatalerror}: ' . $uneErreur->getMessage();
            } elseif ($uneErreur instanceof \Serveur\Exceptions\Types\Notice) {
                $message = '{trad.notice}: ' . $uneErreur->getMessage();
            } else {
                throw new \InvalidArgumentException(sprintf('Invalid error type %s.', get_class($uneErreur)));
            }

            if (!($this->_fichierLogErreur instanceof \Serveur\Lib\Fichier) ||
                !$this->_fichierLogErreur->fichierExiste()
            ) {
                throw new \Exception('Invalid log error file or file not found.');
            }

            $this->_fichierLogErreur->ecrireDansFichier($uneErreur->getDate()->format('d-m-Y H:i:s') . ": \n");
            $this->_fichierLogErreur->ecrireDansFichier("\t" .
                $this->traduireMessageEtRemplacerVariables(
                    "{trad.error}" . " n°" . $uneErreur->getCodeErreur() . ": {errorType." .
                    substr($uneErreur->getCodeErreur(), 0, -2) . "}\n"));
            $this->_fichierLogErreur->ecrireDansFichier(
                "\t" . $this->traduireMessageEtRemplacerVariables($message, $uneErreur->getArguments()) . "\n");
        }
    }