<?php
/*
 * The Wordpress_Oracle_Api class
 *
 * @package     WordPress_Plugins
 * @subpackage  Wordpress_Oracle/includes
 * @since       1.0.0
 * @author      Alexandros Nikiforidis <anikiforidis@simplon.co>
*/

class Wordpress_Oracle_Api {

    protected $controllers;

    protected $api_token;


    public function __construct() {
        $this->api_token = get_option('wp_oracle_api_token');
        $this->controllers = new Wordpress_Oracle_Api_Controllers();
    }


    public function get_request_token() {
        foreach (getallheaders() as $header => $value) {
            if ($header == 'Api-Token') {
                return $value;
            }
        }
    }


    public function wp_oracle_add_api_endpoint() {
        add_rewrite_rule('^wp-oracle(/(.*))?/?$','index.php?wp_oracle=1&wp_oracle_handler=$matches[2]','top');
        flush_rewrite_rules();
    }


    public function wp_oracle_add_query_vars( $vars ) {
        $vars[] = 'wp_oracle';
        $vars[] = 'wp_oracle_handler';
        return $vars;
    }


    public function wp_oracle_parse_request() {
        global $wp;

        if ( isset( $wp->query_vars['wp_oracle'] ) ) {
            error_log($this->get_request_token());
            if ($this->api_token === $this->get_request_token()) {
                $this->wp_oracle_request_handler( $wp->query_vars['wp_oracle_handler'] );
            } else {
                $this->wp_oracle_send_not_authorized();
            }
        }
    }


    public function wp_oracle_request_handler( $callback ) {
        $response = @call_user_func( array( $this->controllers, 'wp_oracle_get_' . $callback ) );
        if ( NULL !== $response ) {
            $this->wp_oracle_send_response( $response );
        }
        else {
            $this->wp_oracle_send_not_found();
        }
    }


    public function wp_oracle_send_response( $response ) {
        header('content-type: application/json; charset=utf-8');
        echo json_encode( $response )."\n";
        exit;
    }


    public function wp_oracle_send_not_found() {
        status_header(404);
        exit;
    }


    public function wp_oracle_send_not_authorized() {
        status_header(401);
        exit;
    }

}
