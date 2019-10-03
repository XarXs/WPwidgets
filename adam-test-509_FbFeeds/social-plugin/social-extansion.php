<?php
/**
 * @wordpress-plugin
 * Plugin Name: Social Extansion
 * Description:
 * Version: 0.0.1
 * Author: Adam Chabros
 */


require_once plugin_dir_path( __FILE__ ) . 'inc/tweet2json.php';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die('Forbidden');
}

add_action( 'after_setup_theme', 'vc_before_init_actions' );
function vc_before_init_actions() {
    require_once(dirname(__FILE__) . '/shortcodes/class-fw-shortcode-ct-social.php');
    require_once(dirname(__FILE__) . '/shortcodes//class-fw-shortcode-ct-twitter.php');
    require_once(dirname(__FILE__) . '/shortcodes//class-fw-shortcode-ct-facebook.php');
}

function init_scripts(){
    $pluginpath = plugins_url('adam-test-2/');
    wp_register_style( 'ct_fa', $pluginpath . 'assets/font-awesome/css/font-awesome.min.css' );
    wp_enqueue_style( 'ct_fa' );
}

add_action('init', 'init_scripts' );