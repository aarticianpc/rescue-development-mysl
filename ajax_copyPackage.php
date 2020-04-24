<?php

include_once('database.php');
include_once('functions.php');

if(empty($_REQUEST['id'])){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Package id missing.', 'code' => 400)));
}

$sql = "
    SELECT * 
    FROM packages as p
    where p.id = ".$_REQUEST['id']."
";
$result = sqlsrv_query($conn, $sql);
$packages = [];
if (sqlsrv_has_rows($result) === true) {
  // output data of each row
  $item_sql = "
    SELECT *
    FROM package_items as pi
    WHERE pi.package_id = ".$_REQUEST['id']."
  ";
  while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $packages[] = ['id' => $row['id'], 'name' => $row['name']];
    $pckg_name = $row['name'].' - copy';
    $pckg_sql = "INSERT INTO packages (name)
				VALUES ('".$pckg_name."'); SELECT SCOPE_IDENTITY();";
		$stmt = sqlsrv_query($conn, $pckg_sql);	
    if($stmt){
        $pckg_id = getLastInsertedId($stmt);
    }

    $item_result = sqlsrv_query($conn, $item_sql);
    $pckg_item_sql = "INSERT INTO package_items (package_id, item) VALUES";
    $pckg_item_sql_arr = [];
    
    while($row_item = sqlsrv_fetch_array($item_result, SQLSRV_FETCH_ASSOC)) {
        $pckg_item_sql_arr[] = "($pckg_id, '".$row_item['item']."')";
    }

    $pckg_item_sql .= implode(', ', $pckg_item_sql_arr).';';

    if(sqlsrv_query($conn, $pckg_item_sql) !== false) {
        header('HTTP/1.1 200 Success');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Package '.$row['name'].' is copied.', 'code' => 200)));
    } else {
        
        $del_pckg_sql = "DELETE FROM packages WHERE id=$pckg_id";
        sqlsrv_query($conn, $del_pckg_sql);

        header('HTTP/1.1 400 Bad Request');
        header('Content-Type: application/json; charset=UTF-8');
        die(json_encode(array('message' => 'Package '.$row['name'].' is not copied.', 'code' => 400)));
    }
    
  }
}