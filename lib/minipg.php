<?php

class MiniPG {

	private $user = null;
	private $password = null;
	private $host = null;
    private $port = null;
	private $database = null;
    
    private $connectString = null;
    private $pdoString = null;

	function __construct($databaseUrl) {
        $url = substr($databaseUrl, 0, strrpos($databaseUrl, '/'));
        $database = substr($databaseUrl, strrpos($databaseUrl, '/') + 1);

        $parsedUrl = parse_url($url);

        $this->user = $parsedUrl['user'];
        $this->password = $parsedUrl['pass'];
        $this->host = $parsedUrl['host'];
        $this->port = $parsedUrl['port'];
        $this->database = $database;

        $this->connectString = "user={$this->user} password={$this->password} host={$this->host} port={$this->port} dbname={$this->database}";
        $this->pdoString = "pgsql:dbname={$this->database};host={$this->host};port={$this->port};user={$this->user};password={$this->password}";
	}


    public function testConnection() {
        //$pgConn = pg_connect($this->connectString);
        //$result = pg_query($pgConn, "SELECT relname FROM pg_stat_user_tables WHERE schemaname='public'");

        $db = new PDO($this->pdoString);

        if(!$db) {
            return false;
        }

        return true;
    }
	

}