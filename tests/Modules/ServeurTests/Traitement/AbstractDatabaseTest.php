<?php
    namespace Tests\ServeurTests\Traitement;

    use Serveur\Traitement\Data\AbstractDatabase;
    use Tests\MockArg;
    use Tests\TestCase;

    class AbstractDatabaseTest extends TestCase
    {
        public function testSetConnection() {
            /**
             * @var $abstractDatabase AbstractDatabase
             */
            $abstractDatabase = $this->createMock('AbstractDatabase');

            $objectConnection = new \stdClass();

            $abstractDatabase->setConnection($objectConnection);
            $this->assertEquals($objectConnection, $abstractDatabase->getConnection());
        }
    }