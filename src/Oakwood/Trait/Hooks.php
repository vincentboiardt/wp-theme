<?php
namespace Oakwood\Trait;

if ( ! defined( 'ABSPATH' ) ) exit;

trait Hooks {

	public $filters = array();
	public $actions = array();

	public function __construct() {
		foreach ( $this->get_filters( $this->filters ) as $hook => $funcOrOpts ) {
			if ( is_string( $funcOrOpts ) ) {
				add_filter( $hook, array( $this, $funcOrOpts ) );
			} else {
				add_filter( $hook, array( $this, $funcOrOpts['function'] ), $funcOrOpts['priority'], $funcOrOpts['arguments'] );
			}
		}

		foreach ( $this->get_actions( $this->actions ) as $hook => $funcOrOpts ) {
			if ( is_string( $funcOrOpts ) ) {
				add_action( $hook, array( $this, $funcOrOpts ) );
			} else {
				add_action( $hook, array( $this, $funcOrOpts['function'] ), $funcOrOpts['priority'], $funcOrOpts['arguments'] );
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
