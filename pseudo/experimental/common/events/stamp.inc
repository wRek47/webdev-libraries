<?php

class Stamp {

    public $description;
    public $created;

    public function __construct() {

        $this->created = time();

    }

}

?>