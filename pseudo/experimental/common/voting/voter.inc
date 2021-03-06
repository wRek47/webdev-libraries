<?php

class Voter {

    public $signature;
    public $votes;

    public $name;
    public $ip;

    public function __construct($profile = null) {
    
        if (!is_null($profile)): $profile = $this->default_voter(); endif;

        $this->name = $profile->name;
        $this->ip = $profile->ip;
    
    }

    public function default_voter() {
    
        $profile = (object) array();

            $profile->name = "Guest";
            $profile->ip = $_SERVER['REMOTE_ADDR'];

        return $profile;
    
    }

}

?>