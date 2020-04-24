<?php

include_once('database.php');

if(empty($_REQUEST['item'])){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Select only one document.', 'code' => 400)));
}

if(empty($_REQUEST['ids'])){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Select only one package.', 'code' => 400)));
}

$del_sql = "DELETE from package_items where package_id IN (".implode(',', $_REQUEST['ids']).") and item = '".$_REQUEST['item']."'"; 

if(sqlsrv_query($conn, $del_sql) !== false) {
  header('HTTP/1.1 200 Success');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Document removed from selected packages successfully.', 'code' => 200)));
} else {
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Sorry, Document is not removed. Error: '.$conn->error.'.', 'code' => 400)));
}