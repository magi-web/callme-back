<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 04/10/2017
 * Time: 00:30
 */

/**
 * Class CallMeBack_Model_CallMeBack
 */
class CallMeBack_Model_PhoneRequest {
    const STATE_DONE = 1;
    const STATE_NOT_DONE = 0;

    /**
     * @var int
     */
    private $id_call;

    /**
     * @var string
     */
    private $phone_number;

    /**
     * @var string
     */
    private $name;

    /** @var  bool */
    private $done;

    /** @var  DateTime */
    private $date;

    /**
     * CallMeBack_Model_Phone constructor.
     */
    public function __construct() {
        $this->done = false;
        $this->date = new DateTime();
    }

    /**
     * @return int
     */
    public function getIdCall() {
        return $this->id_call;
    }

    /**
     * @param int $id
     *
     * @return CallMeBack_Model_PhoneRequest
     */
    public function setIdCall( $id ) {
        $this->id_call = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhoneNumber() {
        return $this->phone_number;
    }

    /**
     * @param string $phone
     *
     * @return CallMeBack_Model_PhoneRequest
     */
    public function setPhoneNumber( $phone ) {
        $this->phone_number = $phone;

        return $this;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return CallMeBack_Model_PhoneRequest
     */
    public function setName( $name ) {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDone() {
        return $this->done;
    }

    /**
     * @param bool $done
     *
     * @return CallMeBack_Model_PhoneRequest
     */
    public function setDone( $done ) {
        $this->done = $done;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * @param DateTime $date
     *
     * @return CallMeBack_Model_PhoneRequest
     */
    public function setDate( $date ) {
        $this->date = $date;

        return $this;
    }

    /**
     * Retourne la représentation de l'entité sous forme de tableau
     *
     * @return array
     */
    public function toArray() {
        return [
            'id_call' => $this->getIdCall(),
            'name' => $this->getName(),
            'phone_number' => $this->getPhoneNumber(),
            'done' => $this->isDone(),
            'date' => $this->getDate()
        ];
    }

}
