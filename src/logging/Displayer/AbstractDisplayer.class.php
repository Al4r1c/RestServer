<?php
namespace Logging\Displayer;

use Logging\I18n\TradManager;
use Serveur\GestionErreurs\Types\AbstractTypeErreur;
use Serveur\Lib\ObjetReponse;
use Serveur\Requete\RequeteManager;

abstract class AbstractDisplayer
{
    /** @var TradManager */
    protected $_tradManager;

    /**
     * @param TradManager $tradManager
     * @throws \InvalidArgumentException
     */
    public function setTradManager($tradManager)
    {
        if (!$tradManager instanceof TradManager) {
            throw new \InvalidArgumentException();
        }

        $this->_tradManager = $tradManager;
    }

    /**
     * @param AbstractTypeErreur $uneErreur
     * @return void
     */
    public function ecrireErreurLog($uneErreur)
    {
        if (!isNull($uneErreur)) {
            $this->ecrireMessageErreur($uneErreur);
        }
    }

    /**
     * @param RequeteManager $restRequete
     */
    public function ecrireLogRequete($restRequete)
    {
        $this->logRequete($restRequete);
    }

    /**
     * @param ObjetReponse $restReponse
     */
    public function ecrireLogReponse($restReponse)
    {
        $this->logReponse($restReponse);
    }

    /**
     * @param string $messageATraduire
     * @param array $arguments
     * @return string
     */
    protected function traduireMessageEtRemplacerVariables($messageATraduire, array $arguments = array())
    {
        return vsprintf($this->_tradManager->recupererChaineTraduite($messageATraduire), $arguments);
    }

    /**
     * @param AbstractTypeErreur $uneErreur
     * @return void
     */
    abstract protected function ecrireMessageErreur($uneErreur);

    /**
     * @param RequeteManager $restRequete
     * @return void
     */
    abstract protected function logRequete($restRequete);

    /**
     * @param ObjetReponse $objetReponse
     * @return void
     */
    abstract protected function logReponse($objetReponse);
}