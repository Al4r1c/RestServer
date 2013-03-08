<?php
    namespace Modules;

    class FactoryMock extends \PHPUnit_Framework_TestCase
    {
        /** @return \PHPUnit_Framework_MockObject_MockObject */
        protected function recupererMockSelonNom($type, $methodes = array())
        {
            $mock = null;

            switch (strtolower($type))
            {
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
                case 'headermanager':
                    $mock = $this->getMockHeadersManager($methodes);
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
                case 'restmanager':
                    $mock = $this->getMockRestManager($methodes);
                    break;
                case 'restrequete':
                    $mock = $this->getMockRestRequete($methodes);
                    break;
                case 'restreponse':
                    $mock = $this->getMockRestReponse($methodes);
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

        /** @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Renderers\AbstractRenderer */
        protected function getMockAbstractRenderer()
        {
            return $this->getMockForAbstractClass('Serveur\Renderers\AbstractRenderer');
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Config\Config
         */
        protected function getMockConfig($methodes = array())
        {
            return $this->getMock('Serveur\Config\Config', $methodes);
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
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\Types\Error
         */
        protected function getMockErreur($methodes = array())
        {
            return $this->getMock('Serveur\Exceptions\Types\Error', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\Handler\ErreurHandler
         */
        protected function getMockErreurHandler($methodes)
        {
            return $this->getMock('Serveur\Exceptions\Handler\ErrorHandler', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\ErrorManager
         */
        protected function getMockErrorManager($methodes)
        {
            return $this->getMock('Serveur\Exceptions\ErrorManager', $methodes);
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
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\HeaderManager
         */
        protected function getMockHeadersManager($methodes = array())
        {
            return $this->getMock('Serveur\Rest\HeaderManager', $methodes);
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
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Exceptions\Types\Notice
         */
        protected function getMockNotice($methodes = array())
        {
            return $this->getMock('Serveur\Exceptions\Types\Notice', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestManager
         */
        protected function getMockRestManager($methodes = array())
        {
            return $this->getMock('Serveur\Rest\RestManager', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestRequete
         */
        protected function getMockRestRequete($methodes = array())
        {
            return $this->getMock('Serveur\Rest\RestRequete', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\RestReponse
         */
        protected function getMockRestReponse($methodes = array())
        {
            return $this->getMock('Serveur\Rest\RestReponse', $methodes);
        }

        /**
         * @param array $methodes
         * @return \PHPUnit_Framework_MockObject_MockObject|\Serveur\Rest\Server
         */
        protected function getMockServer($methodes = array())
        {
            return $this->getMock('Serveur\Rest\Server', $methodes);
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