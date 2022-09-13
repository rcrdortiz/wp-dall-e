<?php

namespace DallE;

use WP_Http_Curl;

class DallEService {

	private WP_Http_Curl $curl_client;
	private DallEStore $store;

	public function __construct(
		WP_Http_Curl $curl_client,
		DallEStore $store
	) {
		$this->curl_client = $curl_client;
		$this->store       = $store;
	}

	public function load_from_remote() {
		$response = $this->get_tasks();
		$tasks    = $response['data'] ?? [];
		array_walk( $tasks, fn( $response ) => $this->parse_task_response( $response ) );
	}

	private function parse_task_response( $response ) {
		$generations = $response['generations']['data'] ?? [];
		$query       = $response['prompt']['prompt']['caption'] ?? 'No caption found';

		if ( ! $generations ) {
			return false;
		}

		$generations = array_map( fn( $gen ) => $gen['generation']['image_path'] ?? '', $generations );
		$this->store->set_query_data( $query, [ 'done' => $generations ] );

		return $generations;
	}

	public function get_all() {
		$queries = $this->store->list();
		$done    = [];
		$pending = [];
		foreach ( $queries as $query => $data ) {

			if ( isset( $data['done'] ) ) {
				$done[ $query ] = $data['done'];
				continue;
			}

			$task = $data['id'] ?? null;
			if ( $task ) {
				// Update to use parse_task_response instead.
				$task_response = $this->get_task( $task );
				$generations   = $task_response['generations']['data'] ?? [];

				if ( $generations ) {
					foreach ( $generations as $gen ) {
						$done[ $query ][] = $gen['generation']['image_path'] ?? '';
					}
					$this->store->set_query_data( $query, [ 'done' => $done[ $query ] ] );
				} else {
					$pending[] = $query;
				}
			}
		}

		return [ $done, $pending ];
	}

	public function get_tasks() {
		return $this->call( 'https://labs.openai.com/api/labs/tasks' );
	}

	public function get_task( string $task ) {
		return $this->call( "https://labs.openai.com/api/labs/tasks/$task" );
	}

	private function call( string $url, ?string $query = null ) {
		$api_key = $this->store->get_api_key();

		$args = [
			'decompress' => false,
			'user-agent' => 'none',
			'stream'     => null,
			'filename'   => '',
			'method'     => $query ? 'POST' : 'GET',
			'headers'    => [
				"Authorization" => "Bearer $api_key",
				"Content-type"  => 'application/json',
			],
		];

		if ( $query ) {
			$args['body'] = json_encode(
				[
					'task_type' => 'text2im',
					'prompt'    => [
						'caption'    => $query,
						'batch_size' => 4,
					],
				]
			);
		}

		$response = $this->curl_client->request( $url, $args );
		// Error handling and things
		// Handle out of credits and other errors
		$body = wp_remote_retrieve_body( $response );

		return json_decode( $body, true );
	}

	public function generate_image_for_query_blocking( string $query ) {
		$existing_response = $this->store->get_query_data( $query );
		if ( $existing_response ) {
			return $existing_response['done'];
		}

		$response = $this->call( 'https://labs.openai.com/api/labs/tasks', $query );

		$id = $response['id'] ?? null;

		if ( ! $id ) {
			return false;
		}

		$retry = 30;
		$done  = [];
		// Implement exponential backoff.
		while ( $retry -- > 0 && ! $done ) {
			$task_response = $this->get_task( $id );
			$generations   = $task_response['generations']['data'] ?? [];

			foreach ( $generations as $gen ) {
				$done[] = $gen['generation']['image_path'] ?? '';
			}

			sleep( 1 );
		}

		if ( ! $done ) {
			return false;
		}

		$this->store->set_query_data( $query, [ 'done' => $done ] );

		return $done;
	}

	public function generate_image_for_query( string $query ) {
		$response = $this->call( 'https://labs.openai.com/api/labs/tasks', $query );
		$this->store->set_query_data( $query, $response );

		return $response;
	}

	public function set_api_key( $api_key ) {
		$this->store->set_api_key( $api_key );
	}

	public function get_api_key() {
		return $this->store->get_api_key();
	}
}