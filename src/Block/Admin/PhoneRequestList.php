<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 15/10/2017
 * Time: 21:22
 */

/**
 * Class CallMeBack_Block_Admin_PhoneRequestList
 */
class CallMeBack_Block_Admin_PhoneRequestList extends CallMeBack_Block_Admin_ListTable {
    /** Class constructor */
    public function __construct() {

        parent::__construct( [
            'singular' => __( 'Phone request', CallMeBack::TEXT_DOMAIN ), //singular name of the listed records
            'plural'   => __( 'Phone requests', CallMeBack::TEXT_DOMAIN ), //plural name of the listed records
            'ajax'     => false //should this table support ajax?
        ] );
    }
    /** Text displayed when no customer data is available */
    public function noItems() {
        _e( 'No items found.', CallMeBack::TEXT_DOMAIN );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function columnName( $item ) {

        // create a nonce
        $delete_nonce = wp_create_nonce( 'callmeback_delete_item' );

        $title = '<strong>' . $item['name'] . '</strong>';

        $actionLabel = __('Delete');

        $actions = [
            'delete' => sprintf( '<a href="?page=%s&action=%s&id_call=%s&_wpnonce=%s">' . $actionLabel . '</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id_call'] ), $delete_nonce )
        ];

        return $title . $this->rowActions( $actions );
    }

    /**
     * Method for "done" column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    public function columnDone( $item ) {

        // create a nonce
        $toggle_nonce = wp_create_nonce( 'callmeback_toggle_item' );

        $title = $item[ 'done' ] ? __('Yes', CallMeBack::TEXT_DOMAIN) : __('No', CallMeBack::TEXT_DOMAIN);

        $actionLabel = $item[ 'done' ] ? __('Toggle to Not done', CallMeBack::TEXT_DOMAIN) : __('Toggle to Done', CallMeBack::TEXT_DOMAIN);

        $actions = [
            'toggle' => sprintf( '<a href="?page=%s&action=%s&id_call=%s&_wpnonce=%s">%s</a>', esc_attr( $_REQUEST['page'] ), 'toggle', absint( $item['id_call'] ), $toggle_nonce, $actionLabel )
        ];

        return $title . $this->rowActions( $actions );
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function columnDefault( $item, $column_name ) {
        switch ( $column_name ) {
            case 'phone_number':
                return $item[ $column_name ];
            case 'date':
                $date = strtotime($item[ $column_name ]);
                return date('d/m/Y H:i:s', $date);
            default:
                return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    protected function columnCb( $item ) {
        return sprintf(
            '<input type="checkbox" name="bulk-items[]" value="%s" />', $item['id_call']
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    public function getColumns() {
        $columns = [
            'cb'           => '<input type="checkbox" />',
            'name'         => __( 'Name', CallMeBack::TEXT_DOMAIN ),
            'phone_number' => __( 'Phone Number', CallMeBack::TEXT_DOMAIN ),
            'date'         => __( 'Date', CallMeBack::TEXT_DOMAIN ),
            'done'         => __( 'Done', CallMeBack::TEXT_DOMAIN )
        ];

        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function getSortableColumns() {
        $sortable_columns = array(
            'name'         => array( 'name', true ),
            'phone_number' => array( 'phone_number', false ),
            'date'         => array( 'date', false ),
            'done'         => array( 'done', false ),
        );

        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function getBulkActions() {
        $actions = [
            'bulk-delete' => __('Delete'),
            'bulk-toggle-yes' => __('Toggle to Done', CallMeBack::TEXT_DOMAIN),
            'bulk-toggle-no' => __('Toggle to Not done', CallMeBack::TEXT_DOMAIN),
        ];

        return $actions;
    }

    /**
     * Screen options
     */
    public static function screenOption() {

        $option = 'per_page';
        $args   = [
            'label'   => 'Items',
            'default' => 5,
            'option'  => 'items_per_page'
        ];

        add_screen_option( $option, $args );
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
    public function prepareItems() {
        list( $columns, $hidden, $sortable ) = $this->getColumnInfo();
        $columns = $this->getColumns();
        $sortable = $this->getSortableColumns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        /** @var CallMeBack_Repository_PhoneRequestRepository $phoneRepository */
        $phoneRepository = new CallMeBack_Repository_PhoneRequestRepository();

        /** Process bulk action */
        $this->processBulkAction();

        $per_page     = $this->getItemsPerPage( 'items_per_page', 5 );
        $current_page = $this->getPagenum();

        list($total_items, $this->items) = $phoneRepository->getItems( $per_page, $current_page, $_REQUEST['orderby'], $_REQUEST['order'] );

        $this->setPaginationArgs( [
            'total_items' => $total_items, //WE have to calculate the total number of items
            'per_page'    => $per_page //WE have to determine how many items to show on a page
        ] );
    }

    /**
     * @param $status
     * @param $option
     * @param $value
     *
     * @return mixed
     */
    public static function setScreenOption( $status, $option, $value ) {
        return $value;
    }

    /**
     * Generates content for a single row of the table
     *
     * @since 3.1.0
     * @access public
     *
     * @param object $item The current item
     */
    public function singleRow( $item ) {
        $classes = $item['done'] ? 'is-done' : 'is-not-done';
        echo "<tr class='$classes'>";
        $this->singleRowColumns( $item );
        echo '</tr>';
    }

    /**
     * Checks nonce in request for the given action
     *
     * @param string $action
     *
     * @return bool
     * @throws Exception
     */
    private function checkNonceFromRequest($action = '') {
        // In our file that handles the request, verify the nonce.
        $nonce = esc_attr( $_REQUEST['_wpnonce'] );

        if ( ! wp_verify_nonce( $nonce, $action ) ) {
            throw new Exception("Nonce could not be verified");
        }

        return true;
    }

    private function redirectAfterActionDone() {
        wp_redirect( esc_url( add_query_arg() ) );
        exit;
    }

    public function processBulkAction() {
        $phoneRepository = new CallMeBack_Repository_PhoneRequestRepository();

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->currentAction() ) {
            if($this->checkNonceFromRequest('callmeback_delete_item')) {
                $phoneRepository->delete( absint( $_GET['id_call'] ) );

                $this->redirectAfterActionDone();
            }
        }

        //Detect when a bulk action is being triggered...
        if ( 'toggle' === $this->currentAction() ) {
            if($this->checkNonceFromRequest('callmeback_toggle_item')) {
                $phoneRepository->toggle( absint( $_GET['id_call'] ) );

                $this->redirectAfterActionDone();
            }
        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] === 'bulk-delete' )
             || ( isset( $_POST['action2'] ) && $_POST['action2'] === 'bulk-delete' )
        ) {

            $delete_ids = esc_sql( $_POST['bulk-items'] );

            // loop over the array of record IDs and delete them
            foreach ( $delete_ids as $id ) {
                $phoneRepository->delete( $id );

            }

            $this->redirectAfterActionDone();
        }

        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && strpos($_POST['action'],'bulk-toggle') !== false )
             || ( isset( $_POST['action2'] ) && strpos($_POST['action2'],'bulk-toggle') !== false )
        ) {
            $newState = $this->getBulkToggleAction();
            $postItems = $_POST['bulk-items'];
            if(!is_null($newState) && is_array($postItems)) {
                $item_ids = [];

                foreach ($postItems as $post_item) {
                    $item_ids[]= intval($post_item);
                }

                $phoneRepository->bulkToggle($item_ids, $newState);

                $this->redirectAfterActionDone();
            }
        }
    }

    /**
     * Extracts the bulk action target among 'yes' or 'no'
     *
     * @return bool|null - true if bulk-toggle-yes, false if bulk-toggle-no
     * or null if the bulk toggle action was not triggered
     */
    private function getBulkToggleAction() {
        if ( ( isset( $_POST['action'] ) && strpos($_POST['action'],'bulk-toggle') !== false )
             || ( isset( $_POST['action2'] ) && strpos($_POST['action2'],'bulk-toggle') !== false )
        ) {
            $action = isset( $_POST['action'] ) ? $_POST['action'] : $_POST['action2'];
            return strpos($action, 'yes') !== false;
        }

        return null;
    }
}
