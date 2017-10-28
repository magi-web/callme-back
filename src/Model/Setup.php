<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 07/10/2017
 * Time: 16:25
 */

/**
 * Class CallMeBack_Model_Setup
 */
class CallMeBack_Model_Setup {
    const TABLE_NAME = 'CallMeBack';

    private $tableSQL = '';
    private $table_name = '';

    /**
     * Retourne l'objet wordpress db pour intéragir simplement avec la db
     *
     * @return wpdb
     */
    private static function wpdb() {
        return $GLOBALS['wpdb'];
    }

    /**
     * CallMeBack_Model_Setup constructor.
     */
    public function __construct() {
        $wpdb = $this->wpdb();
        $this->table_name = $wpdb->prefix . static::TABLE_NAME;
    }

    /**
     * Retourne le nom de la table utilisée par le plugin
     *
     * @return string
     */
    public static function getTableName() {
        $wpdb = static::wpdb();
        return $wpdb->prefix . static::TABLE_NAME;
    }

    public function installData() {
        $wpdb = $this->wpdb();

        // Configuration
        $this->tableSQL   = "id_call mediumint(9) NOT NULL AUTO_INCREMENT, name TEXT DEFAULT '', phone_number TEXT DEFAULT '', done tinyint(1), date DATETIME DEFAULT '0000-00-00 00:00:00', UNIQUE KEY id_call (id_call)";
        $this->path       = __FILE__;

        if ( strlen( trim( $this->tableSQL ) ) > 0 ) {
            if ( $wpdb->get_var( "show tables like '" . $this->table_name . "'" ) != $this->table_name ) {
                $sql = "CREATE TABLE IF NOT EXISTS " . $this->table_name . " (" . $this->tableSQL . ") DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;";

                $wpdb->query($sql);
            }
        }
    }

    public function deactivate() {
        //Nothing to do
    }

    public function uninstallRemovedata() {
        $wpdb = $this->wpdb();

        $wpdb->query( "DROP TABLE " . $this->table_name );
    }
}
