<?php

// phpinfo();

// exit;

$servername = "DESKTOP-39F4MUA";
$host = $servername.",1433";
$username = "sa";
$password = "admin123";
$db = "rescue_development";
// Create connection
$connectionOptions = array(
  "database" => $db,
  "uid" => $username,
  "pwd" => $password
);

// Establishes the connection
$conn = sqlsrv_connect($servername, $connectionOptions);
if ($conn === false) {
    echo '<pre>';
    print_r(sqlsrv_errors());
    die();
}

$dbDetails = array(
  'host' => $servername,
  'user' => $username,
  'pass' => $password,
  'db'   => $db
);