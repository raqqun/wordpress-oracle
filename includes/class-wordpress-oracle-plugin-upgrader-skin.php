<?php

class Wordpress_Oracle_Plugin_Upgrader_Skin extends Plugin_Installer_Skin {

    public $feedback;

    public $error;

    function error( $error ) {
        $this->error = $error;
    }

    function feedback( $feedback ) {
        $this->feedback = $feedback;
    }

    function before() { }

    function after() { }

    function header() { }

    function footer() { }
}