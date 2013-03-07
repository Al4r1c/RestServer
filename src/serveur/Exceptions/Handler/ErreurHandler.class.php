<?php
    namespace Serveur\Exceptions\Handler;

    use Serveur\Exceptions\Types\Notice;
    use Serveur\Exceptions\Types\Error;

    class ErreurHandler {
        /**
         * @var \Logging\Displayer\AbstractDisplayer[]
         */
        private $_observeursLoggerErreurs;

        /**
         * @param \Serveur\Exceptions\Types\AbstractTypeErreur $erreur
         */
        private function ecrireErreur($erreur) {
            foreach ($this->_observeursLoggerErreurs as $unObserveur) {
                $unObserveur->ecrireErreurLog($erreur);
            }
        }

        public function setHandlers() {
            set_error_handler(array($this, 'errorHandler'));
            set_exception_handler(array($this, 'exceptionHandler'));
            $GLOBALS['global_function_ajouterErreur'] = array($this, 'global_ajouterErreur');
        }

        /**
         * @param \Logging\Displayer\AbstractDisplayer $logger
         */
        public function ajouterUnLogger($logger) {
            $this->_observeursLoggerErreurs[] = $logger;
        }

        /**
         * @param int $erreurNumber
         * @param int $codeErreur
         * @param array $arguments
         * @throws \InvalidArgumentException
         */
        public function global_ajouterErreur($erreurNumber, $codeErreur, $arguments) {
            switch ($erreurNumber) {
                case E_USER_ERROR:
                    $this->ecrireErreur(new Error($codeErreur, $arguments));
                    break;

                case E_USER_NOTICE:
                    $this->ecrireErreur(new Notice($codeErreur, $arguments));
                    break;

                default:
                    throw new \InvalidArgumentException('Error type not supported.');
                    break;
            }
        }

        /**
         * @param \Exception $exception
         */
        public function exceptionHandler(\Exception $exception) {
            $erreur = new Error($exception->getCode());
            $erreur->setMessage($exception->getMessage());
            $this->ecrireErreur($erreur);
        }

        /**
         * @param int $codeErreur
         * @param string $messageErreur
         * @param string $fichierErreur
         * @param int $ligneErreur
         * @return bool|null
         * @throws \Exception
         */
        public function errorHandler($codeErreur, $messageErreur, $fichierErreur, $ligneErreur) {
            if (!(error_reporting() & $codeErreur)) {
                return null;
            }

            switch ($codeErreur) {
                case E_COMPILE_ERROR:
                case E_ERROR:
                case E_CORE_ERROR:
                case E_USER_ERROR:
                case E_PARSE:
                    $erreur = new Error($codeErreur);
                    $erreur->setMessage(
                        '{trad.file}: ' . $fichierErreur . ', {trad.line}: ' . $ligneErreur . ' | {trad.warning}: ' .
                            $messageErreur);
                    $this->ecrireErreur($erreur);
                    throw new \Exception();
                    break;

                case E_WARNING:
                case E_CORE_WARNING:
                case E_COMPILE_WARNING:
                case E_USER_WARNING:
                case E_NOTICE:
                case E_USER_NOTICE:
                case E_STRICT:
                case E_DEPRECATED:
                case E_USER_DEPRECATED:
                case E_RECOVERABLE_ERROR:
                    $erreur = new Notice($codeErreur);
                    $erreur->setMessage(
                        '{trad.file}: ' . $fichierErreur . ', {trad.line}: ' . $ligneErreur . ' | {trad.warning}: ' .
                            $messageErreur);
                    $this->ecrireErreur($erreur);
                    break;

                default:
                    throw new \Exception('Type d\'erreur inconnu : [' . $codeErreur . '] ' . $messageErreur);
                    break;
            }

            return true;
        }
    }