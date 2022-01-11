<?php

class Voter {

    public $signature;
    public $votes;

    public function __construct($profile = null) {
    
        if (!is_null($profile)): $profile = $this->default_voter(); endif;

        $this->name = $profile->name;
    
    }

    public function default_voter() {
    
        $profile = (object) array();

            $profile->name = "Guest";
            $profile->ip = $_SERVER['REMOTE_ADDR'];

        return $profile;
    
    }

}

?>