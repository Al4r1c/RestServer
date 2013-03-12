<?php
    namespace Tests\ServeurTests\GestionErreurs;

    use Tests\TestCase;
    use Tests\MockArg;
    use Serveur\GestionErreurs\Handler\ErreurHandler;

    class ErreurHandlerTest extends TestCase
    {
        /** @var ErreurHandler */
        private $_erreurHandler;

        public function setUp()
        {
            $this->_erreurHandler = new ErreurHandler();
        }

        private function expectEcrireErreur()
        {
            $abstractDisplayer = $this->createMock(
                'AbstractDisplayer',
                new MockArg('ecrireMessageErreur')
            );

            $this->_erreurHandler->ajouterUnLogger($abstractDisplayer);
        }

        public function testAjouterErreurGlobal()
        {
            $this->expectEcrireErreur();
            $this->_erreurHandler->global_ajouterErreur(E_USER_ERROR, 10000, array('var1'));
        }

        public function testAjouterNoticeGlobal()
        {
            $this->expectEcrireErreur();
            $this->_erreurHandler->global_ajouterErreur(E_USER_NOTICE, 10000, array('var1'));
        }

        /**
         * @expectedException \InvalidArgumentException
         */
        public function testAjouterErreurNonReconnue()
        {
            $this->_erreurHandler->global_ajouterErreur(E_WARNING, 10000, array('var1'));
        }

        public function testExceptionHandler()
        {
            $this->expectEcrireErreur();
            $this->_erreurHandler->exceptionHandler(new \Exception('Message', 10000));
        }

        /**
         * @expectedException \Exception
         */
        public function testErrorHandlerGraveDoncException()
        {
            $this->expectEcrireErreur();
            $this->_erreurHandler->errorHandler(E_ERROR, 'Message', 'script.php', 15);
        }

        public function testErrorHandlerNotice()
        {
            $this->expectEcrireErreur();
            $this->_erreurHandler->errorHandler(E_DEPRECATED, 'Message', 'script.php', 15);
        }

        /**
         * @expectedException \Exception
         */
        public function testErrorHandlerTypeInconnu()
        {
            $this->_erreurHandler->errorHandler(9999, 'Message', 'script.php', 15);
        }

        public function testNoAdd()
        {
            error_reporting(0);
            $this->assertNull($this->_erreurHandler->errorHandler(9999, 'Message', 'script.php', 15));
            error_reporting(E_ALL);
        }
    }