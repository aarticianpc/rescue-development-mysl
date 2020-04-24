<?php 
	include_once('database.php');
	$statdrop=$_REQUEST['state'];
	$packageid=$_REQUEST['id'];
	$transactionid=$_REQUEST['Trtype'];
	$countstate=count($statdrop);
	$dltqry="delete from package_data where package_id =".$packageid;
	$dlt=sqlsrv_query($conn,$dltqry);
	if($dlt){
		$sql="update packages set transaction_id=$transactionid where id =".$packageid;
		$up=sqlsrv_query($conn,$sql);
		for($i=0;$i<$countstate;$i++){
			$qry="insert into package_data (state_id,package_id) values ('".$statdrop[$i]."','".$packageid."')";
			$rs=sqlsrv_query($conn,$qry);
			
		}
		if($rs){
			header('HTTP/1.1 200 Success');
			header('Content-Type: application/json; charset=UTF-8');
			die(json_encode(array('message' => 'Package Updated successfully.', 'code' => 200)));
		}
	}
	


?>