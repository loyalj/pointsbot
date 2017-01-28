<?php

class MiniPG {

	private $user = null;
	private $password = null;
	private $host = null;
    private $port = null;
	private $database = null;
    private $connectString = null;
    private $connectOpts = null;
    private $db = null;
    private $statement = null;


    /*
    *
    */
	function __construct($databaseUrl) {
        
        // Transform the database URL into a format we can use 
        // to create the connect string for our database.
        $url = substr($databaseUrl, 0, strrpos($databaseUrl, '/'));
        $database = substr($databaseUrl, strrpos($databaseUrl, '/') + 1);
        $parsedUrl = parse_url($url);

        // Store the relevant values and create the database connect string
        $this->user = $parsedUrl['user'];
        $this->password = $parsedUrl['pass'];
        $this->host = $parsedUrl['host'];
        $this->port = $parsedUrl['port'];
        $this->database = $database;
        $this->connectString = "pgsql:dbname={$this->database};host={$this->host};port={$this->port}";
        
        $this->connectOpts = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        // Connect and store the connection
        $this->db = new PDO($this->connectString, $this->user, $this->password); //, $this->connectOpts);
	}


    /*
    *
    */
    public function save($query, $data) {
        $stmt = $this->db->prepare($query);
        $results = $stmt->execute($data);

        return $results;
    }
}