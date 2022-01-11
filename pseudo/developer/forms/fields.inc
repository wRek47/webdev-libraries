<?php

require "/path/to/known_filters.inc";

class FormField {

    protected $controls = array("input", "select", "textarea", "button");
    public $control;

    protected $filters;
    public $filter;

    public $attributes = array();
    public $classes = array();

    public $id;
    public $target;

    public $label;
    public $value;
    public $placeholder;

    public function set_attr($attr, $value) { }
    public function unset_attr($attr) { }
    public function unset_first_attr() { }
    public function unset_last_attr() { }

    public function add_class($class) { }
    public function remove_class($class) { }
    public function remove_first_class() { }
    public function remove_last_class() { }

}

?>