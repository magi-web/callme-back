<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 23/10/2017
 * Time: 22:03
 */

/**
 * Class CallMeBack_Block_Admin_Settings
 */
class CallMeBack_Block_Admin_Settings {

    /**
     * Registers the plugin's options
     */
    public static function registerOptions() {
        add_option( 'cmb_phone_format', '');
        add_option( 'cmb_phone_mask', '');

        // register a new setting for "callmeback" page
        register_setting( 'callmeback', 'cmb_phone_format' );
        register_setting( 'callmeback', 'cmb_phone_mask' );
    }

    /**
     * Prepare the settings
     */
    public function prepareSettings() {
        // register a new section in the "wporg" page
        add_settings_section(
            'cmb_section_developers',
            '',
            array($this, 'renderMainSection'),
            'callme-back-settings'
        );

        // register a new field in the "wporg_section_developers" section, inside the "wporg" page
        add_settings_field(
            'cmb_phone_format', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __( 'Phone validation format', CallMeBack::TEXT_DOMAIN ) . ' *',
            array($this, 'renderOption'),
            'callme-back-settings',
            'cmb_section_developers',
            [
                'label_for' => 'cmb_phone_format',
                'class' => 'cmb_row'
            ]
        );

        // register a new field in the "wporg_section_developers" section, inside the "wporg" page
        add_settings_field(
            'cmb_phone_mask', // as of WP 4.6 this value is used only internally
            // use $args' label_for to populate the id inside the callback
            __( 'Phone mask format', CallMeBack::TEXT_DOMAIN ) . ' *',
            array($this, 'renderOption'),
            'callme-back-settings',
            'cmb_section_developers',
            [
                'label_for' => 'cmb_phone_mask',
                'class' => ''
            ]
        );
    }

    /**
     * Add a sub text to the section
     *
     * @param array $args
     */
    public function renderMainSection( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Please fulfill the form', CallMeBack::TEXT_DOMAIN ); ?></p>
        <?php
    }

    /**
     * Renders an option
     *
     * @param array $args
     */
    public function renderOption($args) {
        // get the value of the setting we've registered with register_setting()
        $option = get_option( $args['label_for'] );
        ?>
        <input id="<?php echo esc_attr( $args['label_for'] ); ?>" type="text" required="required" name="<?php echo esc_attr( $args['label_for'] ); ?>" value="<?php echo $option ?>" />
<?php
    }
}
