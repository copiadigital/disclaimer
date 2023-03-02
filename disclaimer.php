<?php
/**
* Plugin Name:  Disclaimer
* Text Domain:  disclaimer
* Description:  Disclaimer
* Version:      1.0.0
* Author:       Copia Digital
* Author URI:   https://www.copiadigital.com/
* License:      MIT License
*/

$autoload_path = __DIR__.'/vendor/autoload.php';
if ( file_exists( $autoload_path ) ) {
    require_once( $autoload_path );
}

define( 'DISCLAIMER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'DISCLAIMER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

$clover = new Disclaimer\Providers\DisclaimerServiceProvider;
$clover->register();

add_action('init', [$clover, 'boot']);

add_action('plugins_loaded', function() {
    if (!class_exists('acf') && !class_exists('acf_pro') && !function_exists('acf_add_options_page')) {
        deactivate_plugins('disclaimer/disclaimer.php');
        add_action( 'admin_notices', function() {
            $class = 'notice notice-error';
            $message = __( 'ACF Class or ACF Pro not found!', 'disclaimer' );
  
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
        } );
    }
});