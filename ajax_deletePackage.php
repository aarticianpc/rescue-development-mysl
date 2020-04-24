<?php

include_once("database.php");

if(empty($_REQUEST['id'])){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Package id is missing.', 'code' => 400)));
}

$pckg_id = $_REQUEST['id'];

$sql = "SELECT id FROM packages where id = $pckg_id";
$result = sqlsrv_query($conn, $sql);

if (sqlsrv_has_rows($result) === false) {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Package is already removed.', 'code' => 400)));
}

$del_item_sql = "DELETE FROM package_items WHERE package_id=$pckg_id";
sqlsrv_query($conn, $del_item_sql);

$del_pckg_sql = "DELETE FROM packages WHERE id=$pckg_id";
if(sqlsrv_query($conn, $del_pckg_sql) !== false) {
  header('HTTP/1.1 200 Success');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Package removed successfully.', 'code' => 200)));
} else {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Sorry, package is not removed. Error: '.$conn->error.'.', 'code' => 400)));
}

