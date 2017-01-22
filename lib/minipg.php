<?php

class MiniPG {

	private $user = null;
	private $password = null;
	private $host = null;
    private $port = null;
	private $database = null;
    private $connectString = null;

	function __construct($databaseUrl) {
        $url = substr($databaseUrl, 0, strrpos($databaseUrl, '/'));
        echo $url . "\n\n";
        $database = substr($databaseUrl, strrpos($databaseUrl, '/'));
        echo $database . "\n\n" ;

        $parsedUrl = parse_url($url);
        print_r($parsedUrl);
        echo "\n\n";

        $this->user = $parsedUrl['user'];
        $this->password = $parsedUrl['pass'];
        $this->host = $parsedUrl['host'];
        $this->port = $parsedUrl['port'];
        $this->database = $database;

        $this->connectString = "user={$this->user} password={$this->password} host={$this->host} port={$this->port} dbname={$this->database}";
        echo $this->connectString;
	}


    public function testConnection() {
        /*$pgConn = pg_connect($this->connectString);
        $result = pg_query($pg_conn, "SELECT relname FROM pg_stat_user_tables WHERE schemaname='public'");

        if (!pg_num_rows($result)) {
            echo "\n\nYour connection is working, but your database is empty.\nFret not. This is expected for new apps.\n";
        }*/

    }
	

}