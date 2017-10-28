<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 14/10/2017
 * Time: 21:31
 */

/**
 * Class CallMeBack_Utils_SessionMessage
 */
class CallMeBack_Utils_SessionMessage {
    private $messagesPool = [];

    /**
     * CallMeBack_Utils_SessionMessage constructor.
     */
    public function __construct() {
        if(isset($_SESSION['callmeback_messages'])) {
            $this->messagesPool = $_SESSION['callmeback_messages'];
        }
    }

    /**
     * Ajoute un message au pool
     *
     * @param string $channel
     * @param string $message
     */
    public function add($channel, $message) {
        if(!array_key_exists($channel, $this->messagesPool)) {
            $this->messagesPool[$channel] = [];
        }
        $this->messagesPool[$channel][] = $message;
        $_SESSION['callmeback_messages'] = $this->messagesPool;
    }

    /**
     * Retourne un message du canal en entrée
     *
     * @param string $channel
     *
     * @return array
     */
    public function get($channel) {
        $messages = array();
        if(array_key_exists($channel, $this->messagesPool)) {
            $messages = $this->messagesPool[$channel];

            unset($this->messagesPool[$channel]);
            $_SESSION['callmeback_messages'] = $this->messagesPool;
        }
        return $messages;
    }
}
