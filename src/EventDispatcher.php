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

    /**
     * CallMeBack_EventDispatcher constructor.
     */
    private function __construct() {
        if ( is_callable( array( $this, '__init' ) ) ) {
            $this->__init();
        }
    }

    /**
     * Initialization method
     */
    protected function __init() {
        if ( ! defined( 'CMB_AJAX' ) ) {
            $setup = new CallMeBack_Model_Setup();
            //Init et des-init
            register_activation_hook( __FILE__, array( $setup, 'installData' ) );
            register_deactivation_hook( __FILE__, array( $setup, 'deactivate' ) );
            register_uninstall_hook( __FILE__, array( $setup, 'uninstallRemovedata' ) );

            if ( is_admin() ) {
                add_action( 'admin_menu', array( $this, 'initAdminSetupMenu' ) );
                add_action( 'admin_init', array( CallMeBack_Block_Admin_Settings::class, 'registerOptions' ) );
            } else {
                $controller = new CallMeBack_Controller_DefaultController();
                add_shortcode( 'callmeback_form', array( $controller, 'phoneRequestAction' ) );
            }
        }

        add_action( 'rest_api_init', function() {
            $restController = new CallMeBack_Controller_RestController();
            $restController->registerRoutes();
        });
    }

    public function initAdminSetupMenu(){
        $adminController = new CallMeBack_Controller_AdminController();
        add_filter( 'set-screen-option', [ CallMeBack_Block_Admin_PhoneRequestList::class, 'setScreenOption' ], 10, 3 );

        add_menu_page( 'Call Me Back', 'Call Me Back', 'manage_options', 'callme-back',  array ($adminController, 'indexAction'), 'dashicons-phone' );

        $hook = add_submenu_page(
            'callme-back',
            __('Plugin', CallMeBack::TEXT_DOMAIN),
            __('Plugin', CallMeBack::TEXT_DOMAIN),
            'manage_options',
            'callme-back',
            array ($adminController, 'indexAction')
        );
        add_action( "load-$hook", [ CallMeBack_Block_Admin_PhoneRequestList::class, 'screenOption' ] );

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
