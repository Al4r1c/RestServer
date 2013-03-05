<?php
    namespace Logging\Displayer;

    use Logging\Displayer\AbstractDisplayer;

    class Logger extends AbstractDisplayer {
        /**
         * @var string
         */
        private static $_nomFichierErreurs = 'errors.log';

        /**
         * @var string
         */
        private static $_nomFichierAcces = 'access.log';

        /**
         * @var \Serveur\Lib\Fichier
         */
        private $_fichierLogErreur;

        /**
         * @var \Serveur\Lib\Fichier
         */
        private $_fichierLogAcces;

        public function __construct() {
            $this->_fichierLogErreur = $this->creerFichierSiNexistePas(self::$_nomFichierErreurs);
            $this->_fichierLogAcces = $this->creerFichierSiNexistePas(self::$_nomFichierAcces);
        }

        /**
         * @param string $nomFichier
         * @return \Serveur\Lib\Fichier
         */
        private function creerFichierSiNexistePas($nomFichier) {
            $fichier = \Serveur\Utils\FileManager::getFichier();
            $fichier->setFichierParametres($nomFichier, BASE_PATH . '/log');
            $fichier->creerFichier('0700');

            return $fichier;
        }

        /**
         * @param \Serveur\Exceptions\Types\AbstractTypeErreur[] $tabErreurs
         * @return void
         * @throws \Exception
         * */
        protected function ecrireMessageErreur(array $tabErreurs) {
            $fp = fopen($this->_fichierLogErreur->getCheminCompletFichier(), 'a+');
            fseek($fp, SEEK_END);

            foreach ($tabErreurs as $uneErreur) {
                if (substr_count(strtolower(get_class($uneErreur)), 'error') === 1) {
                    $message = '{trad.fatalerror}: ' . $uneErreur->getMessage();
                } elseif (substr_count(strtolower(get_class($uneErreur)), 'notice') === 1) {
                    $message = '{trad.notice}: ' . $uneErreur->getMessage();
                } else {
                    throw new \Exception('Invalid error type');
                }

                fputs($fp, $uneErreur->getDate()->format('d-m-Y H:i:s') . ": \r\n");
                fputs($fp, "\t" . $this->traduireMessageEtRemplacerVariables("{trad.error}" . " n°" . $uneErreur->getCode() . ": {errorType." . substr($uneErreur->getCode(), 0, -2) . "}\r\n"));
                fputs($fp, "\t" . $this->traduireMessageEtRemplacerVariables($message, $uneErreur->getArguments()) . "\r\n");
            }

            fclose($fp);
        }

        /**
         * @param \Serveur\Rest\RestRequete $restRequete
         * @param \Serveur\Rest\RestReponse $restReponse
         * @return void
         * @throws \Exception
         */
        protected function ecrireMessageAcces($restRequete, $restReponse) {
            $fp = fopen($this->_fichierLogAcces->getCheminCompletFichier(), 'a+');
            fseek($fp, SEEK_END);

            fputs($fp, $restRequete->getDateRequete()->format('d-m-Y H:i:s') . ": \r\n");
            fputs($fp, "\t" . $this->traduireMessageEtRemplacerVariables("{trad.remoteIp}: " . $restRequete->getIp()) . "\r\n");
            fputs($fp, "\t" . $this->traduireMessageEtRemplacerVariables("{trad.method}: " . $restRequete->getMethode() . " -- URI: /" . implode('/', $restRequete->getUriVariables()) . "") . "\r\n");
            fputs($fp, "\t" . $this->traduireMessageEtRemplacerVariables("{trad.arguments}:") . "\r\n");
            foreach ($restRequete->getParametres() as $clefParam => $unParam) {
                fputs($fp, "\t\t" . $clefParam . " => " . $unParam . "\r\n");
            }
            fputs($fp, "\t" . $this->traduireMessageEtRemplacerVariables("{trad.reponseCode}: " . $restReponse->getStatus() . " - {trad.reponseFormat}: " . $restReponse->getFormatRetour()) . "\r\n");

            fclose($fp);
        }
    }