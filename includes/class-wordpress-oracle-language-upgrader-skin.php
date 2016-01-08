<?php

class Wordpress_Oracle_Language_Pack_Upgrader_Skin extends Language_Pack_Upgrader_Skin {

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