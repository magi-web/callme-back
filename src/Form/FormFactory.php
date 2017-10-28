<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 14/10/2017
 * Time: 10:25
 */

/**
 * Class FormFactory
 */
class CallMeBack_Form_FormFactory {
    /**
     * @param string $formClass
     * @param mixed $entity
     *
     * @return CallMeBack_Form_AbstractForm
     */
    public function createForm( $formClass, $entity = null ) {
        /** @var CallMeBack_Form_AbstractForm $formInstance */
        $formInstance = new $formClass();
        $formInstance->buildForm();

        $formInstance->hydrateForm( $entity );

        return $formInstance;
    }
}
