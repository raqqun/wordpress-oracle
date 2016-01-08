<?php
/**
 * The Wordpress_Oracle class
 *
 * @package     WordPress_Plugins
 * @subpackage  Wordpress_Oracle
 * @since       1.0.0
 * @author      Alexandros Nikiforidis <anikiforidis@simplon.co>
 */
class Wordpress_Oracle {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Wordpress_Oracle_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;


    public function __construct() {

        $this->plugin_name = 'wordpress-oracle';
        $this->version = '1.0.0';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_api_hooks();
    }

    private function load_dependencies() {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-oracle-loader.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordpress-oracle-admin.php';

        /**
         * The class responsible for defining the api layer.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-oracle-api.php';

        /**
         * The class responsible for defining the api controller.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-oracle-api-controllers.php';


        $this->loader = new Wordpress_Oracle_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {
        $plugin_admin = new Wordpress_Oracle_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

        $this->loader->add_action( 'admin_menu' , $plugin_admin , 'wp_oracle_admin_menu' );
        $this->loader->add_action( 'load-settings_page_wp_oracle', $plugin_admin, 'wp_oracle_maps_menu_handler' );
    }


    /**
     * Register all of the hooks related to the api area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_api_hooks() {
        $plugin_api = new Wordpress_Oracle_Api();
        $this->loader->add_filter( 'query_vars', $plugin_api, 'wp_oracle_add_query_vars' );
        $this->loader->add_action( 'init', $plugin_api, 'wp_oracle_add_api_endpoint' );
        $this->loader->add_action( 'parse_request', $plugin_api, 'wp_oracle_parse_request' );
    }


    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }
    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Wordpress_Oracle_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }
    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}

?>
