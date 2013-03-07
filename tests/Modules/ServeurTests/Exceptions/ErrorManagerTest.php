<?php
    namespace Modules\ServeurTests\Exceptions;

    use Modules\TestCase;

    class ErrorManagerTest extends TestCase {
        /** @var \Serveur\Exceptions\ErrorManager */
        private $_errorManager;

        public function setUp() {
            $this->_errorManager = new \Serveur\Exceptions\ErrorManager();
        }

        public function testSetErrorHandler() {
            $errorHandler = $this->createMock('ErreurHandler');

            $this->_errorManager->setErrorHandler($errorHandler);

            $this->assertAttributeEquals($errorHandler, '_errorHandler', $this->_errorManager);
        }

        public function testSetHandlers() {
            $errorHandler = $this->createMock('ErreurHandler',
                array('setHandlers')
            );

            $this->_errorManager->setErrorHandler($errorHandler);
            $this->_errorManager->setHandlers();
        }

        public function testAjouterObserveur() {
            $abstractDisplayer = $this->getMockAbstractDisplayer();
            $errorHandler = $this->createMock('ErreurHandler',
                array('ajouterUnLogger', $abstractDisplayer));

            $this->_errorManager->setErrorHandler($errorHandler);
            $this->_errorManager->ajouterObserveur($abstractDisplayer);
        }
    }