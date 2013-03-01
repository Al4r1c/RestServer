<?php
	namespace Conteneur;

	class MonConteneur extends Conteneur {
		/** @return \Serveur\Rest\RestManager * */
		public function getRestManager() {
			return $this->conteneur['restManager'];
		}

		/** @return \Serveur\Exceptions\ErrorManager * */
		public function getErrorManager() {
			return $this->conteneur['errorManager'];
		}
	}