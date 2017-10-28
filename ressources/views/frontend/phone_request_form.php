<?php
/**
 * @var $form CallMeBack_Form_PhoneForm
 */

wp_enqueue_script('jquery-mask', CallMeBack::getPluginDirUrl() . 'ressources/assets/js/jquery.mask.min.js');
?>
<form class="search-form callme-form" method="post">
    <?php
        $sessionMessages = new CallMeBack_Utils_SessionMessage();

        $messages = $sessionMessages->get('success');
        if(!empty($messages)) {
            foreach ($messages as $message) {
                echo "<div class=\"control text-success bg-success\">" . $message . "</div>";
            }
        }

        $messages = $sessionMessages->get('error');
        if(!empty($messages)) {
            echo "<ul class=\"errors bg-danger\">";
            foreach ($messages as $message) {
                echo "<li class=\"text-error\">" . $message . "</li>";
            }
            echo "</ul>";
        }
    ?>
    <div class="control">
        <?php $form->renderLabel('name') ?>
        <?php $form->renderWidget('name') ?>
        <?php $form->renderErrors('name') ?>
    </div>

    <div class="control">
        <?php $form->renderLabel('phone_number') ?>
        <?php $form->renderWidget('phone_number') ?>
        <button type="submit" title="<?php echo __( 'CallMe back for free', 'callme-back' ); ?>" class="search-submit">
            <i class="hc-icon-phone"></i><span
                    class="screen-reader-text"><?php echo __( 'CallMe back for free', 'callme-back' ); ?></span>
        </button>
    </div>
    <?php $form->renderErrors('phone_number') ?>
</form>
