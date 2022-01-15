<?php

require "/path/to/ledger.inc";
require "/path/to/stamp.inc";

class Event {

    public $label;
    public $description;

    public $stamps;

    public function __construct() { }

    public function create_stamp() {
    
        $stamp = new Stamp;
        return $stamp;
    
    }

    public function save_stamp($stamp) {
        
        array_push($this->stamps, $stamp);
        unset($stamp); return true;
    
    }

}

?>