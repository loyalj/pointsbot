<?php

class MiniPG {

	private $user = null;
	private $password = null;
	private $host = null;
    private $port = null;
	private $database = null;
    private $connectString = null;
    private $db = null;


    /*
    *
    */
	function __construct($databaseUrl) {
        
        $url = substr($databaseUrl, 0, strrpos($databaseUrl, '/'));
        $database = substr($databaseUrl, strrpos($databaseUrl, '/') + 1);

        $parsedUrl = parse_url($url);

        $this->user = $parsedUrl['user'];
        $this->password = $parsedUrl['pass'];
        $this->host = $parsedUrl['host'];
        $this->port = $parsedUrl['port'];
        $this->database = $database;
        $this->connectString = "pgsql:dbname={$this->database};host={$this->host};port={$this->port};user={$this->user};password={$this->password}";
	}

    
    /*
    *
    */
    public function testConnection() {
        $connectStatus = $this->connect();
        $this->disconnect();

        return $connectStatus;
    }
    

    /*
    *
    */
    private function connect() {
        try {
            $this->db = new PDO($this->connectString);
            if(!$this->db) {
                return false;
            }

            return true;
        }
        catch (PDOException $e) {
            error_log($e->getMessage());
            die();
        }
    }

    
    /*
    *
    */
    private function disconnect() {
        $this->db = null;
    }
	
    /*
    *
    */
    public function query($query) {
        try {
            $this->connect();
            $results = $this->db->query($query);
            $this->disconnect();

            return $results;
        }
        catch (PDOException $e) {
            error_log($e->getMessage());
            die();
        }
    }
}