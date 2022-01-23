<?php

/*

Project: SQL Table Helper
Description: Writing and executing prepared SQL statements dynamically
Started: 1/23/2022

Current Functionality
	Write INSERT queries
	Write UPDATE queries
	Write DELETE queries

Incomplete Functionality
	Write SELECT queries
		SELECT [MIN(), MAX(), AVG(), COUNT(), SUM()]
		SELECT DISTINCT * FROM *
		SELECT * FROM *
		SELECT * FROM * ORDER BY * *
		SELECT * FROM * LIMIT *, *
		SELECT * FROM * WHERE * LIKE *
		SELECT * FROM * WHERE * IN *, *
		SELECT * FROM * WHERE * NOT IN *, *
		SELECT * FROM * WHERE * BETWEEN *, *
		SELECT * FROM * WHERE * NOT BETWEEN *, *
		SELECT * FROM * GROUP BY *
		
		- support any/all operation
		- support select into
		- support insert into select
		- support exists
		- support having
		- support union
		- union all

*/

$sql = new TableHelper($table);

// configure search
$sql->select->distinct(); // DISTINCT clause
$sql->select->limit($min, $max); // LIMIT clause
$sql->select->matches($targets, $matches); // WHERE clause
$sql->select->like($targets, $matches); // LIKE clause
$sql->select->order_by($targets, $sorting); // ORDER BY clause
$sql->select->in($targets); // IN clause
$sql->select->not_in($targets); // NOT IN clause
$sql->select->between($a, $b); // BETWEEN clause
$sql->select->not_between($a, $b); // NOT BETWEEN clause

$sql->select->group_by($columns); // GROUP BY clause

// data found
$data = $sql->select($columns);
$data = $sql->select($columns, "min");
$data = $sql->select($columns, "max");
$data = $sql->select($columns, "avg");
$data = $sql->select($columns, "count");
$data = $sql->select($columns, "sum");

foreach ($data as $key => $value):
endforeach; unset($key, $value);

$sql->update($table, $columns, $targets);
$sql->insert($table, $columns, $values);
$sql->delete($table, $target);

class TableHelper {

	public $table;
	protected $sorting;
	
	protected $order_by;
	protected $limits;
	protected $matches;
	protected $group_by;
	
	protected $like;
	protected $in;
	protected $between;
	
	public function __construct($table) {
	
		$this->select = new SQLSelectHelper;
	
	}
	
	public function select($columns, $aggregate = false) { }

}

class SQLSelectHelper {

	public $raw = "SELECT ~ FROM ~";
	
	protected $distinct;
	protected $joined;
	
	protected $columns;
	protected $table;
	protected $conditions;
	
	public function __construct() {
	}

}

?>