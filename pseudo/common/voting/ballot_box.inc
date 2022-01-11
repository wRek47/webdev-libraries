<?php

class BallotBox {

    public $formats = ["one2one", "one2many", "many2one", "many2many"];
    public $entrants = array();

    public $format;
    public $ballots;
    public $voters;

    public function __construct($format = "many2one") {
    
        if (in_array($format, $this->formats)): $this->format = $format; endif;
    
    }

}

?>