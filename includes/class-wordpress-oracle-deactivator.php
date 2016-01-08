<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Wordpress_Oracle
 * @subpackage Wordpress_Oracle/includes
 * @author     Alexandros Nikiforidis <anikiforidis@simplon.co>
 */
class Wordpress_Oracle_Deactivator {
    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function deactivate() {
        delete_option('wp_oracle_api_token');
        delete_option('wp_oracle_configured');
        flush_rewrite_rules();
    }
}
