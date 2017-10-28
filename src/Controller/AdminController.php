<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 04/10/2017
 * Time: 00:44
 */

/**
 * Class CallMeBack_Controller_AdminController
 */
class CallMeBack_Controller_AdminController extends CallMeBack_Controller_AbstractController {
    /**
     * CallMeBack_Controller_AdminController constructor.
     */
    public function __construct() {
    }

    /**
     * Gestion de la liste des entrées
     */
    public function indexAction() {
        $listBlock = new CallMeBack_Block_Admin_PhoneRequestList();
        $listBlock->prepareItems();

        echo $this->render('phone_list', ['listBlock' => $listBlock]);
    }

    /**
     * Action propre aux options du plugin
     */
    public function settingsAction() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $settingsBlock = new CallMeBack_Block_Admin_Settings();
        $settingsBlock->prepareSettings();

        echo $this->render('settings', ['settingsBlock' => $settingsBlock]);
    }
}
