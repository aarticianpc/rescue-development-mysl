<?php

include_once("database.php");

if(empty($_REQUEST['id']) || empty($_REQUEST['name'])){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Package name cannot be empty.', 'code' => 400)));
}

$name = $_REQUEST['name'];
$id = $_REQUEST['id'];

$sql = "UPDATE packages SET name='".$name."' WHERE id=$id";
if(sqlsrv_query($conn, $sql) !== false) {
  header('HTTP/1.1 200 Success');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Package updated successfully.', 'code' => 200)));
} else {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Sorry, package not updated. Please try again.', 'code' => 400)));
}
