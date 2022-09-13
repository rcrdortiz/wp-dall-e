<?php

namespace DallE;

class DallEAdmin {

	private DallEService $dall_e;

	public function __construct( DallEService $dall_e ) {
		$this->dall_e = $dall_e;
	}

	public function generate_image_from_admin( string $query ) {
		$this->dall_e->generate_image_for_query( $query );
	}

	public function dalle_when_is_admin() {
		$this->dall_e->load_from_remote();
		[$done, $pending] = $this->dall_e->get_all();
		$api_key = $this->dall_e->get_api_key();

		include DALL_E_PATH . '/views/dall-e-admin.php';
	}

	public function set_api_key( $api_key ) {
		$this->dall_e->set_api_key( $api_key );
	}
}