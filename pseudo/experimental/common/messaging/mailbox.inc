<?php

# $pdo = new PDO;

$profile = (object) array();

    $profile->handle = "anonymous#" . rand(0, 5000);
    $profile->name = "Guest";
    $profile->ip = $_SERVER['REMOTE_ADDR'];

class MailBox {

    public $title;

    public $unread;
    public $messages;

    public function __construct($profile = null) {
    
        $this->count_unread();
    
    }

    private function count_unread() {
    
        global $profile;

        $total = 0;

        foreach ($this->messages as $message):
        
            foreach ($message->log as $stamp):
            
                if (isset($stamp->target) AND isset($stamp->profile)):
                
                    if ($stamp->target == "read" AND $stamp->profile == $profile->handle): $total++; endif;
                
                endif;
            
            endforeach; unset($stamp);
        
        endforeach; unset($message);

        $this->unread = $total;
    
    }

}

?>