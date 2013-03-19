<?php
namespace Tests;

use Conteneur\Conteneur;
use Logging\Displayer\AbstractDisplayer;
use Logging\I18n\I18nManager;
use Logging\I18n\TradManager;
use Serveur\GestionErreurs\ErreurManager;
use Serveur\GestionErreurs\Handler\ErreurHandler;
use Serveur\GestionErreurs\Types\Error;
use Serveur\GestionErreurs\Types\Notice;
use Serveur\Lib\Fichier;
use Serveur\Lib\FichierChargement\AbstractChargeurFichier;
use Serveur\Lib\FileSystem;
use Serveur\Lib\ObjetReponse;
use Serveur\Lib\XMLParser\XMLElement;
use Serveur\Lib\XMLParser\XMLParser;
use Serveur\Reponse\Config\Config;
use Serveur\Reponse\Header\Header;
use Serveur\Reponse\Renderers\AbstractRenderer;
use Serveur\Reponse\ReponseManager;
use Serveur\Requete\RequeteManager;
use Serveur\Requete\Server\Server;
use Serveur\Traitement\Data\AbstractDatabase;
use Serveur\Traitement\Data\DatabaseConfig;
use Serveur\Traitement\Ressource\AbstractRessource;
use Serveur\Traitement\TraitementManager;
use Serveur\Utils\Constante;

class FactoryMock extends \PHPUnit_Framework_TestCase
{
    /** @return \PHPUnit_Framework_MockObject_MockObject */
    protected function recupererMockSelonNom($type, $methodes = array())
    {
        $mock = null;

        switch (strtolower($type)) {
            case 'abstractchargeurfichier':
                $mock = $this->getMockAbstractChargeur($methodes);
                break;
            case 'abstractdatabase':
                $mock = $this->getMockAbstractDatabase($methodes);
                break;
            case 'abstractdisplayer':
                $mock = $this->getMockAbstractDisplayer($methodes);
                break;
            case 'abstractrenderer':
                $mock = $this->getMockAbstractRenderer($methodes);
                break;
            case 'abstractressource':
                $mock = $this->getMockAbstractRessource($methodes);
                break;
            case 'config':
                $mock = $this->getMockConfig($methodes);
                break;
            case 'constante':
                $mock = $this->getMockConstante($methodes);
                break;
            case 'databaseconfig':
                $mock = $this->getMockDatabaseConfig($methodes);
                break;
            case 'erreur':
                $mock = $this->getMockErreur($methodes);
                break;
            case 'erreurhandler':
                $mock = $this->getMockErreurHandler($methodes);
                break;
            case 'errormanager':
                $mock = $this->getMockErrorManager($methodes);
                break;
            case 'fichier':
                $mock = $this->getMockFichier($methodes);
                break;
            case 'filesystem':
                $mock = $this->getMockFileSystem($methodes);
                break;
            case 'header':
                $mock = $this->getMockHeaders($methodes);
                break;
            case 'i18nmanager':
                $mock = $this->getMockI18nManager($methodes);
                break;
            case 'conteneur':
                $mock = $this->getMockConteneur($methodes);
                break;
            case 'notice':
                $mock = $this->getMockNotice($methodes);
                break;
            case 'objetreponse';
                $mock = $this->getMockObjetReponse($methodes);
                break;
            case 'requetemanager':
                $mock = $this->getMockRestRequete($methodes);
                break;
            case 'reponsemanager':
                $mock = $this->getMockRestReponse($methodes);
                break;
            case 'server':
                $mock = $this->getMockServer($methodes);
                break;
            case 'tradmanager':
                $mock = $this->getMockTradManager($methodes);
                break;
            case 'traitementmanager':
                $mock = $this->getMockTraitementManager($methodes);
                break;
            case 'xmlelement':
                $mock = $this->getMockXmlElement($methodes);
                break;
            case 'xmlparser':
                $mock = $this->getMockXmlParser($methodes);
                break;
            default:
                new \Exception('Mock type not found.');
                break;
        }

        return $mock;
    }

    protected function getMockAbstractClass($mockClass, $tabMethodes = array())
    {
        return $this->getMockForAbstractClass($mockClass, array(), '', true, true, true, $tabMethodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractChargeurFichier
     */
    protected function getMockAbstractChargeur($methodes = array())
    {
        return $this->getMockAbstractClass('Serveur\Lib\FichierChargement\AbstractChargeurFichier', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractDatabase
     */
    protected function getMockAbstractDatabase($methodes = array())
    {
        return $this->getMockAbstractClass('Serveur\Traitement\Data\AbstractDatabase', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractDisplayer
     */
    protected function getMockAbstractDisplayer($methodes = array())
    {
        return $this->getMockAbstractClass('Logging\Displayer\AbstractDisplayer', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractRenderer
     */
    protected function getMockAbstractRenderer($methodes = array())
    {
        return $this->getMockAbstractClass('Serveur\Reponse\Renderers\AbstractRenderer', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|AbstractRessource
     */
    protected function getMockAbstractRessource($methodes = array())
    {
        return $this->getMockAbstractClass('Serveur\Traitement\Ressource\AbstractRessource', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Config
     */
    protected function getMockConfig($methodes = array())
    {
        return $this->getMock('Serveur\Reponse\Config\Config', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Constante
     */
    protected function getMockConstante($methodes = array())
    {
        return $this->getMock('Serveur\Utils\Constante', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Conteneur
     */
    protected function getMockConteneur($methodes = array())
    {
        return $this->getMock('\Conteneur\Conteneur', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|DatabaseConfig
     */
    protected function getMockDatabaseConfig($methodes = array())
    {
        return $this->getMock('Serveur\Traitement\Data\DatabaseConfig', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Error
     */
    protected function getMockErreur($methodes = array())
    {
        return $this->getMock('Serveur\GestionErreurs\Types\Error', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|ErreurHandler
     */
    protected function getMockErreurHandler($methodes)
    {
        return $this->getMock('Serveur\GestionErreurs\Handler\ErreurHandler', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|ErreurManager
     */
    protected function getMockErrorManager($methodes)
    {
        return $this->getMock('Serveur\GestionErreurs\ErrorManager', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Fichier
     */
    protected function getMockFichier($methodes = array())
    {
        return $this->getMock('Serveur\Lib\Fichier', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|FileSystem
     */
    protected function getMockFileSystem($methodes = array())
    {
        return $this->getMock('Serveur\Lib\FileSystem', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Header
     */
    protected function getMockHeaders($methodes = array())
    {
        return $this->getMock('Serveur\Reponse\Header\Header', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|I18nManager
     */
    protected function getMockI18nManager($methodes = array())
    {
        return $this->getMock('Logging\I18n\I18nManager', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Notice
     */
    protected function getMockNotice($methodes = array())
    {
        return $this->getMock('Serveur\GestionErreurs\Types\Notice', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|ObjetReponse
     */
    protected function getMockObjetReponse($methodes = array())
    {
        return $this->getMock('Serveur\Lib\ObjetReponse', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|RequeteManager
     */
    protected function getMockRestRequete($methodes = array())
    {
        return $this->getMock('Serveur\Requete\RequeteManager', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|ReponseManager
     */
    protected function getMockRestReponse($methodes = array())
    {
        return $this->getMock('Serveur\Reponse\ReponseManager', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|Server
     */
    protected function getMockServer($methodes = array())
    {
        return $this->getMock('Serveur\Requete\Server\Server', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|TradManager
     */
    protected function getMockTradManager($methodes = array())
    {
        return $this->getMock('Logging\I18n\TradManager', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|TraitementManager
     */
    protected function getMockTraitementManager($methodes = array())
    {
        return $this->getMock('Serveur\Traitement\TraitementManager', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|XMLElement
     */
    protected function getMockXmlElement($methodes = array())
    {
        return $this->getMock('Serveur\Lib\XMLParser\XMLElement', $methodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|XMLParser
     */
    protected function getMockXmlParser($methodes = array())
    {
        return $this->getMock('Serveur\Lib\XMLParser\XMLParser', $methodes);
    }
}