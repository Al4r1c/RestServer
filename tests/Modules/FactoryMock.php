<?php
    namespace Modules;

    class FactoryMock extends \PHPUnit_Framework_TestCase
    {
        /** @return \PHPUnit_Framework_MockObject_MockObject */
        protected function recupererMockSelonNom($type, $methodes = array())
        {
            $mock = null;

            switch (strtolower($type)) {
                case 'abstractchargeurfichier':
                    $mock = $this->getMockAbstractChargeur();
                    break;
                case 'abstractdisplayer':
                    $mock = $this->getMockAbstractDisplayer();
                    break;
                case 'abstractrenderer':
                    $mock = $this->getMockAbstractRenderer();
                    break;
                case 'config':
                    $mock = $this->getMockConfig($methodes);
                    break;
                case 'constante':
                    $mock = $this->getMockConstante($methodes);
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
                case 'requetemanager':
                    $mock = $this->getMockRestRequete($methodes);
                    break;
                case 'reponsemanager':
                    $mock = $this->getMockRestReponse($methodes);
                    break;
                case 'routemanager':
                    $mock = $this->getMockRouteManager($methodes);
                    break;
                case 'server':
                    $mock = $this->getMockServer($methodes);
                    break;
                case 'tradmanager':
                    $mock = $this->getMockTradManager($methodes);
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

        /** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\FichierChargement\AbstractChargeurFichier */
        protected function getMockAbstractChargeur()
        {
            return $this->getMockForAbstractClass('Serveur\Lib\FichierChargement\AbstractChargeurFichier');
        }

        /** @return \PHPUnit_Framework_MockObject_MockObject|\Logging\Displayer\AbstractDisplayer */
        protected function getMockAbstractDisplayer()
        {
            return $this->getMockForAbstractClass('Logging\Displayer\AbstractDisplayer');
        }

        /** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Reponse\Renderers\AbstractRenderer */
        protected function getMockAbstractRenderer()
        {
            return $this->getMockForAbstractClass('Serveur\Reponse\Renderers\AbstractRenderer');
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Reponse\Config\Config
         */
        protected function getMockConfig($methodes = array())
        {
            return $this->getMock('Serveur\Reponse\Config\Config', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Utils\Constante
         */
        protected function getMockConstante($methodes = array())
        {
            return $this->getMock('Serveur\Utils\Constante', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\GestionErreurs\Types\Error
         */
        protected function getMockErreur($methodes = array())
        {
            return $this->getMock('Serveur\GestionErreurs\Types\Error', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\GestionErreurs\Handler\ErreurHandler
         */
        protected function getMockErreurHandler($methodes)
        {
            return $this->getMock('Serveur\GestionErreurs\Handler\ErreurHandler', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\GestionErreurs\ErreurManager
         */
        protected function getMockErrorManager($methodes)
        {
            return $this->getMock('Serveur\GestionErreurs\ErrorManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\Fichier
         */
        protected function getMockFichier($methodes = array())
        {
            return $this->getMock('Serveur\Lib\Fichier', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\FileSystem
         */
        protected function getMockFileSystem($methodes = array())
        {
            return $this->getMock('Serveur\Lib\FileSystem', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Reponse\Header\Header
         */
        protected function getMockHeaders($methodes = array())
        {
            return $this->getMock('Serveur\Reponse\Header\Header', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Logging\I18n\I18nManager
         */
        protected function getMockI18nManager($methodes = array())
        {
            return $this->getMock('Logging\I18n\I18nManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Conteneur\Conteneur
         */
        protected function getMockConteneur($methodes = array())
        {
            return $this->getMock('\Conteneur\Conteneur', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\GestionErreurs\Types\Notice
         */
        protected function getMockNotice($methodes = array())
        {
            return $this->getMock('Serveur\GestionErreurs\Types\Notice', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Requete\RequeteManager
         */
        protected function getMockRestRequete($methodes = array())
        {
            return $this->getMock('Serveur\Requete\RequeteManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Reponse\ReponseManager
         */
        protected function getMockRestReponse($methodes = array())
        {
            return $this->getMock('Serveur\Reponse\ReponseManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Traitement\Route\Route
         */
        private function getMockRouteManager($methodes = array())
        {
            return $this->getMock('Serveur\Traitement\Route\Route', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Requete\Server\Server
         */
        protected function getMockServer($methodes = array())
        {
            return $this->getMock('Serveur\Requete\Server\Server', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Logging\I18n\TradManager
         */
        protected function getMockTradManager($methodes = array())
        {
            return $this->getMock('Logging\I18n\TradManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\XMLParser\XMLElement
         */
        protected function getMockXmlElement($methodes = array())
        {
            return $this->getMock('Serveur\Lib\XMLParser\XMLElement', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Lib\XMLParser\XMLParser
         */
        protected function getMockXmlParser($methodes = array())
        {
            return $this->getMock('Serveur\Lib\XMLParser\XMLParser', $methodes);
        }
    }