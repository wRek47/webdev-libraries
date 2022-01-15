<?php

// without PDO

class Query {

    public $use_pdo = false;

	private $table;
	private $where = null;
	
	public function __construct($table, $action = "select") {
	
		$this->table = $table;
		$this->action = $action;
	
	}

    public function bind($value) { }
	
	public function select($data) { }
	
	public function insert($columns) {
	
        list($keys, $vals) = pairs_to_csv($columns, "pairs");
	
	}
	
	public function update($fields) {
	
		$string = pairs_to_csv($fields, "pairs");
		$action = $this->action;
		
		$string = sprintf("`%s` where `%s`", $string, $this->where);
		$string = sprintf("update `%s` set `%s`", $this->table, $string);

        $this->query = $string;
        return $this->query;
	
	}
	
	public function where($key, $value) {
	
		$this->where = sprintf('%s = "%s"', $key, $value);
	
	}

    public function limit($rank, $plots) { }
    public function order_by($keys) { }

}

function pairs_to_csv($array, $model = "pairs") {

	$keys = array();
	$vals = array();
	$pairs = array();
	
	foreach ($array as $key => $value):
	
		array_push($keys, $key);
		array_push($vals, $value);
		
		array_push($pairs, sprintf('%s = "%s"', $key, $value));
	
	endforeach; unset($key, $value);
	
	/* > */
	
	$result = false;

    $keys = implode(", ", $keys);
    $vals = implode(", ", $vals);
    $pairs = array($keys, $vals);
    $default = implode(", ", $pairs);
	
	switch ($model):
	
		case "keys": $result = $keys; break;
		case "values": $result = $vals; break;
        case "pairs": $result = $pairs; break;
		default: $result = $default; break;
	
	endswitch;
	
	return $default;

}

?>