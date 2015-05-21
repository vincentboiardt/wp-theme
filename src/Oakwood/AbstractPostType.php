<?php

namespace Oakwood;

/*
|--------------------------------------------------------------------------
| CLASS AbstractPostType
|--------------------------------------------------------------------------
|
| Extend this class in you theme to create a custom post type.
|
| See https://codex.wordpress.org/Function_Reference/register_post_type
| for availible properties.
|
*/

abstract class AbstractPostType {

	use Traits\Hooks {
		Traits\Hooks::__construct as __hooks_construct;
	}

	/*
	|--------------------------------------------------------------------------
	| SETTINGS
	|--------------------------------------------------------------------------
	*/

	protected $actions = array( 'init' );

	protected $type = '';

	public $public = true;

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCT
	|--------------------------------------------------------------------------
	*/

	public function __construct( $type = null, $properties = array() ) {
		$this->__hooks_construct();

		if ( $type )
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
