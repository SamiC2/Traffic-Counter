<?php
class DBLogin
{
	public $pdo;
	// FILL IN THIS INFORMATION
	public $default_table = 'Traffic';
	private $servername = 'database.cse.tamu.edu';
	private $user = 'justinlovelace';
	private $pw = 'test';
	private $database = 'justinlovelace';

	public function connect() {
		try {
			$this->pdo=new PDO("mysql:host=$this->servername;dbname=$this->database", $this->user, $this->pw);
			// set the PDO error mode to exception
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			return 0;
		} catch (PDOException $e) {
			echo "Connection to database failed: " . $e->getMessage();
			return -1;
		}
	}
}

?>
