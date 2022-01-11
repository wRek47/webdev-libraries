<?php

class KnownFilters {

    public function __construct() {
    
        $file = "/path/to/filters.json";
        $json = file_get_contents($file); unset($file);
        $fields = json_decode($json); unset($json);

        $this->fields = $fields; unset($fields);
    
    }

    public function verify_with_regex($value) { }
    public function verify_with_function($name, $params) { }
    public function verify_with_xss($url, $elements, $values) { }
    public function verify_with_known($model) { }
    public function verify_with_api($target, $article) { }

}

?>