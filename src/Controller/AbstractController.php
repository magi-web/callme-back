<?php
/**
 * Created by PhpStorm.
 * User: ptiperuv
 * Date: 14/10/2017
 * Time: 12:08
 */

class CallMeBack_Controller_AbstractController {
    public $wp_query = false;
    protected $view = null;
    protected $vars = array();
    private $themedir = false;
    private $template_file = '';                              // object to hold variables for the template

    /**
     * CallMeBack_Controller_AbstractController constructor.
     */
    public function __construct() {
        $this->wp_query = $GLOBALS['wp_query'];

        $this->json = (object) array();
        $this->vars = (object) array();


        $this->setTheme();
    }

    /**
     * Set the theme directory manually or auto
     *
     * @param null $template . Name of theme directory.  no leading or trailing slashes
     *
     * @return bool
     */
    public function setTheme( $template = null ) {
        if ( ! $template ) {
            $template = basename(get_stylesheet_directory());
        }

        $this->themedir = get_theme_root() . '/' . $template;

        return true;
    }

    /**
     * Set or get the template file
     *
     * @param null $view
     *
     * @return null
     */
    public function view( $view = null ) {
        if ( $view ) {
            return $this->view = $view;
        } else {
            return $this->view;
        }
    }

    /**
     * @param $template
     *
     * @return string
     */
    private function getAbsoluteTemplateFile($template) {
        $templateFile = DIRECTORY_SEPARATOR . $template . '.php';
        if(!is_admin() && file_exists($this->themedir . $templateFile)) {
            $templateFile = $this->themedir . $templateFile;
        } else {
            $templateFile = CallMeBack::getTemplateDir() . $templateFile;
        }
        return $templateFile;
    }

    /**
     * @param null $html
     * @param array $vars
     *
     * @return bool|string
     * @throws Exception
     */
    public function render( $html = null, $vars = array() ) {
        if ( is_null( $this->view ) && is_null( $html ) && ! is_admin() ) {
            if ( $this->wp_query->is_home ) {
                $this->template_file = get_home_template();
            } else if ( $this->wp_query->is_page ) {
                $this->template_file = get_page_template();
            } else if ( $this->wp_query->is_single ) {
                $this->template_file = get_single_template();
            } else {
                return false;
            }
        } else if ( $html ) {
            $this->template_file = $this->getAbsoluteTemplateFile($html);
        } else if ( $this->view() ) {
            $this->template_file = $this->getAbsoluteTemplateFile($this->view);
        }

        if ( ! $this->template_file || ! file_exists( $this->template_file ) ) {
            throw new Exception("The template " . $this->template_file . " does not exists");
        }

        // make object variables available to the template
        if ( ! count( $vars ) ) {
            $vars = $this->vars;
        }
        $vars = (array) $vars;

        // do the magic
        extract( $vars, EXTR_SKIP );

        ob_start();
            include $this->template_file;
            $view = ob_get_contents();
        ob_end_clean();

        return $view;
    }
}
