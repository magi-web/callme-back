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
class CallMeBack_Form_PhoneForm {
    const FORM_PREFIX = 'callme_back';

    /** @var  CallMeBack_Form_PhoneForm */
    private static $instance;

    /**
     * CallMeBack_Form_PhoneForm constructor.
     */
    private function __construct() {}

    /**
     * Retourne l'instance du singleton
     *
     * @return CallMeBack_Form_PhoneForm
     */
    public static function getInstance() {
        if ( empty( static::$instance ) ) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * Retourne le nom du champs formaté pour l'attribut name des inputs du formulaire
     *
     * @param string $field
     *
     * @return string
     */
    private function getFieldName($field) {
        return static::FORM_PREFIX . "[$field]";
    }

    /**
     * Rendu du formulaire
     */
    public function render_form() {
        ?>
        <form class="search-form callme-form" method="post">
            <div class="control">
                <label for="name"><span class="screen-reader-text"><?php echo __( 'Your name', 'callme-back' ); ?> :</span></label>
                <input id="name" class="search-field" name="<?php echo $this->getFieldName('name'); ?>" type="text" required="required" placeholder="<?php echo __( 'Your name', 'callme-back' ); ?>" />
            </div>

            <div class="control">
                <label for="phone"><span class="screen-reader-text"><?php echo __( 'Your phone number', 'callme-back' ); ?> :</span></label>
                <input id="phone" class="search-field" name="<?php echo $this->getFieldName('phone'); ?>" type="tel" required="required" placeholder="<?php echo __( 'Your phone number', 'callme-back' ); ?>" />
                <button type="submit" title="<?php echo __( 'CallMe back for free', 'callme-back' ); ?>" class="search-submit"><i class="hc-icon-phone"></i><span class="screen-reader-text"><?php echo __( 'CallMe back for free', 'callme-back' ); ?></span></button>
            </div>

        </form>
        <?php
    }
}