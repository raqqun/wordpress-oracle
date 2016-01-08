<?php

class Wordpress_Oracle_Theme_Upgrader_Skin extends Theme_Installer_Skin {

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