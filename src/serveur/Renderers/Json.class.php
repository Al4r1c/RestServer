<?php
	namespace Serveur\Renderers;

	class Json extends \Serveur\Renderers\AbstractRenderer {
		public function render(array $donnees) {
			return json_encode($donnees);
		}
	}