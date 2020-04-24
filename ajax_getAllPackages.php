<?php

include_once('database.php');

$sql = "SELECT id, name FROM packages";
$result = sqlsrv_query($conn, $sql);
$packages = [];
if (sqlsrv_has_rows($result) === true) {
  // output data of each row
  while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $packages[] = ['id' => $row['id'], 'name' => $row['name']];
  }
}

header('HTTP/1.1 200 Success');
header('Content-Type: application/json; charset=UTF-8');
die(json_encode($packages));