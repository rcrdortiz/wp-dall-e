<?php

namespace DallE;

class DallEAjax {

	private DallEService $dall_e;

	public function __construct( DallEService $dall_e ) {
		add_action( 'wp_ajax_get_dall_e_images', fn( $data ) => $this->get_images_on_wp_ajax_get_dall_e_images( $data ) );
		$this->dall_e = $dall_e;
	}

	public function get_images_on_wp_ajax_get_dall_e_images() {
		$query = $_REQUEST['task'];

		if ( ! $query ) {
			wp_send_json_error( 'No query provided' );
		}

		$response = $this->dall_e->generate_image_for_query_blocking( $query );

		if ( ! $response ) {
			wp_send_json_error( 'Could not generate images' );
		}

		wp_send_json( $response );
	}
}