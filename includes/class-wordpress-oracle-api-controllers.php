<?php


class Wordpress_Oracle_Api_Controllers {

    public function wp_oracle_get_status() {

        return array(
            'blog' => array(
                'status' => 'ok'
            )
        );

    }


    public function wp_oracle_get_wp_version() {
        global $wp_version;

        return array(
            'blog' => array(
                'version' => $wp_version
            )
        );

    }


    public function wp_oracle_get_plugins() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }

        $plugins = get_plugins();

        foreach ( $plugins as $path => $plugin ) {
            $plugins[$path]['is_active'] = is_plugin_active( $path );
        }

        return array(
            'blog' => array (
                'plugins' => $plugins
            )
        );
    }


    public function wp_oracle_get_themes() {
        $themes_objects = wp_get_themes();
        $themes = array();
        foreach ($themes_objects as $slug => $theme) {
            array_push(
                $themes,
                array(
                    'slug'      => $slug,
                    'name'      => $theme->get('Name'),
                    'version'   => $theme->get('Version')
                )
            );
        }

        return array(
            'blog' => array (
                'themes' => $themes
            )
        );
    }


    public function wp_oracle_get_core_updates() {
        if ( !function_exists( 'get_core_updates' ) ) {
            require_once ABSPATH . 'wp-admin/includes/update.php';
        }

        // force refresh
        wp_version_check();

        $updates = get_core_updates();

        if (empty($updates)) {
            return array(
                'blog' => array (
                    'core' => 'no_updates'
                )
            );
        } else {
            return $updates;
        }
    }


    public function wp_oracle_get_plugin_updates() {
        if ( !function_exists( 'get_plugin_updates' ) ) {
            require_once ABSPATH . 'wp-admin/includes/update.php';
        }

        // force refresh
        wp_update_plugins();

        $updates = get_plugin_updates();

        if (empty($updates)) {
            return array(
                'blog' => array (
                    'plugins' => 'no_updates'
                )
            );
        } else {
            return $updates;
        }
    }


    public function wp_oracle_get_theme_updates() {
        if ( !function_exists( 'get_theme_updates' ) ) {
            require_once ABSPATH . 'wp-admin/includes/update.php';
        }

        // force refresh
        wp_update_themes();

        $updates = get_theme_updates();

        if (empty($updates)) {
            return array(
                'blog' => array (
                    'themes' => 'no_updates'
                )
            );
        } else {
            return $updates;
        }
    }


    public function wp_oracle_get_translation_updates() {
        if ( !function_exists( 'wp_get_translation_updates' ) ) {
            require_once ABSPATH . 'wp-includes/update.php';
        }

        $updates = wp_get_translation_updates();

        if (empty($updates)) {
            return array(
                'blog' => array (
                    'translations' => 'no_updates'
                )
            );
        } else {
            return $updates;
        }
    }


    public function wp_oracle_get_core_upgrade() {
        include_once ABSPATH . 'wp-admin/includes/admin.php';
        include_once ABSPATH . 'wp-admin/includes/upgrade.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-admin/includes/update.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-oracle-core-upgrader-skin.php';

        // force refresh
        wp_version_check();

        $updates = get_core_updates();

        $update = reset( $updates );
        $skin = new Wordpress_Oracle_Core_Upgrader_Skin();
        $upgrader = new Core_Upgrader( $skin );


        $result = $upgrader->upgrade($update, array(
            'allow_relaxed_file_ownership' => true
        ) );

        if ( is_wp_error( $result ) )
            return $result;

        global $wp_current_db_version, $wp_db_version;

        // we have to include version.php so $wp_db_version
        // will take the version of the updated version of wordpress
        require ABSPATH . WPINC . '/version.php';
        wp_upgrade();

        return array(
            'blog' => array (
                'core_version' => $result
            )
        );
    }


    public function wp_oracle_get_plugin_upgrade() {
        include_once ABSPATH . 'wp-admin/includes/admin.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-oracle-plugin-upgrader-skin.php';
        if ( isset($_REQUEST['plugin']) ) {

            $skin = new Wordpress_Oracle_Plugin_Upgrader_Skin();
            $upgrader = new Plugin_Upgrader( $skin );

            // Do the upgrade
            ob_start();
            $result = $upgrader->upgrade($_REQUEST['plugin']);
            $data = ob_get_contents();
            ob_clean();

            if ( ! empty( $skin->error ) )
                return new WP_Error( 'plugin_upgrader_skin', $upgrader->strings[$skin->error] );
            else if ( is_wp_error( $result ) )
                return $result;
            else if ( ( ! $result && ! is_null( $result ) ) || $data )
                return new WP_Error('Unknown error updating plugin.');

            return array(
                'blog' => array (
                    'plugin_updated' => $_REQUEST['plugin']
                )
            );
        } else {
            return new WP_Error('no_plugin_file', "Please specify plugin name.");
        }
    }


    public function wp_oracle_get_theme_upgrade() {
        include_once ABSPATH . 'wp-admin/includes/admin.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-oracle-theme-upgrader-skin.php';
        if ( isset($_REQUEST['theme']) ) {

            $skin = new Wordpress_Oracle_Theme_Upgrader_Skin();
            $upgrader = new Theme_Upgrader( $skin );

            // Do the upgrade
            ob_start();
            $result = $upgrader->upgrade($_REQUEST['theme']);
            $data = ob_get_contents();
            ob_clean();

            if ( ! empty( $skin->error ) )
                return new WP_Error( 'theme_upgrader_skin', $upgrader->strings[$skin->error] );
            else if ( is_wp_error( $result ) )
                return $result;
            else if ( ( ! $result && ! is_null( $result ) ) || $data )
                return new WP_Error('unknown_error', 'Unknown error updating theme.');

            return array(
                'blog' => array (
                    'theme_updated' => $_REQUEST['theme']
                )
            );
        } else {
            return new WP_Error('no_theme_file', 'Please specify theme name.');
        }
    }


    public function wp_oracle_get_translation_upgrade() {
        include_once ABSPATH . 'wp-admin/includes/admin.php';
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wordpress-oracle-language-upgrader-skin.php';

        $skin = new Wordpress_Oracle_Language_Pack_Upgrader_Skin();
        $upgrader = new Language_Pack_Upgrader( $skin );

        // Do the upgrade
        ob_start();
        $result = $upgrader->bulk_upgrade();
        $data = ob_get_contents();
        ob_clean();

        if ( is_wp_error( $result ) )
            return $result;

        return array( 'status' => 'success' );
    }

}