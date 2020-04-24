<?php
	session_start();

	include_once('database.php');
	include_once('functions.php');

	ini_set('error_reporting', 1);
	error_reporting(E_ALL);
	
	$url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

	$escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
	$query_str = parse_url($escaped_url, PHP_URL_QUERY);
	parse_str($query_str, $query_params);
	$_SESSION['url'] = (!empty($_GET['url'])) ? $_GET['url'] : '';
	
	
	require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
	use \PhpOffice\PhpWord\PhpWord;
	use \PhpOffice\PhpWord\TemplateProcessor;
	require_once __DIR__.DIRECTORY_SEPARATOR.'ExtTemplateProcessor.php';
	use DocxMerge\DocxMerge;
	$base_doc_dir = __DIR__.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.$_SESSION['url'].DIRECTORY_SEPARATOR;
	$base_doc_output_dir = __DIR__.DIRECTORY_SEPARATOR.'updated'.DIRECTORY_SEPARATOR;
	
	@chmod($base_doc_output_dir, 0777);
	$base_tmp_dir =  __DIR__.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
	$documentUpdateName = '';
	$documentDownloadUrl = '';

	\PhpOffice\PhpWord\Settings::setTempDir($base_tmp_dir);

	if(!empty($_POST['document_upload']) || !empty($_POST['save_package'])) {

		$xml = simplexml_load_file("50828MDF-HQ.xml") or die("Error: Cannot create object");

		$xml_arr = (array)$xml;

		if (PHP_SAPI == 'cli') {
			die('This example should only be run from a Web Browser');
		}
		$selected_dir = '';
		if(!empty($_POST['selected_dir'])) {
			
			$selected_dir = $_POST['selected_dir'];

		}
		
		if(empty($selected_dir)){
			$_SESSION['errors'] = array('No files found for merging.');
			header('location: index-new.php');
		}

		$file_paths = explode(",", $selected_dir);
		$pckg_name = (!empty($_POST['pckg_name']))? $_POST['pckg_name'] : '';
		
		if (!empty($pckg_name)) {
			$pckg_name = mssql_escape($pckg_name);	
			$sql = "SELECT id, name FROM packages where name = '$pckg_name'";

			$result = sqlsrv_query($conn, $sql);
			
			if (sqlsrv_has_rows($result) === true) {
				// output data of each row
				while($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
					$pckg_id = $row['id'];
					$pckg_name = $row['name'];
				}
			} else {
				$pckg_sql = "INSERT INTO packages (name)
							VALUES ('".$pckg_name."'); SELECT SCOPE_IDENTITY();";
				$stmt = sqlsrv_query($conn, $pckg_sql);
				if($stmt){
					$pckg_id = getLastInsertedId($stmt);
				}
			}
			if(!empty($pckg_id) && !empty($pckg_name)){
				$success_msg[] = 'Following files has been saved to package: '.$pckg_name.' <ul>';
				$pckg_item_sql = "INSERT INTO package_items (package_id, item) VALUES";
				$pckg_item_val_sql = [];
				foreach($file_paths as $file) {
					$file = mssql_escape($file);
					$item_sql = "SELECT id FROM package_items where package_id = $pckg_id and item = '$file'";
					$item_result = sqlsrv_query($conn, $item_sql);
			
					if (sqlsrv_has_rows($item_result) == false) {
						$pckg_item_val_sql[] = "($pckg_id, '".$file."')";
						$success_msg[] = '<li>'.$file.'</li>';
					}
				}
				$pckg_item_sql .= implode(', ', $pckg_item_val_sql).';';
				$success_msg[] = '</ul>';
				sqlsrv_query($conn, $pckg_item_sql);
				
				if (count($success_msg) > 2) {
					$_SESSION['success'] = array(implode($success_msg));
				}
					
			}
		}
		if(!empty($_POST['document_upload'])) {

			$files = array();
			foreach ($file_paths as $f_key => $file_path) {
				
				$document_info = pathinfo($base_doc_dir.$file_path);
				$documentName = $document_info['filename'];
					
				$phpWord = new PhpWord();
				
				$documentUpdateName = $base_doc_output_dir.$documentName.'-updated'.'.'.$document_info['extension'];
				$documentDownloadUrl = DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR."merge-result.docx";
				
				$document = new ExtTemplateProcessor($base_doc_dir.$file_path);
				$templateVariables = $document->getVariables();

				$search_keys = []; 
				$search_val = [];
				foreach ($templateVariables as $key) {
					if(!empty($xml_arr[$key])){
						if(!is_array($xml_arr[$key])) {
							$value = $xml_arr[$key];
							$search_replace[$key] = htmlentities($value);
							if($value == strip_tags($value)) {
								$search_keys[] = $key;
								$search_val[] = htmlentities($value);
							} else {
								$search_keys[] = $key;
								// $section = $phpWord->addSection();
								$html = html_entity_decode($value);
								$search_val[] = htmlentities(strip_tags($html));
							}
						} else {
							$search_keys[] = $key;
							$search_val[] = '';
						}
					} else {
						$search_keys[] = $key;
						$search_val[] = '';
					}
				}
				
				@unlink($documentUpdateName);
				$document->setValue($search_keys, $search_val);
				ob_clean();	
				$document->saveAs($documentUpdateName);

				if(file_exists($documentUpdateName)){
					$files[$f_key] = $documentUpdateName;
				}
			}
			
			if (count($files) > 0) {	
				$file_name = "merge-result";
				$result_path = $base_tmp_dir.".docx";
				
				if(file_exists($result_path))
				{
					@chmod($result_path,0755);
				}
				
				$docxMerge = \Jupitern\Docx\DocxMerge::instance()
							->addFiles($files)
							->save($base_tmp_dir.$file_name.".docx", true);
				// exit;

				header('Content-Type: application/docx');
				header('Content-Disposition: attachment; filename='.$file_name.'.docx');
				header('Pragma: no-cache');
				readfile($base_tmp_dir.$file_name.".docx");
				
				exit;
			}
		}
	}
	
	function getDirContents($dir, &$results = array()){
		$files = scandir($dir);

		foreach($files as $key => $value){
		    $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
		    if(!is_dir($path)) {
		        $results[] = $value;
		    } else if($value != "." && $value != "..") {
		        getDirContents($path, $results);
		        $results[] = $value;
		    }
		}

		return $results;
	}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<title>Dynamic Word Docs</title>
		<script type="text/javascript" src="bower_components/jquery/dist/jquery.min.js"></script>
		<script type="text/javascript" src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="bower_components/jstree-bootstrap-theme/dist/jstree.min.js"></script>
		<script type="text/javascript" src="bower_components/sweetalert/sweetalert2.min.js"></script>
		
		<!-- Datatables -->
		<script type="text/javascript" src="bower_components/datatables.net/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
		<script type="text/javascript" src="bower_components/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
		<script type="text/javascript" src="bower_components/datatables.net-responsive-bs/js/responsive.bootstrap.min.js"></script>
		<script type="text/javascript" src="CellEdit/js/dataTables.cellEdit.js"></script>

		<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="bower_components/font-awesome.min.css">
		<link rel="stylesheet" href="bower_components/font-awesome.css">
		<link rel="stylesheet" href="bower_components/jstree-bootstrap-theme/dist/themes/proton/style.min.css">
		<link rel="stylesheet" type="text/css" href="bower_components/sweetalert/sweetalert2.min.css">
		
		<!-- Datatables -->
		<link rel="stylesheet" type="text/css" href="bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="bower_components/datatables.net-responsive-bs/css/responsive.bootstrap.min.css">
		
		<style type="text/css">
			html {
				height: 100%;
			}
			body {
				font-family: Arial,sans-serif;
				font-size: 14px;
				margin: 0;
				padding: 0;
				height: 100%;
				min-height: 100%;
			}
			h1 {
				font-size: 1.6em;
			}
			h2 {
				font-size: 1.5em;
			}
			hr {
				margin-top: 10px;
				margin-bottom: 10px;
			}
			.col-container {
				display: table;
				width: 100%;
				height: 100%;
				table-layout: fixed;
			}
			.col {
				display: table-cell;
				vertical-align: middle;
			}
			.col.left {
				width: 40%;
				background: #f5f5f5;
	    		border-right: 1px solid #ccc;
			}
			.col.right {
				width: 60%;
				overflow: hidden;
			}
			.left-container {
				padding: 0 15px;
			}

			.delete,
			.copy {
				display: none;
			}
			
			#jstree {
				overflow-y: hidden;
				overflow-x: auto;
				width: 100%;
				margin-bottom: 15px;
			}
			.left-container fieldset{
				padding: 15px;
			}
			.left-container fieldset,
			.left-container legend {
				border: 1px solid rgba(0, 0, 0, 0.5);
			}
			.left-container legend {
				padding: 3px 5px;
				margin: 0;
				font-size: 1em;
				display: inline-block;
				width: auto;
			}
			.left-container fieldset + fieldset {
				margin-top: 15px;
			}
			.btn.send_to_word,
			.btn.reset_selection {
				margin: 10px 0;
				padding: 20px 10px;
				color: rgba(255, 255, 255, 0.8);
				text-decoration: none;
				border-radius: 3px;
				border: none;
				font-size: 1.5em;
				font-weight: bold;
				text-align: center;
				box-sizing: border-box;
			}
			.btn-group.special {
				display: flex;
			}

			.special .btn {
				flex: 1;
			}
			.error {
				font-size: 1em;
				background: #e20b0b;
				padding: 10px;
				color: rgba(255, 255, 255, 0.8);
			}
			.frame {
				box-sizing: border-box;
				border: none;
			}
			.doc-helper {
				text-align: center;
				text-align: center;
				background: #79d6e0;
				width: 100%;
				height: 100%;
				display: table;
			}
			.helper-container {
				display: table-cell;
				vertical-align: middle;
			}
			.doc-helper img {
				max-width: 200px;
	    		margin: auto;
			}
			.info-text {
				font-size: 2em;
				font-weight: bold;
				color: rgba(0, 0, 0, 0.8);
			}

		</style>
	</head>
	<body>
		<div class="col-container">
			<div class="col left">
				<div class="left-container">
					<h1>Select files to process.</h1>
					<?php 
						if (!empty($_SESSION['errors'])){
							foreach ($_SESSION['errors'] as $error) {
								echo '<div class="alert alert-danger alert-dismissible"> 
									<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.$error.'
								</div>';
							}
							$_SESSION['errors'] = '';
						}
						if(!empty($_SESSION['success'])) {
							foreach ($_SESSION['success'] as $s_msg) {
								echo '<div class="alert alert-success alert-dismissible"> 
									<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'.$s_msg.'
								</div>';
							}
							$_SESSION['success'] = '';
						}
						// 
					?>
					
					<div id="jstree">
						
					</div>
					<form action="" method="post" onsubmit="validateSelections();" enctype="multipart/form-data">
						<input type="hidden" name="selected_dir" id="path" value="">
						<div class="btn-group special">
							<button type="submit" class="btn btn-primary btn-lg disabled send_to_word" id="send_to_word" name="document_upload" value="Upload">Send to word</button>
							<button type="button" class="btn btn-danger btn-lg disabled reset_selection" name="reset_selection" value="reset_selection">Reset</button>
						</div>
					</form>
				</div>
			</div><!-- ./col.left ends here -->
			<div class="col-right">
				<div class="container-fluid">
					<div class="row">
						<div class="col-xs-12">
							<h1>Packages</h1>
							<hr/>
							<table class="table table-striped table-bordered nowrap" id="table_packages" style="width=100%">
								<thead>
									<tr>
										<td>ID</td>
										<td>Name</td>
										<td>Created At</td>
										<td>Actions</td>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<td>ID</td>
										<td>Name</td>
										<td>Created At</td>
										<td>Actions</td>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div> <!-- ./col.right ends here -->
		</div>

		<!-- Modal -->
		<div id="viewModal" class="modal fade" role="dialog">
			<div class="modal-dialog">

				<!-- Modal content-->
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title">Package items</h4>
					</div>
					<div class="modal-body">
						<input type="hidden" name="package_id" id="package_id" value="">
						<table class="table table-striped table-bordered nowrap" id="table_package_items" style="width=100%">
							<thead>
								<tr>
									<td>ID</td>
									<td>Name</td>
									<td>Created At</td>
								</tr>
							</thead>
							<tfoot>
								<tr>
									<td>ID</td>
									<td>Name</td>
									<td>Created At</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>

			</div>
		</div>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
		<script type="text/javascript">
			function getFileExtension(filename) {
				//console.log(filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2));
			  	return filename.slice((filename.lastIndexOf(".") - 1 >>> 0) + 2);
			}
			var arr = [];
			var arr_files = [];
			var arr_folders = [];
			$(document).ready(function(){

				var table = $('#table_packages').DataTable({
					"processing": true,
					"serverSide": true,
					"ajax": "ajax_getPackages.php",
					// "responsive": true,
					"columns": [
						{ "width": "2%" },
						{ "width": "38%" },
						{ "width": "30%" },
						{ "width": "30%" },
					],
				});



				$('body').on('click', '.view', function(e){
					var pckg_id = $(e.target).data('package');
					$('#viewModal').modal('show');
					$('#package_id').val(pckg_id);
					console.log(pckg_id);
				});
				
				$("#viewModal").on('shown.bs.modal', function(e){
					
					$('#table_package_items').DataTable({
						"processing": true,
						"serverSide": true,
						"ajax": {
							url: "ajax_getPackageItems.php",
							data: {
								pckg_id: $('#package_id').val()
							}
						},
						"responsive": true,
						"columns": [
							{ "width": "2%" },
							{ "width": "78%" },
							{ "width": "20%" }
						]
					});
				});

				$("#viewModal").on('hide.bs.modal', function(){
					$('#package_id').val('');
					$('#table_package_items').DataTable().destroy();
					$('#table_package_items').empty();
					$('#table_package_items').html('<thead><tr><td>ID</td><td>Name</td><td>Created At</td><td>Actions</td></tr></thead><tfoot><tr><td>ID</td><td>Name</td><td>Created At</td><td>Actions</td></tr></tfoot>');
				});

				// var jstree_new = $('#jstree').jstree({
				// 	'core' : {
				// 			"themes" : {
				// 				'name': 'proton',
				// 				'responsive': true,
				// 				'variant': 'large'
				// 			},
				// 			"check_callback" : true,
				// 	},
				// 	"plugins" : [ "checkbox" ]
				// });
				var node_arr = [];
				function sortNumber(a,b) {
				    return b - a;
				}

				var node_arr = [];
				function getParentArr(s_node){
					if(s_node){
						if(s_node.parent != "#"){
							var curr_node = $('#jstree').jstree(true).get_node(s_node.parent);
							node_arr.push(curr_node.id.split("_")[1]);
							getParentArr(curr_node);
						}
						node_arr.sort(sortNumber);
						return node_arr.reverse();
					} else {
						return node_arr;
					}
				}

				var i, j, r = [], n, m;
				function selectEvent(data){
					
					arr_folders = [];
					var directory_separator = '\<?php echo DIRECTORY_SEPARATOR ?>';
					i, j, r = [], n, m;
					
					for(i = 0, j = data.selected.length; i < j; i++) {
						r.push(data.instance.get_path(data.selected[i]));
						//console.log(r);	
					}

					var directory_separator = '\<?php echo DIRECTORY_SEPARATOR ?>';
					var folder = [];
					var file = [];
					node_arr = [];
					var parents = getParentArr(data.node);
					//console.log($parents);
					node_arr = [];
					if(parents.length >= 1){
						for (var i = 0; i < parents.length; i++) {
							if(parents[i] != '#'){
								var _node = $('#j1_'+parents[i]).children('a').text();
								if(getFileExtension(_node.replace(/ /g, "-"))){
									file.push(_node);
								} else {
									folder.push(_node);
								}
							}
						}
						
						// if(data.node.text.replace(/ /g, "-").split('.')[1] == 'docx'){
						if(getFileExtension(data.node.text) == 'docx'){
							file.push(data.node.text);
						} else {
							if($.inArray(data.node.text, folder) === -1){
								folder.push(data.node.text);
							}
						}
					} else {
						if(data.node.text.replace(/ /g, "-").split('.')[1] == 'docx'){
							file.push(data.node.text);
						} else {
							folder = [];
							if($.inArray(data.node.text, folder) === -1){
								folder.push(data.node.text);
							}
						}
					}

					arr_folders.push(folder.join(directory_separator));
					//console.log(folder);
					arr_folders.push(file);
					// console.log(arr_folders);
					arr_folders = arr_folders.filter(function(v){return v!==''});
					//console.log(arr_folders.join(directory_separator), arr_files);
					
					if(getFileExtension(arr_folders.join(directory_separator)) == 'docx'){
						
						if($.inArray(arr_folders.join(directory_separator), arr_files) === -1){
							
							arr_files.push(arr_folders.join(directory_separator));
						}
					}
					
					arr_files = arr_files.filter(function(v){return v!==''});
					arr_files = [];
					// console.log(arr_files);
					// console.log('hiii');
					if (r.length > 0) {
						
						for (var i = 0; r.length > i; i++) {

							// console.log(r[i]);
							
							if(getFileExtension(r[i].join(directory_separator)) == 'docx'){
								arr_files.push(r[i].join(directory_separator).replace(/ /g, "-"));
							};
						}
						console.log(arr_files);
						
					}
					$('#path').val(arr_files.join(","));
				}

				$('body').on('click', '.select_items', function(e){
					var pckg_id = $(e.target).data('packageId');
					$.ajax({
						url: 'ajax_getPackageDetail.php',
						dataType: 'text',
						type: 'post',
						contentType: 'application/x-www-form-urlencoded',
						data: {
							id: pckg_id
						},
						success: function( data, textStatus, jQxhr ){
							data = JSON.parse(data);
							for(var i = 0; i < data.data.length; i++) {
								// console.log(data.data[i]);
								openRecursiveDirandSelect(data.data[i]);
							}
							swal("Success", data.message, "success");
						},
						error: function( jqXhr, textStatus, errorThrown ){
							var res = JSON.parse(jqXhr.responseText)
							swal("Bad Request!", res.message, "error");
							// console.log( res, textStatus, errorThrown );
						}

					})
					// console.log(pckg_id);
					// var pckg_item = 'Policy Cover Letter.docx';
					// var pckg_item = 'affiliatedocs/24-Month-Chain-of-Title.docx';
					// openRecursiveDirandSelect(pckg_item);
				});

				function openRecursiveDirandSelect(docWithFullPath, i) {
					i = (i != undefined) ? i : 0;
					var pckg_item_arr = docWithFullPath.split('\\');
					var node_id = $('.jstree-anchor:contains("'+pckg_item_arr[i]+'")').parent('.jstree-node').attr('id');
					// console.log(i, node_id, pckg_item_arr[i]);
					if(i == (pckg_item_arr.length - 1)) {
						// console.log('call back called..')
						$('#jstree').jstree('select_node', "#"+node_id);
					} else {
						$('#jstree').jstree('open_node', "#"+node_id, function(){
							i++;
							openRecursiveDirandSelect(docWithFullPath, i);
						});
					}
				}

				arr_files = [];
				$('#jstree').jstree({
					'core' : {
						"themes" : {
							'name': 'proton',
            				'responsive': true,
							'variant': 'large'
						},
						"check_callback" : true,
						'data' : {
							'url' : function (node) {
								var directory_separator = '\<?php echo DIRECTORY_SEPARATOR ?>';
								var folder = [];
								if(node.id != "#"){
									node_arr = [];
									var parents_n = getParentArr(node);
									if(parents_n.length >= 1){
										for (var i = 0; i < parents_n.length; i++) {
											if(parents_n[i] != '#'){
												if(getFileExtension($('#j1_'+parents_n[i].toString()).children('a').text().replace(/ /g, "-")) != 'docx'){
													folder.push($('#j1_'+parents_n[i].toString()).children('a').text());
												} 
											}
										}
										if(getFileExtension(node.text.replace(/ /g, "-")) != 'docx'){
											folder.push(node.text);
										}
									} else {
										if(getFileExtension(node.text.replace(/ /g, "-")) != 'docx'){
											folder.push(node.text);
										}
									}
								}
								
								return node.id === '#' ?
								'ajax_tree_root.php' :
								'ajax_tree_children.php?folder='+folder.join(directory_separator);
							},
							dataType: 'JSON',
							'data' : function (node) {
								return { 'id' : node.id };
							},
							timeout: 2000,
							'progressive_render': true,
		            		'progressive_unload': false,
		            		'cache': false,
						},
					},
					"plugins" : [ "checkbox" ]	
				});


				$('#jstree')
					.on('select_node.jstree', function(e, data){
		                $('#jstree').jstree(true).open_node(data.node,function(o_data){
							var selected = $('#jstree').jstree(true).get_selected();
							console.log('select event', selected);
							data.selected = selected;
							selectEvent(data);
							toggleButton();
							// $('#jstree-new').on('after_open.jstree', function (e, a_data) {
							// 	console.log(data);
		                	// 	var selected = $('#jstree-new').jstree(true).get_selected();
		                	// 	data.selected = selected;
		                	// 	selectEvent(data);
		                	// 	toggleButton();
		                	// });
		                });
					});


				function toggleButton(){
					if(arr_files.length > 0){
						$("#send_to_word").removeClass("disabled");
						$(".reset_selection").removeClass("disabled");
					} else {
						$("#send_to_word").addClass("disabled");
						$(".reset_selection").addClass("disabled");
					}
				}

				$('body').on('click', '.reset_selection', function(){
					var r = confirm('Are you sure?');
					if(r === true) {
						$('#jstree').jstree("deselect_all");
						arr_files = [];
						toggleButton();
					}
				});

				function changedJstree(){
					$('#jstree')
						.on('changed.jstree', function (e, data) {
							if(data.node){
								selectEvent(data);
							}
							toggleButton();
						});
				}
				changedJstree();
			});

			function validateSelections(){
				// alert('hii');
				if(arr_files.length == 0){
					swal('', 'Please select a document first.!', 'warning');
					event.preventDefault();
					return false;
				}
			};
			
		</script>
			
	</body>
</html>
