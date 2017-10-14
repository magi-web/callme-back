<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 23/10/2017
 * Time: 22:21
 */

// add error/update messages

// check if the user have submitted the settings
// wordpress will add the "settings-updated" $_GET parameter to the url
if ( isset( $_GET['settings-updated'] ) ) {
    // add settings saved message with the class of "updated"
    add_settings_error( 'cmb_messages', 'cmb_message', __( 'Settings Saved', CallMeBack::TEXT_DOMAIN ), 'updated' );
}

// show error/update messages
settings_errors( 'cmb_messages' );
?>
<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
    <form class="callmeback-settings" action="options.php" method="post">
        <?php
        // output security fields for the registered setting "wporg"
        settings_fields( 'callmeback' );
        // output setting sections and their fields
        // (sections are registered for "wporg", each field is registered to a specific section)
        do_settings_sections( 'callme-back-settings' );
        // output save settings button
        submit_button( __('Save Settings', CallMeBack::TEXT_DOMAIN) );
        ?>
    </form>
</div>
