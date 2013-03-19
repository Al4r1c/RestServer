<?php
namespace Logging\Displayer;

use Logging\Displayer\AbstractDisplayer;
use Serveur\GestionErreurs\Types\AbstractTypeErreur;
use Serveur\GestionErreurs\Types\Error;
use Serveur\GestionErreurs\Types\Notice;
use Serveur\Lib\Fichier;
use Serveur\Reponse\ReponseManager;
use Serveur\Requete\RequeteManager;

class Logger extends AbstractDisplayer
{
    /**
     * @var Fichier
     */
    private $_fichierLogErreur;

    /**
     * @var Fichier
     */
    private $_fichierLogAcces;

    /**
     * @param Fichier $fichierLogAcces
     * @throws \InvalidArgumentException
     */
    public function setFichierLogAcces($fichierLogAcces)
    {
        if (!$fichierLogAcces instanceof Fichier) {
            throw new \InvalidArgumentException('Object "\Serveur\Lib\Fichier" required.');
        }

        $this->_fichierLogAcces = $fichierLogAcces;
    }

    /**
     * @param Fichier $fichierLogErreur
     * @throws \InvalidArgumentException
     */
    public function setFichierLogErreur($fichierLogErreur)
    {
        if (!$fichierLogErreur instanceof Fichier) {
            throw new \InvalidArgumentException('Object "\Serveur\Lib\Fichier" required.');
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

        if (!($this->_fichierLogAcces instanceof Fichier) || !$this->_fichierLogAcces->fichierExiste()
        ) {
            throw new \Exception('Invalid log access file or file not found.');
        }

        $this->_fichierLogAcces->ecrireDansFichier($restRequete->getDateRequete()->format('d-m-Y H:i:s') . ": \n");
        $this->_fichierLogAcces->ecrireDansFichier(
            "\t" . $this->traduireMessageEtRemplacerVariables("{trad.remoteIp}: " . $restRequete->getIp()) . "\n"
        );
        $this->_fichierLogAcces->ecrireDansFichier(
            "\t" . $this->traduireMessageEtRemplacerVariables(
                "{trad.method}: " . $restRequete->getMethode() . " -- URI: /" .
                implode('/', $restRequete->getUriVariables()) . ""
            ) . "\n"
        );
        $this->_fichierLogAcces->ecrireDansFichier(
            "\t" . $this->traduireMessageEtRemplacerVariables("{trad.arguments}:") . "\n"
        );
        foreach ($restRequete->getParametres() as $clefParam => $unParam) {
            $this->_fichierLogAcces->ecrireDansFichier("\t\t" . $clefParam . " => " . $unParam . "\n");
        }
    }

    /**
     * @param ReponseManager $restReponse
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    protected function logReponse($restReponse)
    {
        if (!$restReponse instanceof ReponseManager) {
            throw new \InvalidArgumentException(sprintf('Invalid argument type %s.', get_class($restReponse)));
        }

        if (!($this->_fichierLogAcces instanceof Fichier) || !$this->_fichierLogAcces->fichierExiste()
        ) {
            throw new \Exception('Invalid log access file or file not found.');
        }

        $this->_fichierLogAcces->ecrireDansFichier(
            "\t" . $this->traduireMessageEtRemplacerVariables(
                "{trad.reponseCode}: " . $restReponse->getStatus() . " - {trad.reponseFormat}: " .
                $restReponse->getFormatRetour()
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

        if (!($this->_fichierLogErreur instanceof Fichier) || !$this->_fichierLogErreur->fichierExiste()
        ) {
            throw new \Exception('Invalid log error file or file not found.');
        }

        $this->_fichierLogErreur->ecrireDansFichier($uneErreur->getDate()->format('d-m-Y H:i:s') . ": \n");
        $this->_fichierLogErreur->ecrireDansFichier(
            "\t" . $this->traduireMessageEtRemplacerVariables(
                "{trad.error}" . " n°" . $uneErreur->getCodeErreur() . ": {errorType." .
                substr($uneErreur->getCodeErreur(), 0, -2) . "}\n"
            )
        );
        $this->_fichierLogErreur->ecrireDansFichier(
            "\t" . $this->traduireMessageEtRemplacerVariables($message, $uneErreur->getArguments()) . "\n"
        );
    }
}