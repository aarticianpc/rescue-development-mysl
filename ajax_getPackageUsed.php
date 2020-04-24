<?php

include_once('database.php');

if(empty($_REQUEST['item'])){
  header('HTTP/1.1 400 Bad Request');
  header('Content-Type: application/json; charset=UTF-8');
  die(json_encode(array('message' => 'Select only one package.', 'code' => 400)));
}

$dbDetails = array(
  'host' => $host,
  'user' => $username,
  'pass' => $password,
  'db'   => $db
);

// DB table to use
$table = 'packages';
$joinQuery = "FROM {$table} p LEFT JOIN package_items AS pi ON (p.[id] = pi.[package_id])";
$extraCondition = "pi.[item] = '".$_REQUEST['item']."'";

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The db parameter represents the column name in the database. 
// The dt parameter represents the DataTables column identifier.
$columns = array(
    array(
      'db'        => 'p.[id]',
      'dt'        => 0,
      'field'     => 'id',
      'formatter' => function( $d, $row ) {
        // return print_r($row, true);
        return '<input type="checkbox" name="bulk_delete[]" class="bulk_delete" value="'.$d.'" />';
      }
    ),
    array( 'db' => 'p.[id]', 'dt' => 1, 'field' => 'id' ),
    array( 'db' => 'p.[name]',  'dt' => 2, 'field' => 'name' ),
    array(
        'db'        => 'p.[created_at]',
        'dt'        => 3,
        'field'     => 'created_at',
        'formatter' => function( $d, $row ) {
            return date( 'jS M Y', strtotime($d));
        }
    ),
);

// Include SQL query processing class
require( 'ssp-join.sqlsrv.php' );

// Output data as json format
echo json_encode(
    SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns, $joinQuery, $extraCondition)
);