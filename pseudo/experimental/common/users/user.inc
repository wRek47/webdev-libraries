<?php

require "/path/to/userbase.inc";

class User {

    public $handle;

    public $passcode;
    public $role;

}

class Passcode {

    public $original;
    public $input;

    public $saved;
    public $encrypted;

    public function __construct() {
    
        global $profile;

        if (isset($_POST['password'])):
        
            // registration, login, or update?
        
        endif;
    
    }

}

?>