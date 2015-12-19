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
        foreach ($themes_objects as $theme) {
            array_push(
                $themes,
                array(
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


    public function wp_oracle_get_core_new_versions() {
        global $wp_version, $wpdb, $wp_local_package;;

        $php_version = phpversion();

        $translations = wp_get_installed_translations( 'core' );

        $locale = apply_filters( 'core_version_check_locale', get_locale() );

        if ( method_exists( $wpdb, 'db_version' ) )
            $mysql_version = preg_replace('/[^0-9.].*/', '', $wpdb->db_version());
        else
            $mysql_version = 'N/A';

        $user_count = count_users();
        $user_count = $user_count['total_users'];
        $multisite_enabled = 0;
        $num_blogs = 1;
        $wp_install = home_url( '/' );

        $query = array(
            'version'            => $wp_version,
            'php'                => $php_version,
            'locale'             => $locale,
            'mysql'              => $mysql_version,
            'local_package'      => isset( $wp_local_package ) ? $wp_local_package : '',
            'blogs'              => $num_blogs,
            'users'              => $user_count,
            'multisite_enabled'  => $multisite_enabled,
            'initial_db_version' => get_site_option( 'initial_db_version' ),
        );

        $post_body = array(
            'translations' => wp_json_encode( $translations ),
        );

        $options = array(
            'timeout' => 3,
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
            'headers' => array(
                'wp_install' => $wp_install,
                'wp_blog' => home_url( '/' )
            ),
            'body' => $post_body,
        );

        $url = 'http://api.wordpress.org/core/version-check/1.7/?' . http_build_query( $query, null, '&' );
        $raw_response = wp_remote_post( $url, $options );

        return $response = json_decode( wp_remote_retrieve_body( $raw_response ), true );
    }


    public function wp_oracle_get_plugins_new_versions() {
        global $wp_version;

        $plugins = get_plugins();
        $translations = wp_get_installed_translations( 'plugins' );

        $active  = get_option( 'active_plugins', array() );
        $to_send = compact( 'plugins', 'active' );

        $locales = apply_filters( 'plugins_update_check_locales', array( get_locale() ) );

        // Three seconds, plus one extra second for every 10 plugins
        $timeout = 3 + (int) ( count( $plugins ) / 10 );

        $options = array(
            'timeout' => $timeout,
            'body' => array(
                'plugins'      => wp_json_encode( $to_send ),
                'translations' => wp_json_encode( $translations ),
                'locale'       => wp_json_encode( $locales ),
                'all'          => wp_json_encode( true ),
            ),
            'user-agent' => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
        );

        $url = 'http://api.wordpress.org/plugins/update-check/1.1/';
        $raw_response = wp_remote_post( $url, $options );

        return $response = json_decode( wp_remote_retrieve_body( $raw_response ), true );
    }


    public function wp_oracle_get_themes_new_versions() {
        global $wp_version;

        $installed_themes = wp_get_themes();
        $translations = wp_get_installed_translations( 'themes' );

        $themes = $checked = $request = array();

        // Put slug of current theme into request.
        $request['active'] = get_option( 'stylesheet' );

        foreach ( $installed_themes as $theme ) {
            $themes[ $theme->get_stylesheet() ] = array(
                'Name'       => $theme->get('Name'),
                'Title'      => $theme->get('Name'),
                'Version'    => $theme->get('Version'),
                'Author'     => $theme->get('Author'),
                'Author URI' => $theme->get('AuthorURI'),
                'Template'   => $theme->get_template(),
                'Stylesheet' => $theme->get_stylesheet(),
            );
        }

        $request['themes'] = $themes;

        $locales = apply_filters( 'themes_update_check_locales', array( get_locale() ) );

        $timeout = 3 + (int) ( count( $themes ) / 10 );

        $options = array(
            'timeout' => $timeout,
            'body' => array(
                'themes'       => wp_json_encode( $request ),
                'translations' => wp_json_encode( $translations ),
                'locale'       => wp_json_encode( $locales ),
            ),
            'user-agent'    => 'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' )
        );

        $url = $http_url = 'http://api.wordpress.org/themes/update-check/1.1/';
        $raw_response = wp_remote_post( $url, $options );

        return $response = json_decode( wp_remote_retrieve_body( $raw_response ), true );

    }
}