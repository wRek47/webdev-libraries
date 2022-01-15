<?php

require "/path/to/ballot_box.inc";
require "/path/to/voter.inc";
require "/path/to/entrants.inc";

class Ballot {

    public $serial;
    public $stamp;

    public $voter;

    public $entrants = array();

    public function __construct($voter) {
    
        $this->voter = $voter;
        $this->stamp = new Stamp;
    
    }

}

?>