<?php
/*
 * Plugin Name: Groundhogg - Trials Client (Private Plugin)
 * Plugin URI:  https://www.groundhogg.io/?utm_source=wp-plugins&utm_campaign=plugin-uri&utm_medium=wp-dash
 * Description: Create and stop trials via the funnel builder.
 * Version: 1.0
 * Author: Groundhogg Inc.
 * Author URI: https://www.groundhogg.io/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash
 * Text Domain: groundhogg
 * Domain Path: /languages
 *
 * Groundhogg is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * Groundhogg is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'GROUNDHOGG_TRIALS_CLIENT_VERSION', '1.0' );
define( 'GROUNDHOGG_TRIALS_CLIENT_PREVIOUS_STABLE_VERSION', '0.1' );
define( 'GROUNDHOGG_TRIALS_CLIENT_NAME', 'Trials Client' );

define( 'GROUNDHOGG_TRIALS_CLIENT__FILE__', __FILE__ );
define( 'GROUNDHOGG_TRIALS_CLIENT_PLUGIN_BASE', plugin_basename( GROUNDHOGG_TRIALS_CLIENT__FILE__ ) );
define( 'GROUNDHOGG_TRIALS_CLIENT_PATH', plugin_dir_path( GROUNDHOGG_TRIALS_CLIENT__FILE__ ) );

define( 'GROUNDHOGG_TRIALS_CLIENT_URL', plugins_url( '/', GROUNDHOGG_TRIALS_CLIENT__FILE__ ) );

define( 'GROUNDHOGG_TRIALS_CLIENT_ASSETS_PATH', GROUNDHOGG_TRIALS_CLIENT_PATH . 'assets/' );
define( 'GROUNDHOGG_TRIALS_CLIENT_ASSETS_URL', GROUNDHOGG_TRIALS_CLIENT_URL . 'assets/' );

add_action( 'plugins_loaded', function (){
    load_plugin_textdomain( GROUNDHOGG_TRIALS_CLIENT_TEXT_DOMAIN, false, basename( dirname( __FILE__ ) ) . '/languages' );
} );

define( 'GROUNDHOGG_TRIALS_CLIENT_TEXT_DOMAIN', 'groundhogg' );

if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {
    add_action( 'admin_notices', function(){
        $message = sprintf( esc_html__( '%s requires PHP version %s+, plugin is currently NOT RUNNING.', 'groundhogg' ), GROUNDHOGG_TRIALS_CLIENT_NAME, '5.6' );
        $html_message = sprintf( '<div class="notice notice-error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    } );
} elseif ( ! version_compare( get_bloginfo( 'version' ), '4.9', '>=' ) ) {
    add_action( 'admin_notices', function (){
        $message = sprintf( esc_html__( '%s requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'groundhogg' ), GROUNDHOGG_TRIALS_CLIENT_NAME, '4.9' );
        $html_message = sprintf( '<div class="notice notice-error">%s</div>', wpautop( $message ) );
        echo wp_kses_post( $html_message );
    } );
} else {

    // Groundhogg is loaded, load now.
    if ( did_action( 'groundhogg/loaded' ) ){

        require GROUNDHOGG_TRIALS_CLIENT_PATH . 'includes/plugin.php';

    // Lazy load, wait for Groundhogg!
    } else {
        add_action('groundhogg/loaded', function () {
            require GROUNDHOGG_TRIALS_CLIENT_PATH . 'includes/plugin.php';
        });

        // Might not actually be loaded, so we'll check in later.
        add_action( 'admin_notices', function () {

            // Is not loaded!
            if ( ! defined( 'GROUNDHOGG_VERSION' ) ){
                $message = sprintf(esc_html__('Groundhogg is not currently active, it must be active for %s to work.', 'groundhogg'), GROUNDHOGG_TRIALS_CLIENT_NAME );
                $html_message = sprintf('<div class="notice notice-warning">%s</div>', wpautop($message));
                echo wp_kses_post($html_message);
            }
        });
    }
}


