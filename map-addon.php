<?php
/**
 * Plugin Name: Multiple Pin marker widget for elementor
 * Description: Add multiple pin on your map
 * Plugin URI:  https://elementor.com/
 * Version:     1.0.0
 * Author:      Khairul Islam
 * Author URI:  https://developers.elementor.com/
 * Text Domain: abf_wiget
 *
 * Elementor tested up to: 3.7.2
 * Elementor Pro tested up to: 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register oEmbed Widget.
 *
 * Include widget file and register widget class.
 *
 * @since 1.0.0
 * @param \Elementor\Widgets_Manager $widgets_manager Elementor widgets manager.
 * @return void
 */
function register_abf_map_widget( $widgets_manager ) {

    require_once( __DIR__ . '/widgets/abf-map-widget.php' );

    $widgets_manager->register( new \abf_map_Widget() );

}
add_action( 'elementor/widgets/register', 'register_abf_map_widget' );