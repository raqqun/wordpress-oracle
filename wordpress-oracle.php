<?php
/*
Plugin Name: Wordpress Oracle
Description: Expose an API to easily monitor wordpress installations
Plugin URI:  http://www.raqqun0101.net/
Version:     1.0.0
Author:      Alexandros Nikiforidis
Author URI:  http://www.raqqun0101.net/
License:     GPL2

Copyright 2015 Alexandros Nikiforidis

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( get_option('wp_oracle_configured') !== '1' ) {
    add_action( 'admin_notices', 'wp_oracle_admin_notices' );
}


function wp_oracle_admin_notices() { ?>
<div class='error'>
    <p>
        <?php echo 'Wordpress Oracle Plugin is not configured : '; ?>
        <a href="">Configuration Page</a>
    </p>
</div>
<?php }

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wordpress-oracle-activator.php
 */
function activate_wordpress_oracle() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-oracle-activator.php';
    Wordpress_Oracle_Activator::activate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wordpress-oracle-deactivator.php
 */
function deactivate_wordpress_oracle() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-oracle-deactivator.php';
    Wordpress_Oracle_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wordpress_oracle' );
register_deactivation_hook( __FILE__, 'deactivate_wordpress_oracle' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wordpress-oracle.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {
    $plugin = new Wordpress_Oracle();
    $plugin->run();
}


run_plugin_name();