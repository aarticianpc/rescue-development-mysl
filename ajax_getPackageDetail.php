<?php

include_once('database.php');

if(empty($_REQUEST['id'])){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Select one package.', 'code' => 400)));
}

$item_sql = "
    SELECT *
    FROM package_items as pi
    WHERE pi.package_id = ".$_REQUEST['id']."
  ";
$item_result = sqlsrv_query($conn, $item_sql);
$data = [];
while($row_item = sqlsrv_fetch_array($item_result, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row_item['item'];
}

if(empty($data)) {
    header('HTTP/1.1 400 Bad Request');
    header('Content-Type: application/json; charset=UTF-8');
    die(json_encode(array('message' => 'Package doesn\'t contains any docs.', 'code' => 400)));
}

header('HTTP/1.1 200 Success');
header('Content-Type: application/json; charset=UTF-8');
die(json_encode(array('message' => 'Package docs are selected.', 'data' => $data, 'code' => 200)));