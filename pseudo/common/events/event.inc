<?php

class Event {

    public $label;
    public $description;

    public $stamps;

    public function __construct() { }

    public function create_stamp() {
    
        $stamp = (object) array();
            $stamp->created = time();
        
        return $stamp;
    
    }

    public function save_stamp($stamp) {
        
        array_push($this->stamps, $stamp);
        unset($stamp); return true;
    
    }

}

?>