<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 03/10/2017
 * Time: 23:09
 */

/**
 * Class CallMeBack_Form_PhoneForm
 */
class CallMeBack_Form_PhoneForm extends CallMeBack_Form_AbstractForm {
    const FORM_PREFIX = 'callme_back';

    /**
     * @return string
     */
    public function getBlockPrefix() {
        return static::FORM_PREFIX;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm() {
        $this->formFields = [
            'name'         => [
                'label'           => 'Your name :',
                'type'            => 'text',
                'placeholder'     => 'Your name',
                'required'        => true,
                'attr'            => [
                    'class' => 'search-field'
                ],
                'invalid_message' => 'This field is mandatory'
            ],
            'phone_number' => [
                'label'                  => 'Your phone number :',
                'type'                   => 'tel',
                'placeholder'            => 'Your phone number',
                'required'               => true,
                'format'                 => get_option( 'cmb_phone_format', '' ),
                'attr'                   => [
                    'class' => 'search-field',
                    'data-mask' => get_option( 'cmb_phone_mask', '' )
                ],
                'invalid_message'        => 'This field is mandatory',
                'invalid_format_message' => 'This value does not match the format'
            ]
        ];
    }
}
