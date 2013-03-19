<?php
namespace Serveur\GestionErreurs\Handler;

use Serveur\GestionErreurs\Types\Error;
use Serveur\GestionErreurs\Types\Notice;

class ErreurHandler
{
    /**
     * @var \Logging\Displayer\AbstractDisplayer[]
     */
    private $_observeursLoggerErreurs;

    private static $_noticeCodes = array(E_COMPILE_ERROR, E_ERROR, E_CORE_ERROR, E_USER_ERROR, E_PARSE);
    private static $_erreurCodes = array(E_WARNING,
        E_CORE_WARNING,
        E_COMPILE_WARNING,
        E_USER_WARNING,
        E_NOTICE,
        E_USER_NOTICE,
        E_STRICT,
        E_DEPRECATED,
        E_USER_DEPRECATED,
        E_RECOVERABLE_ERROR);

    /**
     * @param \Serveur\GestionErreurs\Types\AbstractTypeErreur $erreur
     */
    private function ecrireErreur($erreur)
    {
        foreach ($this->_observeursLoggerErreurs as $unObserveur) {
            $unObserveur->ecrireErreurLog($erreur);
        }
    }

    /**
     * @codeCoverageIgnore
     */
    public function setHandlers()
    {
        set_error_handler(array($this, 'errorHandler'));
        set_exception_handler(array($this, 'exceptionHandler'));
        $GLOBALS['global_function_ajouterErreur'] = array($this, 'global_ajouterErreur');
    }

    /**
     * @param \Logging\Displayer\AbstractDisplayer $logger
     */
    public function ajouterUnLogger($logger)
    {
        $this->_observeursLoggerErreurs[] = $logger;
    }

    /**
     * @param int $erreurNumber
     * @param int $codeErreur
     * @throws \InvalidArgumentException
     * @internal param array $arguments
     */
    public function global_ajouterErreur($erreurNumber, $codeErreur)
    {
        $arguments = func_get_arg(2);

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
    public function exceptionHandler(\Exception $exception)
    {
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
    public function errorHandler($codeErreur, $messageErreur, $fichierErreur, $ligneErreur)
    {
        if (!(error_reporting() & $codeErreur)) {
            return null;
        }

        if (in_array($codeErreur, self::$_noticeCodes)) {
            $erreur = new Error($codeErreur);
            $erreur->setMessage(
                '{trad.file}: ' . $fichierErreur . ', {trad.line}: ' . $ligneErreur . ' | {trad.warning}: ' .
                $messageErreur);
            $this->ecrireErreur($erreur);
            throw new \Exception();
        } elseif (in_array($codeErreur, self::$_erreurCodes)) {
            $erreur = new Notice($codeErreur);
            $erreur->setMessage(
                '{trad.file}: ' . $fichierErreur . ', {trad.line}: ' . $ligneErreur . ' | {trad.warning}: ' .
                $messageErreur);
            $this->ecrireErreur($erreur);
        } else {
            throw new \Exception('Type d\'erreur inconnu : [' . $codeErreur . '] ' . $messageErreur);
        }

        return true;
    }
}