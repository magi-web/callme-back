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
    throw new Exception( "Sorry, you are not allowed to access this file directly." );
}

/**
 * Class CallMeBack
 */
class CallMeBack {
    const TEXT_DOMAIN = 'callme-back';
    /** ================================================================================================================
     * Initialisation du plugin
     *
     * @return void
     */
    private static $instance = false;

    /**
     * CallMeBack constructor.
     */
    protected function __construct() {
        if ( is_callable( array( $this, '__init' ) ) ) {
            $this->__init();
        }
    }

    /**
     * Initialization method
     */
    protected function __init() {
        spl_autoload_register( array( $this, 'autoloader' ) );

        CallMeBack_EventDispatcher::getInstance();
    }

    /**
     * @return string
     */
    public static function getTemplateDir() {
        $subDir = is_admin() ? 'admin' : 'frontend';
        return static::getPluginDir()
               . DIRECTORY_SEPARATOR . 'ressources'
               . DIRECTORY_SEPARATOR . 'views'
               . DIRECTORY_SEPARATOR . $subDir . DIRECTORY_SEPARATOR;
    }

    /**
     * Returns the current plugin directory
     *
     * @return string
     */
    public static function getPluginDir() {
        return plugin_dir_path( __FILE__ );
    }

    /**
     * Returns the current plugin directory
     *
     * @return string
     */
    public static function getPluginDirUrl() {
        return plugin_dir_url( __FILE__ );
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

    /**
     * Autoloader du plugin
     *
     * @param string $class_name
     */
    private function autoloader( $class_name ) {
        if ( false !== strpos( $class_name, 'CallMeBack' ) ) {
            $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
            $class_file  = str_replace( array( 'CallMeBack_', '_' ), array(
                    '',
                    DIRECTORY_SEPARATOR
                ), $class_name ) . '.php';
            require_once $classes_dir . $class_file;
        }
    }
}

$callMe = CallMeBack::getInstance();
