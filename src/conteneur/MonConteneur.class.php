<?php
	namespace Conteneur;

	class MonConteneur extends Conteneur {
		/** @return \Serveur\Rest\RestManager **/
		public function getRestManager() {
			return $this->conteneur['restManager'];
		}

		/** @return \Serveur\Config\Config **/
		public function getConfigManager() {
			return $this->conteneur['configManager'];
		}

		/** @return \Serveur\Exceptions\ErrorManager **/
		public function getErrorManager() {
			return $this->conteneur['errorManager'];
		}

		/** @return \Serveur\I18n\TradManager **/
		public function getTradManager() {
			return $this->conteneur['tradManager'];
		}
	}