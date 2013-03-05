<?php
    namespace Modules\ServeurTests\Rest;

    include_once(__DIR__ . '/../../../TestEnv.php');

    use Modules\TestCase;
    use Serveur\Rest\HeaderManager;

    class HeaderManagerTest extends TestCase {
        /** @var HeaderManager */
        private $headerManager;

        public function setUp() {
            $this->headerManager = new HeaderManager();
        }

        public function testAjouterHeader() {
            $this->headerManager->ajouterHeader('Content-type', 'application/pdf');
            $this->headerManager->ajouterHeader('Expires', '0');

            $this->assertAttributeCount(2, 'headers', $this->headerManager);
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testAjouterHeaderChampNonString() {
            $this->headerManager->ajouterHeader(null, 'application/pdf');
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\ArgumentTypeException
         * @expectedExceptionCode 1000
         */
        public function testAjouterHeaderValeurNonString() {
            $this->headerManager->ajouterHeader('Content-type', 5);
        }

        /**
         * @expectedException     \Serveur\Exceptions\Exceptions\MainException
         * @expectedExceptionCode 20400
         */
        public function testAjouterHeaderSiValide() {
            $this->headerManager->ajouterHeader('WRONG', 'FAKE');
        }

        /**
         * @runInSeparateProcess
         */
        public function testEnvoiHeader() {
            $this->headerManager->ajouterHeader('Content-type', 'application/xml');
            $this->headerManager->ajouterHeader('Content-Length', '21245');

            $this->headerManager->envoyerHeaders();

            $headersList = xdebug_get_headers();
            $this->assertContains('Content-type: application/xml', $headersList);
            $this->assertCount(2, $headersList);
        }
    }