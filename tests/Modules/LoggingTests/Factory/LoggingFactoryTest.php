<?php
namespace Tests\LoggingTests\Factory;

use AlaroxRestServeur\Logging\LoggingFactory;
use Tests\TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;

class LoggingFactoryTest extends TestCase
{
    public function testRecupererLogger()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('root'));

        $factory = LoggingFactory::getLogger('logger', vfsStream::url('root'));
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