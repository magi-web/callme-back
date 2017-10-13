<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 03/10/2017
 * Time: 22:38
 */

/**
 * Class CallMeBack_Controller_DefaultController
 */
class CallMeBack_Controller_DefaultController {
    /**
     * Action d'affichage du formulaire
     */
    public function onFormRenderAction() {
        $form = CallMeBack_Form_PhoneForm::getInstance();
        $form->render_form();
    }


    /**
     * @param $postedData
     */
    public function onPhoneSubmitAction($postedData) {
        $form = CallMeBack_Form_PhoneForm::getInstance();
    }
}