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
class CallMeBack_Controller_DefaultController extends CallMeBack_Controller_AbstractController {
    /**
     * Action d'affichage du formulaire
     */
    public function phoneRequestAction() {
        $formFactory = new CallMeBack_Form_FormFactory();
        /** @var CallMeBack_Form_PhoneForm $form */
        $form = $formFactory->createForm(CallMeBack_Form_PhoneForm::class, new CallMeBack_Model_PhoneRequest());

        if ( $form->isSubmitted() ) {
            $form->handleRequest();

            if($form->isValid()) {
                $phoneRequest = $form->getData();

                try {
                    $phoneRepository = new CallMeBack_Repository_PhoneRequestRepository();
                    $phoneRepository->save($phoneRequest);

                    if ( get_option( 'callmeback_send_mail_notification', false ) ) {
                        $subject = __( '[CallMeBack] new Request', CallMeBack::TEXT_DOMAIN );
                        $message = $this->render( 'phone_request_email', [ 'phoneRequest' => $phoneRequest ] );
                        wp_mail( get_option( 'admin_email' ), $subject, $message );
                    }

                    $message = __("Your request has been successfully submitted.<br>We will call you back as soon as possible.", CallMeBack::TEXT_DOMAIN);
                    $sessionMessage = new CallMeBack_Utils_SessionMessage();
                    $sessionMessage->add('success', $message);

                } catch (Exception $e) {
                    $message = __("An error occured while submitting your informations.<br>Please try again later.", CallMeBack::TEXT_DOMAIN);

                    $sessionMessage = new CallMeBack_Utils_SessionMessage();
                    $sessionMessage->add('error', $message);
                }
            } else {
                $message = __("The form contains some errors. Please review them before.", CallMeBack::TEXT_DOMAIN);

                $sessionMessage = new CallMeBack_Utils_SessionMessage();
                $sessionMessage->add('error', $message);
            }
        }

        echo $this->render('phone_request_form', ['form' => $form]);
    }
}
