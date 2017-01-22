<?php

class MiniPG {

	private $user = null;
	private $password = null;
	private $host = null;
	private $path = null;
    private $connectString = null;

	function __construct($databaseUrl) {

        extract(parse_url(($databaseUrl)));

        $this->user = $user;
        $this->password = $pass;
        $this->host = $host;
        $this->path = $path;

        $this->connectString = "user=$user password=$pass host=$host dbname=" . substr($path, 1);
	}


    public function testConnection() {
        $pgConn = pg_connect($this->connectString);
        $result = pg_query($pg_conn, "SELECT relname FROM pg_stat_user_tables WHERE schemaname='public'");

        if (!pg_num_rows($result)) {
            echo "Your connection is working, but your database is empty.\nFret not. This is expected for new apps.\n";
        }

    }
	

}