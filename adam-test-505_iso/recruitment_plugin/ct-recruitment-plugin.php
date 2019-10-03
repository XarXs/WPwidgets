<?php
/**
 * @wordpress-plugin
 * Plugin Name: createIT Recruitment plugin
 * Description:
 * Version: 0.0.1
 * Author: Adam Chabros
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}
/** Loads plugin class file. */
require_once( dirname( __FILE__ ) . '/Ct_Recruitment_Plugin.php' );
/**
 * Creates an instance of the class
 * and calls its initialization method.
 */
function ct_recruitment_plugin_run() {

    $ct_r_plug = new Ct_Recruitment_Plugin();
    $ct_r_plug->init();


}
ct_recruitment_plugin_run();