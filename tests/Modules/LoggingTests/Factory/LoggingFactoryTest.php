<?php
namespace Tests\LoggingTests\Factory;

use AlaroxRestServeur\Logging\LoggingFactory;
use Tests\TestCase;

class LoggingFactoryTest extends TestCase
{
    public function testRecupererLogger()
    {
        $factory = LoggingFactory::getLogger('logger', '/path/to/logFolder');
        $this->assertInstanceOf("AlaroxRestServeur\\Logging\\Displayer\\AbstractDisplayer", $factory);
    }

    /**
     * @expectedException \Exception
     */
    public function testRecupererInexistant()
    {
        LoggingFactory::getLogger('WRONG_ONE', '.');
    }
}