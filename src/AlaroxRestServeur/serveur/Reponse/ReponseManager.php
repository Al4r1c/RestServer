<?php
namespace AlaroxRestServeur\Serveur\Reponse;

use AlaroxRestServeur\Logging\Displayer\AbstractDisplayer;
use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\ArgumentTypeException;
use AlaroxRestServeur\Serveur\GestionErreurs\Exceptions\MainException;
use AlaroxRestServeur\Serveur\Lib\ObjetReponse;
use AlaroxRestServeur\Serveur\Reponse\Config\Config;
use AlaroxRestServeur\Serveur\Reponse\Header\Header;
use AlaroxRestServeur\Serveur\Reponse\Renderers\AbstractRenderer;
use AlaroxRestServeur\Serveur\Utils\Constante;

class ReponseManager
{
    /**
     * @var Header
     */
    private $_header;

    /**
     * @var string[]
     */
    private $_formatsAcceptes;

    /**
     * @var string
     */
    private $_charset;

    /**
     * @var callable
     */
    private $_renderFactory;

    /**
     * @var string
     */
    private $_contenu;

    /**
     * @var AbstractDisplayer[]
     */
    private $_observeurs = array();

    public function __construct()
    {
        http_response_code(500);
    }

    /**
     * @return string[]
     */
    public function getFormatsAcceptes()
    {
        return $this->_formatsAcceptes;
    }

    /**
     * @return string
     */
    public function getCharset()
    {
        return $this->_charset;
    }

    /**
     * @return int
     * @codeCoverageIgnore
     */
    public function getStatus()
    {
        return http_response_code();
    }

    /**
     * @return string
     */
    public function getContenuReponse()
    {
        return $this->_contenu;
    }

    /**
     * @param Header $headerManager
     * @throws ArgumentTypeException
     */
    public function setHeader($headerManager)
    {
        if (!$headerManager instanceof Header) {
            throw new ArgumentTypeException(
                500, '\AlaroxRestServeur\Serveur\Reponse\Header\Header', $headerManager
            );
        }

        $this->_header = $headerManager;
    }

    /**
     * @param Config $configuration
     * @throws ArgumentTypeException
     */
    public function setConfig($configuration)
    {
        if (!$configuration instanceof Config) {
            throw new ArgumentTypeException(
                500, '\AlaroxRestServeur\Serveur\Reponse\Config\Config', $configuration
            );
        }

        $this->setFormatsAcceptes($configuration->getConfigValeur('render'));
        $this->setCharset($configuration->getConfigValeur('config.charset'));
    }

    /**
     * @param callable $renderFactory
     * @throws ArgumentTypeException
     */
    public function setRenderFactory($renderFactory)
    {
        if (!is_callable($renderFactory)) {
            throw new ArgumentTypeException(500, 'callable', $renderFactory);
        }

        $this->_renderFactory = $renderFactory;
    }

    /**
     * @param string[] $formatsAcceptes
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function setFormatsAcceptes($formatsAcceptes)
    {
        if (!is_array($formatsAcceptes)) {
            throw new ArgumentTypeException(500, 'array', $formatsAcceptes);
        }

        if (isNull($formatsAcceptes)) {
            throw new MainException(40000, 400);
        }

        $this->_formatsAcceptes = $formatsAcceptes;
    }

    /**
     * @param string $charset
     * @throws ArgumentTypeException
     * @throws MainException
     */
    public function setCharset($charset)
    {
        if (!is_string($charset)) {
            throw new ArgumentTypeException(500, 'string', $charset);
        }

        if (!in_array(strtoupper($charset), array_map('strtoupper', mb_list_encodings()))) {
            throw new MainException(40001, 500, $charset);
        }

        $this->_charset = strtolower($charset);
    }

    /**
     * @param AbstractDisplayer[] $observeurs
     * @throws ArgumentTypeException
     */
    public function setObserveurs($observeurs)
    {
        if (!is_array($observeurs)) {
            throw new ArgumentTypeException(500, 'array', $observeurs);
        }

        foreach ($observeurs as $unObserveur) {
            if (!$unObserveur instanceof AbstractDisplayer) {
                throw new ArgumentTypeException(500, '\Logging\Displayer\AbstractDisplayer', $unObserveur);
            }
        }

        $this->_observeurs = $observeurs;
    }

    /**
     * @param ObjetReponse $objetReponse
     */
    private function envoyerHeaders($objetReponse)
    {
        http_response_code($objetReponse->getStatusHttp());
        $this->_header->ajouterHeader(
            'Content-type', $objetReponse->getFormat() . '; charset=' . strtolower($this->_charset)
        );
        $this->_header->envoyerHeaders();
    }

    /**
     * @param string $nomClasseRendu
     * @throws MainException
     * @return bool|string
     */
    private function getRenderClass($nomClasseRendu)
    {
        if (isNull($this->_renderFactory)) {
            throw new MainException(40003, 500);
        }

        return call_user_func($this->_renderFactory, $nomClasseRendu);
    }

    /**
     * @param ObjetReponse $objetReponse
     * @param $formatsDemandes
     * @throws ArgumentTypeException
     * @throws MainException
     * @param array $formatsDemandes
     */
    public function fabriquerReponse($objetReponse, $formatsDemandes)
    {
        if (!is_array($formatsDemandes)) {
            throw new ArgumentTypeException(500, 'array', $formatsDemandes);
        }

        foreach ($formatsDemandes as $unFormatDemande) {
            if (false !== $tempNomClassRendu = array_search_recursif($unFormatDemande, $this->_formatsAcceptes)) {
                $objetReponse->setFormat(Constante::chargerConfig('mimes')[strtolower($unFormatDemande)]);

                $nomClassRendu = $tempNomClassRendu;
                break;
            }
        }

        if (isset($nomClassRendu)) {
            /* @var $view AbstractRenderer */
            if (false !== $view = $this->getRenderClass($nomClassRendu)) {
                $this->_contenu = $view->render($objetReponse->getDonneesReponse());
            } else {
                throw new MainException(40002, 415, ucfirst(strtolower($nomClassRendu)));
            }
        } else {
            $objetReponse->setErreurHttp(406);
        }

        $this->envoyerHeaders($objetReponse);
        $this->ecrireReponseLog($objetReponse);
    }

    /**
     * @param ObjetReponse $reponse
     */
    private function ecrireReponseLog($reponse)
    {
        foreach ($this->_observeurs as $unObserveur) {
            $unObserveur->ecrireLogReponse($reponse);
        }
    }
}