<?php
namespace Logging\Displayer;

use AlaroxFileManager\FileManager\File;
use Logging\Displayer\AbstractDisplayer;
use Serveur\GestionErreurs\Types\AbstractTypeErreur;
use Serveur\GestionErreurs\Types\Error;
use Serveur\GestionErreurs\Types\Notice;
use Serveur\Lib\ObjetReponse;
use Serveur\Requete\RequeteManager;

class Logger extends AbstractDisplayer
{
    /**
     * @var File
     */
    private $_fichierLogErreur;

    /**
     * @var File
     */
    private $_fichierLogAcces;

    /**
     * @param File $fichierLogAcces
     * @throws \InvalidArgumentException
     */
    public function setFichierLogAcces($fichierLogAcces)
    {
        if (!$fichierLogAcces instanceof File) {
            throw new \InvalidArgumentException('Object "\AlaroxFileManager\File" required.');
        }

        $this->_fichierLogAcces = $fichierLogAcces;
    }

    /**
     * @param File $fichierLogErreur
     * @throws \InvalidArgumentException
     */
    public function setFichierLogErreur($fichierLogErreur)
    {
        if (!$fichierLogErreur instanceof File) {
            throw new \InvalidArgumentException('Object "\AlaroxFileManager\File" required.');
        }

        $this->_fichierLogErreur = $fichierLogErreur;
    }

    /**
     * @param RequeteManager $restRequete
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function logRequete($restRequete)
    {
        if (!$restRequete instanceof RequeteManager) {
            throw new \InvalidArgumentException(sprintf('Invalid argument type %s.', get_class($restRequete)));
        }

        if (!($this->_fichierLogAcces instanceof File) || !$this->_fichierLogAcces->fileExist()
        ) {
            throw new \Exception('Invalid log access file or file not found.');
        }

        $this->_fichierLogAcces->writeInFile($restRequete->getDateRequete()->format('d-m-Y H:i:s') . ": \n");
        $this->_fichierLogAcces->writeInFile(
            "\t" . $this->traduireMessageEtRemplacerVariables("{trad.remoteIp}: " . $restRequete->getIp()) . "\n"
        );
        $this->_fichierLogAcces->writeInFile(
            "\t" . $this->traduireMessageEtRemplacerVariables(
                "{trad.method}: " . $restRequete->getMethode() . " -- URI: /" .
                implode('/', $restRequete->getUriVariables()) . ""
            ) . "\n"
        );
        $this->_fichierLogAcces->writeInFile(
            "\t" . $this->traduireMessageEtRemplacerVariables("{trad.arguments}:") . "\n"
        );
        $this->_fichierLogAcces->writeInFile(arrayToString($restRequete->getParametres(), 2));
    }

    /**
     * @param ObjetReponse $objetReponse
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function logReponse($objetReponse)
    {
        if (!$objetReponse instanceof ObjetReponse) {
            throw new \InvalidArgumentException(sprintf('Invalid argument type %s.', get_class($objetReponse)));
        }

        if (!($this->_fichierLogAcces instanceof File) || !$this->_fichierLogAcces->fileExist()
        ) {
            throw new \Exception('Invalid log access file or file not found.');
        }

        $this->_fichierLogAcces->writeInFile(
            "\t" . $this->traduireMessageEtRemplacerVariables(
                "{trad.reponseCode}: " . $objetReponse->getStatusHttp() . " - {trad.reponseFormat}: " .
                $objetReponse->getFormat()
            ) . "\n"
        );
    }

    /**
     * @param AbstractTypeErreur $uneErreur
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function ecrireMessageErreur($uneErreur)
    {
        if ($uneErreur instanceof Error) {
            $message = '{trad.fatalerror}: ' . $uneErreur->getMessage();
        } elseif ($uneErreur instanceof Notice) {
            $message = '{trad.notice}: ' . $uneErreur->getMessage();
        } else {
            throw new \InvalidArgumentException(sprintf('Invalid error type %s.', get_class($uneErreur)));
        }

        if (!($this->_fichierLogErreur instanceof File) || !$this->_fichierLogErreur->fileExist()
        ) {
            throw new \Exception('Invalid log error file or file not found.');
        }

        if (($uneErreur->getCodeErreur() & ($uneErreur->getCodeErreur() - 1)) == 0) {
            $errorType = 1;
        } else {
            $errorType = substr($uneErreur->getCodeErreur(), 0, 3);
        }

        $this->_fichierLogErreur->writeInFile($uneErreur->getDate()->format('d-m-Y H:i:s') . ": \n");
        $this->_fichierLogErreur->writeInFile(
            "\t" . $this->traduireMessageEtRemplacerVariables(
                "{trad.error}" . " n°" . $uneErreur->getCodeErreur() . ": {errorType." . $errorType . "}\n"
            )
        );

        $this->_fichierLogErreur->writeInFile(
            "\t" . $this->traduireMessageEtRemplacerVariables($message, $uneErreur->getArguments()) . "\n"
        );
    }
}