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
    private $template_file = '';                              // object to hold varaibles for the template

    /**
     * CallMeBack_Controller_AbstractController constructor.
     */
    public function __construct() {
        global $blog_id, $wp, $wpdb, $wp_query;

        $this->blog_id  = $blog_id;
        $this->wp       = &$wp;
        $this->wpdb     = &$wpdb;
        $this->wp_query = &$wp_query;

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
            //$template = get_template();
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
            $baseDir = is_admin() ? CallMeBack::getPluginDir() : $this->themedir;
            $this->template_file = $baseDir . DIRECTORY_SEPARATOR . $html . '.php';
        } else if ( $this->view() ) {
            $baseDir = is_admin() ? CallMeBack::getPluginDir() : $this->themedir;
            $this->template_file = $baseDir . '/' . $this->view . '.php';
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