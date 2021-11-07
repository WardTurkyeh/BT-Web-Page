<?php
$serverName = "BJD-OE-01-0053\SQLEXPRESS";

$connectionInfo = array( "Database"=>"mvd");
$conn = sqlsrv_connect( $serverName, $connectionInfo);

if (!$conn){
    die("<script>alert('Connection Failed.')</script>");
}

$base_url = "http://localhost/MVD%20Upload%20-%20BIMTEC/"; // Website url

?>
