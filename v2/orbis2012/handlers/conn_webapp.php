<?php
$dbhost = getenv("DBHOST") ?: "localhost";
$dbname = getenv("DBNAME") ?: "orbis";
$dbuser = getenv("DBUSER") ?: "webapp";

$connectString_orbis = "host={$dbhost} dbname={$dbname} user={$dbuser}";

if (getenv("DBPASSWORD")) $connectString_orbis .= " password={getenv('DBPASSWORD')}";
?>
