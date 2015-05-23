<?php

namespace Oakwood;

/*
|--------------------------------------------------------------------------
| CLASS AbstractTheme
|--------------------------------------------------------------------------
|
| Extend this class in you theme to create your core theme class
|
*/

abstract class AbstractTheme {

	use Traits\Hooks {
		Traits\Hooks::__construct as __hooks_construct;
	}

	/*
	|--------------------------------------------------------------------------
	| SETTINGS
	|--------------------------------------------------------------------------
	*/

	protected $filters = array(
		'wp_nav_menu_objects',
		
		'excerpt_more' => 'excerpt_more',

		'the_content_more_link' => array(
			'priority'  => 10,
			'arguments' => 2
		),

		'embed_oembed_html' => array(
			'hook'      => 'embed_oembed_html',
			'priority'  => 99,
			'arguments' => 3
		)
	);

	protected $actions = array(
		'remove_header_tags' => 'wp',
		'open_graph'         => 'wp_head',
		'after_setup_theme'  => 'after_setup_theme',
		'enqueue_assets'     => 'wp_enqueue_scripts',
		'register_menus'     => 'after_setup_theme',
		'register_sidebars'  => 'widgets_init',
		'register_widgets'   => 'widgets_init'
	);

	public $widgets = array(
		'WP_Nav_Menu_Widget'        => false,
		'WP_Widget_Calendar'        => false,
		'WP_Widget_Links'           => false,
		'WP_Widget_Pages'           => false,
		'WP_Widget_Recent_Comments' => false,
		'WP_Widget_Recent_Posts'    => false,
		'WP_Widget_RSS'             => false,
		'WP_Widget_Search'          => false,
		'WP_Widget_Tag_Cloud'       => false
	);

	public $scripts = array();
	public $styles  = array();

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

	// Adds 'has-children' and/or 'is-child-item' classes to menu items
	public function wp_nav_menu_objects( $items ) {
		$parents = wp_list_pluck( $items, 'menu_item_parent' );

		foreach ( $items as $item ) {
			in_array( $item->ID, $parents ) && $item->classes[] = 'has-children';
			$item->classes[] = $item->post_parent != 0 ? 'is-child-item' : '';
		}

		return $items;
	}

	// Custom excerpt ending
	public function excerpt_more( $more ) {
		return ' ...';
	}

	// Read more link
	public function the_content_more_link( $more_link, $more_link_text ) {
		return str_replace( $more_link_text, __( 'Read more' ), $more_link );
	}

	// Wraps videos with responsive video class
	public function embed_oembed_html() {
		return '<div class="video-crop">' . $html . '</div>';
	}

	/*
	|--------------------------------------------------------------------------
	| ACTIONS
	|--------------------------------------------------------------------------
	*/

	public function remove_header_tags() {
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );
	}

	public function open_graph() {
		$title       = get_bloginfo( 'name' );
		$description = get_bloginfo( 'description' );
		$extra       = '';
		$image       = '';
		$type        = 'website';

		if ( is_single() ) {
			$title = get_the_title();
		}

		if ( is_single() && has_post_thumbnail() ) {
			$thumb = wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' );
			
			if ( isset( $thumb[0] ) && ! empty( $thumb[0] ) )
				$image = $thumb[0];
		}

		echo "\n\n\t<!--=== OPEN GRAPH TAGS ===-->\n";

		if ( ! empty ( $title ) )
			echo "\t<meta property='og:title' content='" . esc_attr( $title ) . "'>\n";

		if ( ! empty ( $description ) )
			echo "\t<meta property='og:description' content='" . esc_attr( $description ) . "'>\n";

		if ( ! empty ( $image ) )
			echo "\t<meta property='og:image' content='" . esc_attr( $image ) . "'>\n";

		if ( ! empty ( $type ) )
			echo "\t<meta property='og:type' content='" . esc_attr( $type ) . "'>\n";

		echo "\t<meta property='og:site_name' content='" . get_bloginfo( 'name' ) . "'>\n";
	}

	public function after_setup_theme() {
		add_theme_support( 'post-thumbnails' );

		if ( ! current_user_can( 'administrator' ) )
			show_admin_bar( false );
	}

	final public function enqueue_assets() {
		foreach ( $this->get_styles( $this->styles ) as $handle => $srcOrOpts ) {
			if ( is_string( $srcOrOpts ) )
				wp_enqueue_style( $handle, $srcOrOpts );
			else
				call_user_func_array( 'wp_enqueue_style', array_merge( array( $handle ), $srcOrOpts ) );
		}

		foreach ( $this->get_scripts( $this->scripts ) as $handle => $srcOrOpts ) {
			if ( is_string( $srcOrOpts ) )
				wp_enqueue_script( $handle, $srcOrOpts );
			else
				call_user_func_array( 'wp_enqueue_script', array_merge( array( $handle ), $srcOrOpts ) );
		}
	}

	public function register_menus() {
		register_nav_menus( array(
			'top_menu'    => __( 'Top Menu' ),
			'footer_menu' => __( 'Footer Menu' )
		) );
	}

	public function register_sidebars() {
		register_sidebar( array(
			'name' => __( 'Standard' ),
			'id'   => 'standard'
		) );
	}

	public function register_widgets() {
		foreach ( $this->get_widgets( $this->widgets ) as $widget => $active ) {
			if ( ! $active )
				unregister_widget( $widget );
			else
				register_widget( 'Oakwood\\Widgets\\' . $widget );
		}
	}

	/*
	|--------------------------------------------------------------------------
	| CUSTOM ACTIONS
	|--------------------------------------------------------------------------
	*/

	public static function body_prepend() {
		do_action( 'body_prepend' );
	}

	/*
	|--------------------------------------------------------------------------
	| GETTERS
	|--------------------------------------------------------------------------
	*/

	public function get_widgets( $widgets = array() ) {
		return $widgets;
	}

	public function get_styles( $styles = array() ) {
		return $styles;
	}

	public function get_scripts( $scripts = array() ) {
		return $scripts;
	}

	/*
	|--------------------------------------------------------------------------
	| STATIC
	|--------------------------------------------------------------------------
	*/

	public static function version() {
		return wp_get_theme()->get( 'Version' );
	}

}
