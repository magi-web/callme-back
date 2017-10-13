<?php
/*
Plugin Name: Call Me Back
Description: Add a call me back button and store the requests into database
Author: Mathieu Girard
Text Domain: callme-back
Domain Path: /ressources/languages
Version: 0.1
*/

/* Prevent direct access to this file */
if ( ! defined( 'ABSPATH' ) ) {
    exit( "Sorry, you are not allowed to access this file directly." );
}

spl_autoload_register( 'callmeback_autoloader' );
/**
 * Autoloader du plugin
 * @param string $class_name
 */
function callmeback_autoloader( $class_name ) {
    if ( false !== strpos( $class_name, 'CallMeBack' ) ) {
        $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
        $class_file = str_replace( array('CallMeBack_', '_'), array('', DIRECTORY_SEPARATOR), $class_name ) . '.php';
        require_once $classes_dir . $class_file;
    }
}

/**
 * Class CallMeBack
 */
class CallMeBack {
    /** ================================================================================================================
     * Initialisation du plugin
     *
     * @return void
     */
    static $instance = false;

    /**
     * CallMeBack constructor.
     */
    protected function __construct() {
        if ( is_callable( array( $this, '_init' ) ) ) {
            $this->_init();
        }
    }

    /**
     * Initialization method
     */
    protected function _init() {
        CallMeBack_EventDispatcher::getInstance();
    }

    /**
     * Function to instantiate our class and make it a singleton
     */
    public static function getInstance() {
        if ( ! static::$instance ) {
            static::$instance = new CallMeBack();
        }

        return static::$instance;
    }
}

$callMe = CallMeBack::getInstance();