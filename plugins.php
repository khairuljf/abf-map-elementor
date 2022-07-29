<?php
/**
 * Plugin Name: Abf Elementor Addons
 * Description: Abf Addons for Elementor  plan is cover maximum plugins & 5 widgets in per months.
 * Version:     1.0.0
 * Author:      Khairul Islam
 * Author URI:  https://github.com/khairuljf
 * Plugin URI:  https://github.com/khairuljf/abf-map-elementor
 * Text Domain: abf_addon
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}


function register_abf_addons( $widgets_manager ) {

    require_once( __DIR__ . '/widgets/abf-map-widget.php' );

    $widgets_manager->register( new \abf_map_Widget() );

}
add_action( 'elementor/widgets/register', 'register_abf_addons' );
