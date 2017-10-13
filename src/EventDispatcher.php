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
            new CallMeBack_Controller_AdminController();
        } else {
            $controller = new CallMeBack_Controller_DefaultController();
            $this->controllers['default'] = $controller;
            add_shortcode( 'callmeback_form', array( $controller, 'onFormRenderAction' ) );

            add_action( 'parse_request', array($this, 'parse_request'), 20 );
        }
    }
    /**
     * Intercepte les éléments posts si le formulaire a été soumis pour délencher les actions adéquates
     */
    public function parse_request() {
        if ( isset( $_POST[CallMeBack_Form_PhoneForm::FORM_PREFIX] ) ) {
            $this->controllers['default']->onPhoneSubmitAction($_POST[CallMeBack_Form_PhoneForm::FORM_PREFIX]);
            exit;
        }
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