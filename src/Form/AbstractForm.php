<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 14/10/2017
 * Time: 10:26
 */

/**
 * Class CallMeBack_Form_AbstractForm
 */
abstract class CallMeBack_Form_AbstractForm {
    /**
     * @var array
     */
    protected $formFields = [];
    /**
     * @var object
     */
    protected $entity;
    protected $data = null;
    protected $errors = [];

    /**
     * Initialise le formulaire et construit la collection de champs
     *
     * @return mixed
     */
    abstract public function buildForm();

    /**
     * @return mixed
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Retourne le champs du formulaire
     *
     * @param string $fieldName
     *
     * @return array
     * @throws CallMeBack_Form_Exception
     */
    public function get( $fieldName = '' ) {
        if ( array_key_exists( $fieldName, $this->formFields ) ) {
            return $this->formFields[ $fieldName ];
        }
        throw new CallMeBack_Form_Exception( "Le champs $fieldName est absent du formulaire" );
    }

    /**
     * Hydrate les informations de l'entité dans les champs du formulaire
     *
     * @param mixed $entity
     *
     * @throws CallMeBack_Form_Exception
     */
    public function hydrateForm( $entity = null ) {
        $this->entity = is_object( $entity ) ? clone( $entity ) : $entity;
        $this->data   = is_object( $entity ) ? $entity : [];

        foreach ( $this->formFields as $fieldName => $fieldOptions ) {
            $value = "";

            if ( ! is_null( $entity ) ) {
                $fieldNameCml = CallMeBack_Utils_StringUtils::toCamelCase( $fieldName );
                $getter       = 'get' . ucfirst( $fieldNameCml );
                if ( method_exists( $entity, $getter ) ) {
                    $value                                   = $entity->$getter();
                    $this->formFields[ $fieldName ]['value'] = $value;
                } else {
                    throw new CallMeBack_Form_Exception( "La méthode " . $getter . "() pour l'objet " . get_class( $entity ) . " n'existe pas." );
                }
            }

            $this->formFields[ $fieldName ]['value'] = $value;
        }
    }

    /**
     * Retourne vrai si le formulaire a été soumis (champs dans la méthode post)
     *
     * @return bool
     */
    public function isSubmitted() {
        return isset( $_POST[ $this->getBlockPrefix() ] );
    }

    /**
     * Retourne le préfix à utiliser dans le rendu des champs du formulaire
     *
     * @return string
     */
    abstract public function getBlockPrefix();

    /**
     * Méthode qui gère la soumission du formulaire afin d'affecter les valeurs au formulaire interne et à l'entité liée
     *
     * @throws CallMeBack_Form_Exception
     */
    public function handleRequest() {
        if ( isset( $_POST[ $this->getBlockPrefix() ] ) ) {
            foreach ( $this->formFields as $fieldName => $fieldOptions ) {
                $value                                   = $_POST[ $this->getBlockPrefix() ][ $fieldName ];
                $this->formFields[ $fieldName ]['value'] = sanitize_text_field($value);
            }

            if ( is_array( $this->data ) ) {
                foreach ( $this->formFields as $fieldName => $fieldOptions ) {
                    $this->data[ $fieldName ] = $this->formFields[ $fieldName ]['value'];
                }
            } else {
                foreach ( $this->formFields as $fieldName => $fieldOptions ) {
                    $fieldNameCml = CallMeBack_Utils_StringUtils::toCamelCase( $fieldName );
                    $setter       = 'set' . ucfirst( $fieldNameCml );
                    if ( method_exists( $this->data, $setter ) ) {
                        $this->data->$setter( $this->formFields[ $fieldName ]['value'] );
                    } else {
                        throw new CallMeBack_Form_Exception( "La méthode " . $setter . "() pour l'objet " . get_class( $this->data ) . " n'existe pas." );
                    }
                }
            }
        }
    }

    /**
     * Retourne vrai si le formulaire est valide (teste les champs obligatoires)
     *
     * @return bool
     */
    public function isValid() {
        $this->errors = [];
        $errorsCount = 0;
        foreach ( $this->formFields as $fieldName => $fieldOptions ) {
            $this->errors[ $fieldName ] = [];
            if ( array_key_exists( 'required', $fieldOptions ) && $fieldOptions['required'] && empty( $this->formFields[ $fieldName ]['value'] ) ) {
                $this->errors[ $fieldName ][] = __( $fieldOptions['invalid_message'], CallMeBack::TEXT_DOMAIN );
                $errorsCount++;
            }

            if (array_key_exists('format', $fieldOptions) && !empty($this->formFields[ $fieldName ]['value'])) {
                $validator = new CallMeBack_Validator_Format($fieldOptions['format']);
                $isValid = $validator->validate($this->formFields[ $fieldName ]['value']);
                if(!$isValid) {
                    $this->errors[ $fieldName ][] = __( $fieldOptions['invalid_format_message'], CallMeBack::TEXT_DOMAIN );
                    $errorsCount++;
                }
            }
        }

        return ($errorsCount === 0);
    }

    /**
     * Retourne l'élément label du champs associé
     *
     * @param string $fieldName
     */
    public function renderLabel( $fieldName ) {
        $fieldEntry = $this->formFields[ $fieldName ];

        $id    = $this->getFieldId( $fieldName );
        $label = __( $fieldEntry['label'], CallMeBack::TEXT_DOMAIN );

        echo "<label for='$id'><span class=\"screen-reader-text\">$label</span></label>";
    }

    /**
     * Retourne la liste des erreurs liées au champs associé
     * @param $fieldName
     */
    public function renderErrors($fieldName) {
        $errorsTpl = "";
        if(!empty($this->errors[$fieldName])) {
            $errorsTpl .= "<ul class='errors'>";
            foreach ($this->errors[$fieldName] as $error) {
                $errorsTpl .= "<li class='text-error'>$error</li>";
            }
            $errorsTpl .= "</ul>";
        }

        echo $errorsTpl;
    }

    /**
     * Retourne l'id du champs formulaire
     *
     * @param string $field
     *
     * @return string
     */
    public function getFieldId( $field ) {
        return $this->getBlockPrefix() . "_" . $field;
    }

    /**
     * Retourne l'élément input du champs associé
     *
     * @param string $fieldName
     */
    public function renderWidget( $fieldName ) {
        $fieldEntry = $this->formFields[ $fieldName ];

        $attrs = array_merge(
            $fieldEntry['attr'],
            [
                'id'          => $this->getFieldId( $fieldName ),
                'name'        => $this->getFieldName( $fieldName ),
                'value'       => $fieldEntry['value'],
                'placeholder' => __( $fieldEntry['placeholder'], CallMeBack::TEXT_DOMAIN ),
                'title'       => __( $fieldEntry['placeholder'], CallMeBack::TEXT_DOMAIN ),
                'type'        => $fieldEntry['type'],
            ]
        );

        if ( ! empty( $fieldEntry['required'] ) ) {
            $attrs['required'] = 'required';
        }

        $attrs = join( " ", array_map( function ( $key, $value ) {
            return $key . '="' . $value . '"';
        }, array_keys( $attrs ), $attrs ) );

        $widget = "<input $attrs />";

        echo $widget;
    }

    /**
     * Retourne le nom du champs formaté pour l'attribut name des inputs du formulaire
     *
     * @param string $field
     *
     * @return string
     */
    public function getFieldName( $field ) {
        return $this->getBlockPrefix() . "[$field]";
    }
}
