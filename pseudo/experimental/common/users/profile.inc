<?php

require "/path/to/portfolio.inc";
require "/path/to/user.inc";

class Profile {

    public $handle;
    public $userdata;

    public $portfolio;
    public $activity;
    public $notifications;

    public function __construct() {
    
        $session = (isset($_SESSION['profile'])) ? true : false;

        if ($session):
        
            $profile = unserialize($_SESSION['profile']);
        
        endif;

        $this->activity = $profile->activity;
        $session = $_SESSION['profile-log'];

        $this->portfolio = new Portfolio($this->handle);
    
    }

}

?>