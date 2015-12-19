<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Wordpress-Oracle
 * @subpackage Wordpress-Oracle/includes
 * @author     Your Name <email@example.com>
 */
class Wordpress_Oracle_Activator {
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate() {
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-oracle-api.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wordpress-oracle-admin.php';
        Wordpress_Oracle_Api::wp_oracle_add_api_endpoint();
        Wordpress_Oracle_Admin::create_admin_user();
    }
}
