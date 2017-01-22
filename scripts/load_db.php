<?php

<?php
require_once '../vendor/autoload.php';
require_once '../lib/minipg.php';


$databaseUrl = getenv('DATABASE_URL');
$miniPg = new MiniPG($databaseUrl);


         'from'   => $fromUser,
         'to'     => $toUser,
         'award'  => $awardType,

$query = "CREATE TABLE awards (
    action     varchar(40) CONSTRAINT firstkey PRIMARY KEY,
    from       varchar(40) NOT NULL,
    to         varchar(40) NOT NULL,
    award      varchar(40),
    value      integer NOT NULL
);"

$miniPg->query($query);

