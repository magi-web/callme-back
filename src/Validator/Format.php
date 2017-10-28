<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 22/10/2017
 * Time: 10:27
 */

/**
 * Class CallMeBack_Validator_Format
 */
class CallMeBack_Validator_Format {
    private $format = '';

    /**
     * CallMeBack_Validator_Format constructor.
     *
     * @param $format
     */
    public function __construct($format) {
        $this->format = $format;
    }

    /**
     * Validate the value with the inner format
     *
     * @param $value
     *
     * @return bool
     */
    public function validate($value) {
        if($this->format !== '') {
            return preg_match($this->format, $value) === 1;
        }
        return true;
    }
}
