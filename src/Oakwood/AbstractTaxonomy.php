<?php

namespace Oakwood;

/*
|--------------------------------------------------------------------------
| CLASS AbstractTaxonomy
|--------------------------------------------------------------------------
|
| Extend this class in you theme to create a custom taxonomy.
|
| See https://codex.wordpress.org/Function_Reference/register_taxonomy
| for availible properties.
|
*/

abstract class AbstractTaxonomy {

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

	protected $post_types = array();

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
		register_taxonomy( $this->type, $this->post_types, get_object_vars( $this ) );
	}

}
