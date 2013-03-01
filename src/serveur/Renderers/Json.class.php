<?php
	namespace Serveur\Renderers;

	class Json extends \Serveur\Renderers\AbstractRenderer {
		/**
		 * @param array $donnees
		 * @return string
		 */
		public function render(array $donnees) {
			return json_encode($donnees);
		}
	}