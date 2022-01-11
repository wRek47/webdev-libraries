<?php

require "/path/to/pathing.inc";

class FileSystem {

    public $paths;

    public $raw;

    public $tree;
    public $dir;

    public $folders;
    public $files;

    public function __construct($path = null) {
    
        $this->paths = new FilePaths();

        if (!is_null($path)): $this->paths->open($path); endif;

        $this->cycle();
    
    }

    public function cycle() {
    
        $this->raw = scandir($this->paths->active);

        $this->set_folder_tree();
        $this->set_file_tree();

        $this->set_directory_tree();

        $this->set_system_tree();
    
    }

    private function set_folder_tree() {
    
        $scan = $this->raw;
    
    }

    private function set_file_tree() { }

    private function set_directory_tree() { }
    private function set_system_tree() { }


}

?>