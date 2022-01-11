<?php

class MailBox {

    public $title;

    public $unread;
    public $messages;

    public function __construct($profile = null) {
    
        if (!is_null($profile)): $profile = $this->default_user(); endif;
        
        $this->name = $profile->name;
        $this->ip = $profile->ip;
    
    }

    public function default_user() {
    
        $profile = (object) array();

            $profile->name = "Guest";
            $profile->ip = $_SERVER['REMOTE_ADDR'];

        return $profile;
    
    }

}

?>