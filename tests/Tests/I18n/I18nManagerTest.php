<?php
	namespace Tests\I18n;

	include_once(__DIR__ . '/../../TestEnv.php');

	use Tests\TestCase;

	class I18nManagerTest extends TestCase {

		/** @var \Serveur\I18n\I18nManager */
		private $tradManager;

		public function setUp() {
			$this->tradManager = new \Serveur\I18n\I18nManager();
		}
	}