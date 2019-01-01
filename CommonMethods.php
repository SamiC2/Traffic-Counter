<?php 
/*****************************************
** File:    CommonMethods.php
** Project: CSCE 315 Project 1, Spring 2018
** Author:  Justin Lovelace
** Date:    3/28/2018
** Section: 505
** E-mail:  justinlovelace@tamu.edu
**
** This file redirects was provided by Professor
** Lupoli and is responsible for connecting to
** the database.
***********************************************/


class Common
{	
	var $conn;
	var $debug;
	
	var $db="database.cse.tamu.edu";
	var $dbname="justinlovelace";
	var $user="justinlovelace";
	var $pass="test";
			
	function Common($debug)
	{
		$this->debug = $debug; 
		$rs = $this->connect($this->user); // db name really here
		return $rs;
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% */
	
	function connect($db)// connect to MySQL DB Server
	{
		try
		{
			$this->conn = new PDO('mysql:host='.$this->db.';dbname='.$this->dbname, $this->user, $this->pass);
	    	} catch (PDOException $e) {
        	    print "Error!: " . $e->getMessage() . "<br/>";
	            die();
        	}
	}

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% */

	function executeQuery($sql, $filename) // execute query
	{
		if($this->debug == true) { echo("$sql <br>\n"); }
		$rs = $this->conn->query($sql) or die("Could not execute query '$sql' in $filename"); 
		return $rs;
	}			

} // ends class, NEEDED!!

?>