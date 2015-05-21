<?php

namespace Oakwood\Traits;

/*
|--------------------------------------------------------------------------
| TRAIT Hooks
|--------------------------------------------------------------------------
|
| This is a trait for managing hooks in wordpress.
| Can be used in any class for handling filters & actions easy.
|
*/

trait Hooks {

	public function __construct() {
		$this->init_hooks( 'filter' );
		$this->init_hooks( 'action' );
	}

	private function init_hooks( $type = 'action' ) {
		$property = $type . 's';
		$getter   = 'get_' . $property;
		$setter   = 'add_' . $type;

		if (!isset($this->{$property}))
			return;

		foreach ( $this->{$getter}( $this->{$property} ) as $key => $value ) {
			if ( is_string( $key ) ) {
				if ( is_string( $value ) ) {
					$setter( $value, array( $this, $key ) );
				}
				if ( is_array( $value ) ) {
					$setter( isset( $value['hook'] ) ? $value['hook'] : $key, array( $this, $key ), $value['priority'], $value['arguments'] );
				}
			} else {
				$setter( $value, array( $this, $value ) );
			}
		}
	}

	public function get_filters( $filters = array() ) {
		return $filters;
	}

	public function get_actions( $actions = array() ) {
		return $actions;
	}

}
