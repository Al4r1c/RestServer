<?php
    namespace Modules\ServeurTests\Exceptions;

    use Modules\TestCase;
    use Modules\MockArg;

    class ErreurHandlerTest extends TestCase
    {
        /** @var \Serveur\Exceptions\Handler\ErreurHandler */
        private $_errorHandler;

        public function setUp()
        {
            $this->_errorHandler = new \Serveur\Exceptions\Handler\ErreurHandler();
        }

        private function expectEcrireErreur()
        {
            $abstractDisplayer = $this->createMock(
                'AbstractDisplayer',
                new MockArg('ecrireMessageErreur')
            );

            $this->_errorHandler->ajouterUnLogger($abstractDisplayer);
        }

        public function testAjouterErreurGlobal()
        {
            $this->expectEcrireErreur();
            $this->_errorHandler->global_ajouterErreur(E_USER_ERROR, 10000, array('var1'));
        }

        public function testAjouterNoticeGlobal()
        {
            $this->expectEcrireErreur();
            $this->_errorHandler->global_ajouterErreur(E_USER_NOTICE, 10000, array('var1'));
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testAjouterErreurNonReconnue()
        {
            $this->_errorHandler->global_ajouterErreur(E_WARNING, 10000, array('var1'));
        }

        public function testExceptionHandler()
        {
            $this->expectEcrireErreur();
            $this->_errorHandler->exceptionHandler(new \Exception('Message', 10000));
        }

        /**
         * @expectedException \Exception
         */
        public function testErrorHandlerGraveDoncException()
        {
            $this->expectEcrireErreur();
            $this->_errorHandler->errorHandler(E_ERROR, 'Message', 'script.php', 15);
        }

        public function testErrorHandlerNotice()
        {
            $this->expectEcrireErreur();
            $this->_errorHandler->errorHandler(E_DEPRECATED, 'Message', 'script.php', 15);
        }

        /**
         * @expectedException \Exception
         */
        public function testErrorHandlerTypeInconnu()
        {
            $this->_errorHandler->errorHandler(9999, 'Message', 'script.php', 15);
        }

        public function testNoAdd()
        {
            error_reporting(0);
            $this->assertNull($this->_errorHandler->errorHandler(9999, 'Message', 'script.php', 15));
            error_reporting(E_ALL);
        }
    }