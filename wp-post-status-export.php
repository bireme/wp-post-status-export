<?php
/*
Plugin Name: WP Post Status Export
Plugin URI: https://github.com/bireme/wp2iahx-export
Description: This plugin show up the WP post status in output format (XML and JSON).
Author: BIREME/OPAS/OMS
Version: 1.0.0
*/

define( 'STATS_EXPORT_DIR', plugin_dir_path(__FILE__) );
define( 'STATS_EXPORT_URL', plugin_dir_url(__FILE__) );

require_once(STATS_EXPORT_DIR . 'functions.php');

if ( !function_exists( 'do_feed_stats' ) ) {
    function do_feed_stats() {
        load_template( STATS_EXPORT_DIR . 'feed-stats.php' );
    }
}

add_action( 'do_feed_stats', 'do_feed_stats', 10, 1 );

?>
