<?php
namespace OWC;

if ( ! defined( 'ABSPATH' ) ) exit;

abstract class AbstractCron {
	public function __construct() {
		add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
		
		if ( ! wp_next_scheduled( 'owc_clear_transients' ) ) {
			wp_schedule_event( time(), 'daily', 'owc_clear_transients' );
		}
		add_action( 'owc_clear_transients', array( $this, 'clear_transients' ) );
	}

	public function cron_schedules( $schedules ) {
		$schedules['15min'] = array(
			'interval' => 60 * 15,
			'display'  => __( 'Every 15 minutes' )
		);

		return $schedules;
	}

	public function clear_transients() {
		global $wpdb;

		$time = strtotime( '- 7 days' );
		
		if ( $time > time() || $time < 1 )
			return false;

		$transients = $wpdb->get_col( $wpdb->prepare( "SELECT REPLACE( option_name, '_transient_timeout_', '' ) AS transient_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_timeout_%%' AND option_value < %s", $time ) );
		
		foreach( $transients as $transient_name ) {
			get_transient( $transient_name );
		}
	}
}