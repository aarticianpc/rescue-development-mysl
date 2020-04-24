<?php

include_once("database.php");

if(empty($_REQUEST['id'])){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Package item id is missing.', 'code' => 400)));
}

$pckg_id = $_REQUEST['id'];

$del_item_sql = "DELETE FROM package_items WHERE id=$pckg_id";

if(sqlsrv_query($conn, $del_item_sql) !== false) {
  header('HTTP/1.1 200 Success');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Package item removed successfully.', 'code' => 200)));
} else {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Sorry, package item is not removed. Error: '.$conn->error.'.', 'code' => 400)));
}

