<?php

namespace DallE\Base;

abstract class OptionStore {

	private array $data;
	private bool $update = false;

	final public function __construct() {
		$this->data = get_option( $this->get_storage_key(), [] );
	}

	final public function __destruct() {
		if ( $this->update ) {
			update_option( $this->get_storage_key(), $this->data );
		}
	}

	final protected function set( string $key, $value ): bool {
		if ( $this->get( $key ) != $value ) {
			$this->data[ $key ] = $value;
			$this->update       = true;

			return true;
		}

		return false;
	}

	final protected function get( string $key ) {
		return $this->data[$key] ?? null;
	}

	final public function list() {
		return $this->data;
	}

	protected abstract function get_storage_key(): string;
}