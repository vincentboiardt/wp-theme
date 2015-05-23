<?php

namespace Oakwood;

/*
|--------------------------------------------------------------------------
| CLASS AbstractAdmin
|--------------------------------------------------------------------------
|
| Extend this class in you theme to create your core theme admin class
|
*/

abstract class AbstractAdmin {

	use Traits\Hooks {
		Traits\Hooks::__construct as __hooks_construct;
	}

	/*
	|--------------------------------------------------------------------------
	| SETTINGS
	|--------------------------------------------------------------------------
	*/

	protected $filters = array(
		'tiny_mce_before_init',
		'mce_buttons'
	);

	protected $actions = array(
		'add_pages'     => 'init',
		'prevent_admin' => 'admin_init',
		'dashboard'     => 'admin_menu'
	);

	public $pages = array(
		array(
			'page_title' => 'Theme Settings',
			'menu_title' => 'Theme',
			'menu_slug'  => 'theme-settings',
			'capability' => 'edit_posts'
		)
	);

	/*
	|--------------------------------------------------------------------------
	| CONSTRUCT
	|--------------------------------------------------------------------------
	*/

	public function __construct() {
		$this->__hooks_construct();
	}

	/*
	|--------------------------------------------------------------------------
	| FILTERS
	|--------------------------------------------------------------------------
	*/

	public function tiny_mce_before_init( $settings ) {
		$settings['extended_valid_elements'] = "iframe[id|class|title|style|align|frameborder|height|scrolling|src|width]";
		$settings['style_formats']           = '[
			{title: "Preamble", inline: "span", classes: "preamble"}
		]';

		return $settings;
	}

	public function mce_buttons( $settings ) {
		$settings[] = 'styleselect';

		return $settings;
	}

	/*
	|--------------------------------------------------------------------------
	| ACTIONS
	|--------------------------------------------------------------------------
	*/

	public function add_pages() {
		if ( function_exists( 'acf_add_options_page' ) ) {
			foreach ( $this->pages as $page ) {
				if ( isset( $page['parent_slug'] ) )
					acf_add_options_sub_page( $page );
				else
					acf_add_options_page( $page );
			}
		}
	}

	public function prevent_admin() {
		if ( ! defined( 'DOING_AJAX' ) && is_admin() && ! current_user_can( 'edit_posts' ) ) {
			wp_redirect( home_url() );
			exit;
		}
	}

	public function dashboard() {
		remove_meta_box( 'dashboard_incoming_links', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_plugins', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_recent_drafts', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_primary', 'dashboard', 'core' );
		remove_meta_box( 'dashboard_secondary', 'dashboard', 'core' );
	}

}
