<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 19/10/2017
 * Time: 20:21
 */

/**
 * Class CallMeBack_Controller_RestController
 */
class CallMeBack_Controller_RestController extends WP_REST_Controller {
    /**
     * Register the routes for the objects of the controller.
     */
    public function registerRoutes() {
        $version = '1';
        $namespace = 'callme-back/v' . $version;
        $base = 'call';
        register_rest_route( $namespace, '/' . $base . '/list', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'getItems' ),
                'permission_callback' => array( $this, 'getPermissionsCheck' ),
                'args'                => array(
                    'state'    => array(
                        'default' => CallMeBack_Model_PhoneRequest::STATE_NOT_DONE,
                    ),
                    'per_page' => array(
                        'default' => 10
                    ),
                    'page'     => array(
                        'default' => 1
                    )
                ),
            )
        ) );
        register_rest_route( $namespace, '/' . $base . '/(?P<id_call>[\d]+)', array(
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'updateStateItem' ),
                'permission_callback' => array( $this, 'getPermissionsCheck' ),
                'args'                => array(
                    'state'    => array(
                        'default' => CallMeBack_Model_PhoneRequest::STATE_DONE,
                    )
                )
            ),
            array(
                'methods'  => WP_REST_Server::DELETABLE,
                'callback' => array( $this, 'deleteItem' ),
                'permission_callback' => array( $this, 'getPermissionsCheck' ),
            ),
        ) );
        register_rest_route( $namespace, '/' . $base . '/schema', array(
            'methods'         => WP_REST_Server::READABLE,
            'callback'        => array( $this, 'getPublicItemSchema' ),
        ) );
    }

    /**
     * Get a collection of items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function getItems( $request ) {
        $params = $request->get_params();
        $phoneRequestRepository = new CallMeBack_Repository_PhoneRequestRepository();
        list($count, $items) = $phoneRequestRepository->listItems($params['per_page'],$params['page'] );

        $data = array('total' => $count, 'per_page' => $params['per_page'], 'page' => $params['page'], 'last_page' => (round($count/$params['per_page']) +1), 'results' => []);
        foreach( $items as $item ) {
            $itemdata = $this->prepareItemForResponse( $item, $request );
            $data['results'][] = $this->prepareResponseForCollection( $itemdata );
        }

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Check if a given request has access to the rest controller
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function getPermissionsCheck( $request ) {
        return true;// <--use to make readable by all
    }

    /**
     * Mets à jour l'état d'un objet
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function updateStateItem( $request ) {
        $params = $request->get_params();
        $phoneRequestRepository = new CallMeBack_Repository_PhoneRequestRepository();
        $phoneRequest = $phoneRequestRepository->find($params['id_call']);

        $data = ['success' => false];
        if($phoneRequest) {
            $phoneRequest->setDone(intval($params['done']));
            $phoneRequestRepository->save($phoneRequest);
            $data['success'] = true;
        } else {
            $data['error'] = sprintf(__('Entity for the id %d was not found.', CallMeBack::TEXT_DOMAIN), $params['id_call']);
        }

        return new WP_REST_Response( $data, 200 );
    }

    /**
     * Supprime un objet
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function deleteItem( $request ) {
        $params = $request->get_params();

        $phoneRequestRepository = new CallMeBack_Repository_PhoneRequestRepository();
        $phoneRequestRepository->delete($params['id_call']);

        $data = ['success' => true];

        return new WP_REST_Response( $data, 200 );
    }


    /**
     * Prepare the item for the REST response
     *
     * @param CallMeBack_Model_PhoneRequest $item WordPress representation of the item.
     * @param WP_REST_Request $request Request object.
     * @return mixed
     */
    public function prepareItemForResponse( $item, $request ) {
        // Wrap the data in a response object.
        return rest_ensure_response( $item->toArray() );
    }

    /**
     * Prepares a response for insertion into a collection.
     *
     * @since 4.7.0
     * @access public
     *
     * @param WP_REST_Response $response Response object.
     * @return array|mixed Response data, ready for insertion into collection data.
     */
    public function prepareResponseForCollection($response) {
        return $this->prepare_response_for_collection($response);
    }

    /**
     * Retrieves the item's schema for display / public consumption purposes.
     *
     * @since 4.7.0
     * @access public
     *
     * @return array Public item schema data.
     */
    public function getPublicItemSchema() {

        $schema = $this->getItemSchema();

        foreach ( $schema['properties'] as &$property ) {
            unset( $property['arg_options'] );
        }

        return $schema;
    }

    /**
     * Retrieves the comment's schema, conforming to JSON Schema.
     *
     * @since 4.7.0
     * @access public
     *
     * @return array
     */
    public function getItemSchema() {
        $schema = array(
            '$schema'              => 'http://json-schema.org/schema#',
            'title'                => 'comment',
            'type'                 => 'object',
            'properties'           => array(
                'id_call'               => array(
                    'description'  => __( 'Unique identifier for the object.' ),
                    'type'         => 'integer',
                    'context'      => array( 'view', 'edit', 'embed' ),
                    'readonly'     => true,
                ),
                'name'           => array(
                    'description'  => __( 'Display name for the object.' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit', 'embed' ),
                    'arg_options'  => array(
                        'sanitize_callback' => 'sanitize_text_field',
                    )
                ),
                'phone_number'     => array(
                    'description'  => __( 'Phone number for the object.' ),
                    'type'         => 'string',
                    'format'       => 'phone',
                    'context'      => array( 'edit' )
                ),
                'done'           => array(
                    'description'  => __( 'State of the object.' ),
                    'type'         => 'string',
                    'context'      => array( 'view', 'edit' ),
                    'arg_options'  => array(
                        'sanitize_callback' => 'sanitize_key',
                    ),
                ),
                'date'             => array(
                    'description'  => __( "The date the object was published, in the site's timezone." ),
                    'type'         => 'string',
                    'format'       => 'date-time',
                    'context'      => array( 'view', 'edit', 'embed' ),
                )
            ),
        );

        return $this->add_additional_fields_schema( $schema );
    }
}
