<?php

require_once('DBLogin.php');

class Query
{
	private $db_login;
	private $pdo;
	function __construct() {
        $this->db_login = new DBLogin();
		$this->db_login->connect();
		$this->pdo = $this->db_login->pdo;
    }

    /*
		API functions
    */
	public function getResult($start, $end, $table = "") {
		$s = $this->queryBuilder($start, $end, $table);
		if ($s == "Invalid Query") {
			// if the parameters give us an invalid sql statement, execute a blank one instead
			// this is allows us to still load the page with no fatal errors but just with no data results
			return "";
		}
		// if query is valid, go ahead and run it
		$result = $this->pdo->query($s);
		return $result;
	}

	public function deleteRowCount($count, $table = "") {
		$s = $this->deleteRowBuilder($count, $table = "");
		try {
			$this->pdo->query($s);
			echo "Successfully deleted row with count ".$count.".";
		} catch (PDOException $e) {
			echo "Could not delete row with count ".$count.": " . $e->getMessage();
		}
		
	}

	/* 
		helper functions
	*/
	public function queryBuilder($start, $end, $table = "") {
		// the $table parameter is optional
		// if left blank we will use our default table as defined in DBLogin.php
		if ($table == "") {
			$table = $this->db_login->default_table;
		}
		// if start is greater than the end time, or the table name has spaces, it's invalid
		if ($start >= $end || preg_match('/\s/',$table) ) {
			return "Invalid Query";
		}
		return "SELECT * FROM ".$table." WHERE date BETWEEN from_unixtime(".$start.") AND from_unixtime(".$end.")";

	}

	public function deleteRowBuilder($count, $table = "") {
		if ($table == "") {
			$table = $this->db_login->default_table;
		}
		return "DELETE FROM ".$table." WHERE count=".$count;
	}


}
?>