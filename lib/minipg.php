<?php

class MiniPG {

	private $user = null;
	private $password = null;
	private $host = null;
	private $path = null;
    private $connectString = null;

	function __construct($databaseUrl) {

        $url = substr($databaseUrl, 0, strpos($databaseUrl, ':'));
        $path = substr($databaseUrl, strpos($databaseUrl, ':') + 1);

        $parsedUrl = parse_url($url);


        $this->user = $parsedUrl['user'];
        $this->password = $parsedUrl['pass'];
        $this->host = $parsedUrl['host'];
        $this->path = $path;

        $this->connectString = "user={$this->user} password={$this->pass} host={$this->host} dbname=" . substr($path, 1);
        echo $this->connectString;
	}


    public function testConnection() {
        //$pgConn = pg_connect($this->connectString);
        //$result = pg_query($pg_conn, "SELECT relname FROM pg_stat_user_tables WHERE schemaname='public'");

        //if (!pg_num_rows($result)) {
        //    echo "Your connection is working, but your database is empty.\nFret not. This is expected for new apps.\n";
        //}

    }
	

}