<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 14/10/2017
 * Time: 17:14
 */

/**
 * Class CallMeBack_Repository_PhoneRequestRepository
 *
 * Gère l'enregistrement en base de donnée
 */
class CallMeBack_Repository_PhoneRequestRepository {
    /**
     * Retourne l'objet wordpress db pour intéragir simplement avec la db
     *
     * @return wpdb
     */
    private function wpdb() {
        return $GLOBALS['wpdb'];
    }
    /**
     * Retourne la totalité des entrées en base pour les demandes de rappel
     *
     * @return array
     */
    public function findAll() {
        $wpdb = $this->wpdb();

        // SELECT id_call, name, phone_number, done, date
        $table            = $this->getModelTable();
        $rawPhoneRequests = $wpdb->get_results( "SELECT id_call, name, phone_number, done, date FROM $table ORDER BY id_call DESC LIMIT 0,4" );

        $phoneRequests = [];
        foreach ( $rawPhoneRequests as $rawPhoneRequest ) {
            $phoneRequests[] = $this->createFromRawData( $rawPhoneRequest );
        }

        return $phoneRequests;
    }

    /**
     * Retourne le nom de la table du plugin
     *
     * @return string
     */
    private function getModelTable() {
        $wpdb = $this->wpdb();

        return $wpdb->prefix . CallMeBack_Model_Setup::TABLE_NAME;
    }

    /**
     * Retourne une entité phoneRequest hydratee
     *
     * @param array $rawPhoneRequest
     *
     * @return CallMeBack_Model_PhoneRequest
     */
    private function createFromRawData( $rawPhoneRequest ) {
        $phoneRequest = new CallMeBack_Model_PhoneRequest();
        $this->hydrateRawData( $phoneRequest, $rawPhoneRequest );

        return $phoneRequest;
    }

    /**
     * Affecte les données à l'entité
     *
     * @param CallMeBack_Model_PhoneRequest $entity
     * @param array $rawData
     *
     * @throws CallMeBack_Repository_Exception
     */
    private function hydrateRawData( CallMeBack_Model_PhoneRequest $entity, $rawData ) {
        if ( ! empty( $entity ) ) {
            foreach ( $rawData as $column => $value ) {
                $setter = 'set' . ucfirst( CallMeBack_Utils_StringUtils::toCamelCase( $column ) );
                if ( method_exists( $entity, $setter ) ) {
                    $entity->$setter( $value );
                } else {
                    throw new CallMeBack_Repository_Exception( "La méthode $setter n'existe pas pour l'objet de classe " . get_class( $entity ) );
                }
            }
        }
    }

    /**
     * Retrieve items’ data from the database
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return array
     */
    public function getItems( $per_page = 5, $page_number = 1, $orderby, $order ) {
        return $this->search( [], $per_page, $page_number, $orderby, $order, 'ARRAY_A' );
    }

    /**
     * Find items in the table
     *
     * @param array $filters
     * @param int $per_page
     * @param int $page_number
     * @param $orderby
     * @param $order
     *
     * @return array|null|object
     */
    private function search( $filters = [], $per_page = 5, $page_number = 1, $orderby, $order, $hydrate = OBJECT ) {
        $wpdb = $this->wpdb();

        $table = $this->getModelTable();

        $sql = "SELECT * FROM $table";

        if ( ! empty( $filters ) ) {
            $sql .= " WHERE (";
            $conditions = [];
            foreach ($filters as $filter) {
                $conditions[] = esc_sql( join( " ", $filter ) );
            }
            $sql .= join( ") AND (", $conditions );
            $sql .= ") ";
        }

        if ( ! empty( $orderby ) ) {
            $sql .= sprintf( ' ORDER BY %s %s',
                esc_sql( $orderby ),
                ( ! empty( $order ) ? esc_sql( $order ) : 'ASC' )
            );
        }

        $sql .= " LIMIT $per_page";

        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

        $result = $wpdb->get_results( $sql, $hydrate );

        $countNb = $wpdb->get_var( str_replace( 'SELECT *', 'SELECT COUNT(*)', $sql ) );

        return [ intval( $countNb ), $result ];
    }

    /**
     * Retourne la liste des items
     *
     * @return array
     */
    public function listItems( $per_page = 10, $page_number = 1 ) {
        list( $count, $results ) = $this->search( [], $per_page, $page_number, 'date', 'desc' );
        $data = [];
        foreach ( $results as $result ) {
            $data [] = $this->createFromRawData( $result );
        }

        return [ $count, $data ];
    }

    /**
     * Retourne les items liés à un état
     *
     * @param int $state
     *
     * @return array
     */
    public function findItemsByState( $state, $per_page = 10, $page_number = 1 ) {
        list( $count, $results ) = $this->search( [
            [
                'done',
                '=',
                $state
            ]
        ], $per_page, $page_number, 'date', 'desc' );
        $data = [];
        foreach ( $results as $result ) {
            $data [] = $this->createFromRawData( $result );
        }

        return [ $count, $data ];
    }

    /**
     * Delete a record.
     *
     * @param int $id ID
     */
    public function delete( $id ) {
        $wpdb = $this->wpdb();

        $wpdb->delete(
            $wpdb->prefix . CallMeBack_Model_Setup::TABLE_NAME,
            [ 'id_call' => $id ],
            [ '%d' ]
        );
    }

    /**
     * Toggles the "done" column of a record.
     *
     * @param int $id ID
     * @param int $state the new State (optional - will toggle by default)
     */
    public function toggle( $id, $state = null ) {
        $wpdb = $this->wpdb();

        $phoneRequest = $this->find( $id );
        if ( $phoneRequest ) {
            $newState = ! is_null( $state ) ? intval( $state ) : intval( ! $phoneRequest->isDone() );
            $wpdb->update(
                $wpdb->prefix . CallMeBack_Model_Setup::TABLE_NAME,
                [ 'done' => $newState ],
                [ 'id_call' => $id ],
                [ '%d' ],
                [ '%d' ]
            );
        }
    }

    /**
     * Cherche et retourne une entité CallMeBack_Model_PhoneRequest via son id
     *
     * @param int $idCall
     *
     * @return CallMeBack_Model_PhoneRequest|null
     */
    public function find( $idCall ) {
        $wpdb = $this->wpdb();

        $phoneRequest = null;

        $table           = $this->getModelTable();
        $sql             = sprintf( 'SELECT id_call, name, phone_number, done, date FROM %s WHERE id_call = %d LIMIT 0,1', $table, $idCall );
        $rawPhoneRequest = $wpdb->get_row( $sql );
        if ( ! empty( $rawPhoneRequest ) ) {
            $phoneRequest = $this->createFromRawData( $rawPhoneRequest );
        }

        return $phoneRequest;
    }

    /**
     * Toggles the "done" column of a record.
     *
     * @param array $ids ID
     * @param int $state the new State
     */
    public function bulkToggle( $ids, $state ) {
        $wpdb = $this->wpdb();

        $newState = intval( $state );
        $sql      = "UPDATE " . CallMeBack_Model_Setup::getTableName() . ' SET done=%d WHERE id_call IN (%s)';
        $sql      = sprintf( $sql, $newState, join( ',', $ids ) );
        $wpdb->query( esc_sql( $sql ) );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public function recordCount() {
        $wpdb = $this->wpdb();

        $table = $wpdb->prefix . CallMeBack_Model_Setup::TABLE_NAME;

        $sql = "SELECT COUNT(*) FROM $table";

        return $wpdb->get_var( $sql );
    }

    /**
     * Enregistre une entité en base
     *
     * @param CallMeBack_Model_PhoneRequest $entity
     */
    public function save( CallMeBack_Model_PhoneRequest $entity ) {
        $wpdb = $this->wpdb();
        $table = $this->getModelTable();

        if ( empty( $entity->getIdCall() ) ) {
            $sql = $wpdb->prepare(
                "INSERT INTO $table (name, phone_number, done, date) VALUES ( %s, %s, %d, %s )",
                $entity->getName(),
                $entity->getPhoneNumber(),
                $entity->isDone(),
                $entity->getDate()->format( 'Y-m-d H:i:s' ) );
        } else {
            $sql = $wpdb->prepare( "UPDATE $table SET name=%s, phone_number=%s, done=%s, date=%s WHERE id_call = %d",
                $entity->getName(),
                $entity->getPhoneNumber(),
                $entity->isDone(),
                $entity->getDate()->format( 'Y-m-d H:i:s' ),
                $entity->getIdCall() );
        }
        $wpdb->query( $sql );

        if ( empty( $entity->getIdCall() ) ) {
            $idCall = $wpdb->insert_id;
            $entity->setIdCall( $idCall );
        }
    }
}
