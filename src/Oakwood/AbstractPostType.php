<?php
namespace Oakwood;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class PostType {

	use Trait\Hooks {
		Trait\Hooks::__construct as __hooks_construct;
	}

	/*
	|--------------------------------------------------------------------------
	| SETTINGS
	|--------------------------------------------------------------------------
	*/

	protected $actions = array( 'init' );

	protected $type = '';

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCT
	|--------------------------------------------------------------------------
	*/

	public function __construct( $type, $properties ) {
		$this->__hooks_construct();

		$this->type = $type;

		foreach ( $properties as $key => $value ) {
			$this->{$key} = $value;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| ACTIONS
	|--------------------------------------------------------------------------
	*/

	public function init() {
		register_post_type( $this->type, get_object_vars( $this ) );
	}

}
