<?php
    namespace Tests\ServeurTests\Reponse;

    use Tests\TestCase;
    use Serveur\Reponse\Header\Header;

    class HeaderManagerTest extends TestCase
    {
        /** @var Header */
        private $headerManager;

        public function setUp()
        {
            $this->headerManager = new Header();
        }

        public function testAjouterHeader()
        {
            $this->headerManager->ajouterHeader('Content-type', 'application/pdf');
            $this->headerManager->ajouterHeader('Expires', '0');

            $this->assertAttributeCount(2, '_headers', $this->headerManager);
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testAjouterHeaderChampNonString()
        {
            $this->headerManager->ajouterHeader(null, 'application/pdf');
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testAjouterHeaderValeurNonString()
        {
            $this->headerManager->ajouterHeader('Content-type', 5);
        }

        /**
         * @expectedException     \Serveur\GestionErreurs\Exceptions\MainException
         * @expectedExceptionCode 40100
         */
        public function testAjouterHeaderSiValide()
        {
            $this->headerManager->ajouterHeader('WRONG', 'FAKE');
        }

        /**
         * @runInSeparateProcess
         */
        public function testEnvoiHeader()
        {
            $this->headerManager->ajouterHeader('Content-type', 'application/xml');
            $this->headerManager->ajouterHeader('Content-Length', '21245');

            $this->headerManager->envoyerHeaders();

            $headersList = xdebug_get_headers();
            $this->assertContains('Content-type: application/xml', $headersList);
            $this->assertCount(2, $headersList);
        }
    }