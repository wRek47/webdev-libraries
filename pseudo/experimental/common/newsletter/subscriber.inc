<?php

class Subscriber {

    public $name;
    public $ip;

    public function __construct($profile) {
    
        $this->name = $profile->name;
        $this->ip = $profile->ip;
    
    }

}

?>