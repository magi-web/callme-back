<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 07/10/2017
 * Time: 16:21
 */

/**
 * Class CallMeBack_EventDispatcher
 */
class CallMeBack_EventDispatcher {
    /**
     * @var bool|CallMeBack_EventDispatcher
     */
    private static $instance = false;

    private $controllers = [];

    /**
     * CallMeBack_EventDispatcher constructor.
     */
    private function __construct() {
        if ( is_callable( array( $this, '_init' ) ) ) {
            $this->_init();
        }
    }

    /**
     * Initialization method
     */
    protected function _init() {
        $setup = new CallMeBack_Model_Setup();
        //Init et des-init
        register_activation_hook( __FILE__, array( $setup, 'install_data' ) );
        register_deactivation_hook( __FILE__, array( $setup, 'deactivate' ) );
        register_uninstall_hook( __FILE__, array( $setup, 'uninstall_removedata' ) );

        if(is_admin()) {
            add_action('admin_menu', array($this, 'callmeback_plugin_setup_menu'));
            add_action( 'admin_init', array( CallMeBack_Block_Admin_Settings::class, 'registerOptions' ) );
        } else {
            //TODO utiliser une classe dédiée pour les routes et les shortcodes
            $controller = new CallMeBack_Controller_DefaultController();
            $this->controllers['default'] = $controller;
            add_shortcode( 'callmeback_form', array( $controller, 'phoneRequestAction' ) );

            add_action( 'rest_api_init', function() {
                $restController = new CallMeBack_Controller_RestController();
                $restController->register_routes();
            });
        }
    }

    function callmeback_plugin_setup_menu(){
        $adminController = new CallMeBack_Controller_AdminController();
        add_filter( 'set-screen-option', [ CallMeBack_Block_Admin_PhoneRequestList::class, 'set_screen' ], 10, 3 );

        add_menu_page( 'Call Me Back', 'Call Me Back', 'manage_options', 'callme-back',  array ($adminController, 'indexAction'), 'dashicons-phone' );

        $hook = add_submenu_page(
            'callme-back',
            __('Plugin', CallMeBack::TEXT_DOMAIN),
            __('Plugin', CallMeBack::TEXT_DOMAIN),
            'manage_options',
            'callme-back',
            array ($adminController, 'indexAction')
        );
        add_action( "load-$hook", [ CallMeBack_Block_Admin_PhoneRequestList::class, 'screen_option' ] );

        add_submenu_page(
            'callme-back',
            __('Call Me Back Settings', CallMeBack::TEXT_DOMAIN),
            __('Settings', CallMeBack::TEXT_DOMAIN),
            'manage_options',
            'callme-back-settings',
            array ($adminController, 'settingsAction')
        );
    }

    /**
     * Retourne l'instance du singleton
     *
     * @return CallMeBack_EventDispatcher
     */
    public static function getInstance() {
        if ( empty( static::$instance ) ) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}