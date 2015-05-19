<?php
namespace Oakwood\Trait;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Hooks {

	protected $filters = array();
	protected $actions = array();

	public function __construct() {
		$this->init_hooks( 'filter' );
		$this->init_hooks( 'action' );
	}

	private function init_hooks( $type = 'action' ) {
		$property = $type . 's';
		$getter   = 'get_' . $property;
		$setter   = 'add_' . $type;

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
