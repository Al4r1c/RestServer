<?php
    namespace Modules\LoggingTests\I18n;

    include_once(__DIR__ . '/../../../TestEnv.php');

    use Modules\TestCase;

    class LoggingFactoryTest extends TestCase {
        public function testRecupererLogger() {
            $factory = \Logging\LoggingFactory::getLogger('logger');
            $this->assertInstanceOf("Logging\\Displayer\\AbstractDisplayer", $factory);
        }

        /**
         * @expectedException \Exception
         */
        public function testRecupererInexistant() {
            \Logging\LoggingFactory::getLogger('WRONG_ONE');
        }
    }