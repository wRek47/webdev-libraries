<?php

class SecureInput {

    public $original;
    public $result;

    public function post($key) {
    
        return $this->filter_post($key);
    
    }

    public function encode($method) {
    
        switch ($method):
        
            case "url-safe": $this->result = urlencode($this->result); break;
            case "url-unsafe": $this->result = urldecode($this->result); break;
            
            case "html-safe": $this->result = htmlspecialchars($this->result); break;
            case "html-unsafe": $this->result = htmlspecialchars_decode($this->result); break;

            case "rot13": $this->result = str_rot13($this->result); break;
            case "pwd": $this->result = password_hash($this->result, PASSWORD_DEFAULT); break;

            case "array-safe":
            
                if (!is_array($this->result)): $this->result = array($this->result); endif;
            
            break;

            case "pdo-safe":
            
                if (!is_object($this->result)):
                
                    if (is_array($this->result)): $this->result = (object) $this->result; endif;
                
                endif;
            
            break;
        
        endswitch; unset($method);
    
    }
    
    public function filter_get($key, $filter = FILTER_UNSAFE_RAW, $known = null) {
    
        $this->original = $_GET[$key];
        $this->result = filter_input(INPUT_GET, $key, $filter);

        if (!is_null($known)):
        
            # run additional filters
        
        endif;
    
    }

    public function filter_post($key, $filter = FILTER_UNSAFE_RAW, $known = null) {
    
        $this->original = $_POST[$key];
        $this->result = filter_input(INPUT_POST, $key, $filter);

        if (!is_null($known)):
        
            # run additional filters
        
        endif;
    
    }

}

class Validate {

    public function __construct($filters, $value) {
    
        $this->original = $value;
        
        foreach ($filters as $filter):
        
            if ($filter == "url-safe"): $value = urlencode($value); endif;
            if ($filter == "sql-safe"): $value = PDO::query($value); endif;

            if ($filter == "html-safe"): $value = htmlspecialchars(trim($value)); endif;

            if ($filter == "array-safe"): if (!is_array($value)): $value = array($value); endif; endif;

            if ($filter == "pdo-safe"):
                if (!is_object($value) AND !is_array($value)): $value = (object) array($value);
                elseif (!is_object($value) AND is_array($value)): $value = (object) $value; endif;
            endif;
        
        endforeach; unset($filter);

        $this->result = $value;
    
    }

    public function username() {}

}

?>