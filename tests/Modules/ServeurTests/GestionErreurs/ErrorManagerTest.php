<?php
    namespace Modules\ServeurTests\Exceptions;

    use Modules\TestCase;
    use Modules\MockArg;
    use Serveur\GestionErreurs\ErreurManager;

    class ErrorManagerTest extends TestCase
    {
        /** @var ErreurManager */
        private $_errorManager;

        public function setUp()
        {
            $this->_errorManager = new ErreurManager();
        }

        public function testSetErrorHandler()
        {
            $errorHandler = $this->createMock('ErreurHandler');

            $this->_errorManager->setErrorHandler($errorHandler);

            $this->assertAttributeEquals($errorHandler, '_errorHandler', $this->_errorManager);
        }

        public function testSetHandlers()
        {
            $errorHandler = $this->createMock(
                'ErreurHandler',
                new MockArg('setHandlers')
            );

            $this->_errorManager->setErrorHandler($errorHandler);
            $this->_errorManager->setHandlers();
        }

        public function testAjouterObserveur()
        {
            $abstractDisplayer = $this->getMockAbstractDisplayer();
            $errorHandler = $this->createMock(
                'ErreurHandler',
                new MockArg('ajouterUnLogger', null, array($abstractDisplayer))
            );

            $this->_errorManager->setErrorHandler($errorHandler);
            $this->_errorManager->ajouterObserveur($abstractDisplayer);
        }
    }