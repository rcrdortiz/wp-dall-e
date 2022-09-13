<?php

namespace DallE;

use DallE\Base\OptionStore;

class DallEStore extends OptionStore {

	public function set_query_data( $query, $response ) {
		$this->set( $query, $response );
	}

	public function get_query_data( $query ) {
		return $this->get( $query );
	}

	public function set_api_key( string $api_key ) {
		return $this->set( 'api_key', $api_key );
	}

	public function get_api_key() {
		return $this->get( 'api_key' );
	}

	protected function get_storage_key(): string {
		return 'dall_e_request_store';
	}
}