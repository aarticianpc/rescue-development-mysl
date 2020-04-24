<?php

include_once('database.php');


// DB table to use
$table = 'packages';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database. 
// The `dt` parameter represents the DataTables column identifier.
$columns = array(
    array( 'db' => 'id', 'dt' => 0 ),
    array( 'db' => 'name',  'dt' => 1 ),
    array(
        'db'        => 'created_at',
        'dt'        => 2,
        'formatter' => function( $d, $row ) {
            return date( 'jS M Y', strtotime($d));
        }
      ),
    array(
      'db'        => 'id',
      'dt'        => 3,
      'formatter' => function( $d, $row ) {
          return '
            <button type="button" class="btn btn-xs btn-primary select_items" name="select_items" data-package-id="'.$d.'">Select items</button>
            <a class="btn btn-xs btn-danger delete" href="javascript:void(0);" data-package='.$d.'>Delete</a>
            <a class="btn btn-xs btn-info view" data-package='.$d.' href="javascript:void(0);">View Items</a>
            <a class="btn btn-xs btn-success copy" data-package='.$d.' href="javascript:void(0);">Copy</a>
            <a class="btn btn-xs btn-warning setting" data-package='.$d.' data-name="'.ucfirst($row['name']).'" href="javascript:void(0);">Setting</a>
          ';
      }
    )
);

// Include SQL query processing class
require( 'ssp.class.sqlsrv.php' );

// Output data as json format
echo json_encode(
  SSP::simple( $_GET, $dbDetails, $table, $primaryKey, $columns )
);