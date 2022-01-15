<?php

class FilePaths {

    public $root;
    public $base = "/";
    public $core;

    public $active = "";

    public $states = ["Not Found", "Found Exact", "Found in Root", "Found in Core"];
    public $state;
    public $status;

    public function __construct() {
    
        $this->root = getcwd() . DIRECTORY_SEPARATOR;
        $this->core = $this->root . $this->base;
    
    }

    public function open($path) {
    
        $this->state = $this->analyze($path);
        $this->status = $this->states[$this->state];

        $this->reset($path);
    
    }

    public function reset($path) {
    
        $result = $this->active;

        if ($this->state > 0):
        
            if ($this->state == 1): $result = $path;
            elseif ($this->state == 2): $result = $this->root . $path;
            elseif ($this->state == 3): $result = $this->core . $path;
            endif;
        
        endif;

        $this->active = $result;
    
    }

    public function analyze($path) {
    
        $result = 0;

        if (is_dir($path)):
        
            $result = 1;
        
        elseif (is_dir($this->root . $path)):
        
            $result = 2;
        
        elseif (is_dir($this->core . $path)):
        
            $result = 3;
        
        endif;

        return $result;
    
    }

}

?>