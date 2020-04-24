<?php

    include_once("database.php");   
    $pckg_id = $_REQUEST['id'];
    $qry="select packages.id,packages.transaction_id,package_data.state_id from packages LEFT JOIN package_data on packages.id = package_data.package_id where packages.id=".$pckg_id;
    //echo $qry;exit;
    $rs=sqlsrv_query($conn, $qry);
    $setdata = [];
    while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)) {
        $setdata['state_ids'][] = $row['state_id'];
        $setdata['Transaction_id'] = $row['transaction_id'];
    }
    
    die(json_encode($setdata));
?>