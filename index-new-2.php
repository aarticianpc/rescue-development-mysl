<?php
	require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
	use \PhpOffice\PhpWord\PhpWord;
	use \PhpOffice\PhpWord\TemplateProcessor;
	require_once __DIR__.DIRECTORY_SEPARATOR.'ExtTemplateProcessor.php';
	use DocxMerge\DocxMerge;
	$base_doc_dir = __DIR__.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR;
	$base_doc_output_dir = __DIR__.DIRECTORY_SEPARATOR.'updated'.DIRECTORY_SEPARATOR;
	$base_tmp_dir =  __DIR__.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
	@chmod($base_doc_output_dir, 0777);
	$documentUpdateName = '';
	$selected_dir = '';
	$errors = [];
	$success = [];
	$makefile ='';

	\PhpOffice\PhpWord\Settings::setTempDir($base_tmp_dir);
	

	if(!empty($_POST['make_dir'])) {

		$makefile = 'docs'.DIRECTORY_SEPARATOR.$_POST['makedir'];
		if (!(empty($_POST['sub_dir']))) {
			$sub_dir = explode(",", $_POST['sub_dir']);
			$sbmakefolder = "docs".DIRECTORY_SEPARATOR.$sub_dir[0].DIRECTORY_SEPARATOR.$_POST['makedir'];
			$documentUpdateName = mkdir($sbmakefolder);
		} else{
			$documentUpdateName =  mkdir($makefile);
		}
			
		header('Location: '.$_SERVER['REQUEST_URI']);

	}

	function recursiveRemoveDirectory($directory)
	{
	    foreach(glob("{$directory}/*") as $file)
	    {
	        if(is_dir($file)) { 
	            recursiveRemoveDirectory($file);
	        } else {
	            unlink($file);
	        }
	    }
	    rmdir($directory);
	}

	if(!empty($_POST['document_remove'])){
		if (PHP_SAPI == 'cli') {
			die('This example should only be run from a Web Browser');
		}

		$selected_folders = explode(",", $_POST['remove_folder_name']);
		$selected_files = explode(",", $_POST['remove_file_name']);

		if(!empty($selected_files)) {
			foreach ($selected_files as $key => $file) {
				$target_file = __DIR__.DIRECTORY_SEPARATOR."docs".DIRECTORY_SEPARATOR.$selected_folders[0].DIRECTORY_SEPARATOR.$file;
				if (file_exists($target_file)) {
					@chmod($target_file,0777);
					@unlink("$target_file");
				}
			}

		} else {
			$errors[] = 'Please select a files to remove.';
		}

		if(!empty($selected_folders)) {
			foreach ($selected_folders as $key => $folder) {
				$target_folder = __DIR__.DIRECTORY_SEPARATOR."docs".DIRECTORY_SEPARATOR.$folder;
				if(is_dir($target_folder))
				{
					if (!empty($_POST['only_folder'])) {
						if($_POST['only_folder'] == 'true'){
							recursiveRemoveDirectory($target_folder);
						}
					}
					header('Location: '.$_SERVER['PHP_SELF']);
				}
			}
		} else {
			$errors[] = 'Please select a directory to remove.';
		}
	}


	if(!empty($_POST['document_download'])) {
		$xml = simplexml_load_file("50828MDF-HQ.xml") or die("Error: Cannot create object");

		$xml_arr = (array)$xml;

		if (PHP_SAPI == 'cli') {
			die('This example should only be run from a Web Browser');
		}

		$file_paths = explode(",", $_POST['selected_dir']);
		$folder_path = $_POST['selected_folder'];
		$files = array();
		foreach ($file_paths as $f_key => $file_path) {

			$document_info = pathinfo($base_doc_dir.$folder_path.DIRECTORY_SEPARATOR.$file_path);
			$documentName = $document_info['filename'];

			$phpWord = new PhpWord();
			$documentUpdateName = $base_doc_output_dir.$documentName.'-updated'.'.'.$document_info['extension'];
			$documentDownloadUrl = DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR."merge-result.docx";

			$document = new ExtTemplateProcessor($base_doc_dir.$folder_path.DIRECTORY_SEPARATOR.$file_path);
			// $templateVariables = $document->getVariables();

			// $search_keys = []; 
			// $search_val = [];
			// foreach ($templateVariables as $key) {
			// 	if(!empty($xml_arr[$key])){
			// 		if(!is_array($xml_arr[$key])) {
			// 			$value = $xml_arr[$key];
			// 			$search_replace[$key] = htmlentities($value);
			// 			if($value == strip_tags($value)) {
			// 				$search_keys[] = $key;
			// 				$search_val[] = htmlentities($value);
			// 			} else {
			// 				$search_keys[] = $key;
			// 				$section = $phpWord->addSection();
			// 				$html = html_entity_decode($value);
			// 				$search_val[] = htmlentities(strip_tags($html));
			// 			}
			// 		} else {
			// 			$search_keys[] = $key;
			// 			$search_val[] = '';
			// 		}
			// 	} else {
			// 		$search_keys[] = $key;
			// 		$search_val[] = '';
			// 	}
			// }

			@unlink($documentUpdateName);

			$document->setValue($search_keys, $search_val);
			ob_clean();	
			$document->saveAs($documentUpdateName);

			header('Content-Type: application/docx');
			header('Content-Disposition: attachment; filename='.$document_info['filename'].'.'.$document_info['extension']);
			header('Pragma: no-cache');
			readfile($documentUpdateName);
			exit;
		}

		// if (count($file_paths) > 1) {		
		// 	$result_path = $base_tmp_dir."merge-result.docx";
		// 	if(file_exists($result_path))
		// 	{
		// 		@chmod($result_path,0755);
		// 		@unlink($result_path);
		// 	}

		// 	$docxMerge = \Jupitern\Docx\DocxMerge::instance()
		// 				->addFiles($files)
		// 				->save($base_tmp_dir."merge-result.docx", true);

		// 	header('Content-Type: application/docx');
		// 	header('Content-Disposition: attachment; filename=merge-result.docx');
		// 	header('Pragma: no-cache');
		// 	readfile($base_tmp_dir."merge-result.docx");
		// 	exit;			
		// } else {
		// 	$result_path = $base_tmp_dir."merge-result.docx";
		// 	if(file_exists($result_path))
		// 	{
		// 		@chmod($result_path,0755);
		// 		@unlink($result_path);
		// 	}
		// 	$docxMerge = \Jupitern\Docx\DocxMerge::instance()
		// 				->addFiles($files)
		// 				->save($base_tmp_dir."merge-result.docx", true);

		// 	header('Content-Type: application/docx');
		// 	header('Content-Disposition: attachment; filename=merge-result.docx');
		// 	header('Pragma: no-cache');
		// 	readfile($base_tmp_dir."merge-result.docx");
		// 	exit;
		// 	$file_paths = '';
		// }
	}


	if(!empty($_POST['document_upload'])) {

		if (PHP_SAPI == 'cli') {
			die('This example should only be run from a Web Browser');
		}

		if(!empty($_POST['selected_dir'])) {
			$selected_dir = $_POST['selected_dir'];
		} else {
			$errors[] = 'Please select a directory to upload file.';
		}


		if (!empty($_FILES["file"]["name"])) {
			$extension = explode(".", basename($_FILES["file"]["name"]));
			if($extension[1] == 'docx' || $extension['1'] == 'doc') {
				$target_dir = __DIR__.DIRECTORY_SEPARATOR."docs".DIRECTORY_SEPARATOR.$selected_dir.DIRECTORY_SEPARATOR;
				$doc_file = $target_dir . str_replace(' ', '-', basename($_FILES["file"]["name"]));

				if (move_uploaded_file($_FILES["file"]["tmp_name"], $doc_file)) {
					$success[] = "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
				} else {
					$errors[] = "Sorry, there was an error uploading your file.";
				}
			} else {
				$errors[] = "File formate not supported, Please upload  'docx' files only.";
			}
		} else {
			$errors[] = "Please select doc file.";
		}

		$xml_file = "50828MDF-HQ.xml";
		if (!empty($xml_file) && !empty($doc_file) && !empty($selected_dir)) {
			$xml = simplexml_load_file($xml_file) or die("Error: Cannot create object");
			/** Include PHPExcel */
			$document_info = $xml_file; 
			$documentName = $extension[0];

				
			$phpWord = new PhpWord();
			// $documentName = 'Election-to-Decline-Owner';
			// $documentName = 'companyName';
			// $documentName = 'LIMITED-POWER-OF-ATTORNEY-new';
			$documentUpdateName = $base_doc_output_dir.$documentName.'-updated'.'.'.$extension[1];
			$documentDownloadUrl = '/phpoffice-demo/updated/'.$documentName.'-updated'.'.'.$extension[1];

			$document = new ExtTemplateProcessor($doc_file);
			$templateVariables = $document->getVariables();
			
			$xml_arr = (array)$xml;
			$search_keys = []; 
			$search_val = [];
			foreach ($templateVariables as $key) {
				if(!empty($xml_arr[$key])) {
					if(!is_array($xml_arr[$key])){
						$value = $xml_arr[$key];
						$search_replace[$key] = $value;
						if($value == strip_tags($value)) {
							$search_keys[] = $key;
							$search_val[] = $value;
						} else {
							$search_keys[] = $key;
							$section = $phpWord->addSection();
							$html = html_entity_decode($value);
							$search_val[] = strip_tags($html);
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

			$document->setValue($search_keys, $search_val);
			ob_clean();	
			$document->saveAs($documentUpdateName);
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

	function findnodeintree($cats,$cat_id)
	{       
	    foreach($cats as $node)
	    {                   
	        if((int)$node['id'] == $cat_id){       
	            return $node;
	        }
	        elseif(array_key_exists('children', $node)) {
	            $r = $this->findnodeintree($node['children'], $cat_id);
	            if($r !== null){
	                return $r;
	            }
	        }           
	    }
	    return null;
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
	<script type="text/javascript" src="bower_components/core.js"></script>
	<script type="text/javascript" src="bower_components/dropzone/dropzone.min.js"></script>
	<script type="text/javascript" src="bower_components/dropzone/dropzone-amd-module.min.js"></script>

	<script type="text/javascript" src="bower_components/sweetalert/sweetalert2.min.js"></script>
	
	<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="bower_components/jstree-bootstrap-theme/dist/themes/proton/style.min.css">
	<link rel="stylesheet" type="text/css" href="bower_components/sweetalert/sweetalert2.min.css"><link rel="stylesheet" type="text/css" href="bower_components/dropzone/basic.min.css">
	<link rel="stylesheet" type="text/css" href="bower_components/dropzone/dropzone.min.css">


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
			width: 30%;
			background: #f5f5f5;
    		border-right: 1px solid #ccc;
		}
		.col.right {
			width: 70%;
			overflow: hidden;
		}
		.left-container {
			padding: 0 15px;
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
			border: 1px dashed rgba(0, 0, 0, 0.5);
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
			.button {
			display: block;
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
		.dz-message{
			display: none;
		}
		.dz-preview{
			display: none;
		}
		.drag-drop{
			padding: 25px 20px;
			border: 1px dashed #79d6e0;
			background-color: #fff;
			text-align: center;
		}
		.drag-drop span{
			font-size: 20px;
		}
		.drag-drop input{
			margin: 0 auto;
			width: 16%;
		}
		iframe{
		    overflow:hidden;
		}
	</style>	
</head>
<body>
	<div class="col-container">
		<div class="col left">
			<div class="left-container">
				<h1>Upload files to process.</h1>

				<h2>Please select a directory to which you want to upload your doc.</h2>
				<?php 
					if (!empty($errors)){
						foreach ($errors as $error) {
							echo '<p class="error">'.$error.'</p>';
						}

						foreach ($success as $s_msg) {
							echo '<p class="success">'.$s_msg.'</p>';
						}
					}
				?>
				
				<div id="jstree">
					
				</div>

				<form action="" method="post" class="form-inline" id="remove_node" enctype="multipart/form-data">
					<input type="hidden" name="remove_file_name" id="selected_files">
					<input type="hidden" name="remove_folder_name" id="selected_folders">
					<input type="hidden" name="only_folder" id="only_folder">
					<input type="hidden" name="document_remove" value="remove">
					<input class="btn btn-primary" type="submit" onclick="remove_node();" value="Delete" name="document_remove">
					<button class="btn btn-primary" id="download-doc"> Download </button>
				</form>
				<br>
				<form action="" method="post" class="form-inline " enctype="multipart/form-data">
					<div class="input-group">
						<input type="text" class="form-control" name="makedir" placeholder="Create Directory">
						<input type="hidden" name="sub_dir" id="subdir" value="">
						<span class="input-group-btn">	
							<button type="submit" class="btn btn-primary" name="make_dir" value="submit">Create Directory</button>
						</span>
					</div>
				</form>
				<br>

				<form action="upload_doc.php" method="post" id="dropzone" enctype="multipart/form-data">
						<!-- <fieldset>
						<legend>Select xml file</legend>
						<input type="file" name="xmlFile" />
						</fieldset> -->
					<div class="drag-drop">
						<span>Drag and drop doc/docx file <br> OR <br> Click to upload</span>
						<hr>
						<input type="file" id="selectedFile" name="file" />
					</div>
					<input type="hidden" name="selected_dir" id="path" value="">
					<input type="hidden" name="document_upload" value="Upload">
				</form>

				<form action="" method="post" id="download_doc" enctype="multipart/form-data" style="display: none;">
					<input type="hidden" name="selected_dir" id="download_path" value="">
					<input type="hidden" name="selected_folder" id="download_folder" value="">
					<input type="hidden" name="document_download" value="Upload">
				</form>
				<br>

				<!-- <button type="submit" class="btn btn-primary btn-lg btn-block button" name="document_upload" value="Upload">Download</button> -->
				<div class="download-button">
					<?php 
						if(!empty($documentUpdateName)){
							if(file_exists($documentUpdateName)){
								?>
								<a class="btn btn-primary btn-lg btn-block" href="<?php echo $documentDownloadUrl; ?>">Send to word</a>
								<?php	
							}
						}
					?>
				</div>
			</div>
		</div>
<!-- 		<div class="col right">
			<div style="width: 100%; height: 100%; display: table;">
				<div class="doc-helper">
					<div class="helper-container">
						<img src="images/document-icon.png" alt="Document">
						<p class="info-text">Generated docs will be appear here.</p>
					</div>	
				</div>
			</div>
		</div> -->
	</div>
	
	<script type="text/javascript">

		$(document).ready(function(){

			function sortNumber(a,b) {
			    return b - a;
			}

			var node_arr = [];
			function getParentArr(s_node){

				if(s_node.parent != "#"){
					var curr_node = $('#jstree').jstree(true).get_node(s_node.parent);
					node_arr.push(curr_node.id.split("_")[1]);
					getParentArr(curr_node);
				}
				node_arr.sort(sortNumber);
				return node_arr.reverse();
			}

			$('#jstree').jstree({
			'core' : {
				"themes": {
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
							if(parents_n.length >= 1) {
								for (var i = 0; i < parents_n.length; i++) {
									if(parents_n[i] != '#'){
										if($('#j1_'+parents_n[i].toString()).children('a').text().replace(/ /g, "-").split('.')[1] != 'docx'){
											folder.push($('#j1_'+parents_n[i].toString()).children('a').text());
										} 
									}
								}
								if(node.text.replace(/ /g, "-").split('.')[1] != 'docx'){
									folder.push(node.text);
								}
							} else {
								if(node.text.replace(/ /g, "-").split('.')[1] != 'docx'){
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
					'progressive_render': true,
            		'progressive_unload': false,
            		'cache': false,
				},
			}	
			});

	
			
			// function triggerOpenFiles(){
			// 	if($("#selected_files").val()){
			// 		if($("#selected_files").val().split(',').length == 1){
			// 			swal({
			// 				title: 'Info',
			// 				text: 'Do you want to review this file?',
			// 				type: 'info',
			// 				showCancelButton: true,
			// 				confirmButtonColor: '#3085d6',
			// 				cancelButtonColor: '#d33',
			// 				confirmButtonText: 'Ok'
			// 			}).then(function () {
			// 				var directory_separator = '\<?php echo DIRECTORY_SEPARATOR ?>';
			// 				var selected_files = $("#selected_files").val().split(',');
			// 				for (var i = 0; i < selected_files.length; i++) {
			// 					var extension = selected_files[i].split('.').pop();
			// 					var filename = selected_files[i].split('.')[selected_files[i].split('.').length - 2].split(directory_separator).pop();
			// 					window.open('docs_mht'+directory_separator+filename+'.mht', '_blank');
			// 				}
			// 			});
			// 		}
			// 	}
			// }
			// function generateJstree(){
			var i, j, r = [], n, m;
			function selectEvent(data){
				var arr_files = [];
				var arr_folders = [];
				var directory_separator = '\<?php echo DIRECTORY_SEPARATOR ?>';
				i, j, r = [], n, m;
				for(i = 0, j = data.selected.length; i < j; i++) {
					r.push(data.instance.get_path(data.selected[i]));
				}

				var directory_separator = '\<?php echo DIRECTORY_SEPARATOR ?>';
				var folder = [];
				var file = '';
				node_arr = [];


				var parents = getParentArr(data.node);

				if(parents.length >= 1){
					for (var i = 0; i < parents.length; i++) {
						if(parents[i] != '#'){
							if($('#j1_'+parents[i].toString()).children('a').text().replace(/ /g, "-").split('.')[1]){
								file += $('#j1_'+parents[i].toString()).children('a').text().replace(/ /g, "-")+directory_separator;
							} else {
								folder.push($('#j1_'+parents[i].toString()).children('a').text());
							}
						}
					}
					if(data.node.text.replace(/ /g, "-").split('.')[1] == 'docx'){
						file = data.node.text.replace(/ /g, "-");
					} else {
						if($.inArray(data.node.text, folder) === -1){
							folder.push(data.node.text);
						}
					}
				} else {
					if(data.node.text.replace(/ /g, "-").split('.')[1] == 'docx'){
						file = data.node.text.replace(/ /g, "-");
					} else {
						folder = [];
						if($.inArray(data.node.text, folder) === -1){
							folder.push(data.node.text);
						}
					}
				}

				arr_folders.push(folder.join(directory_separator));
				if(file != ""){
					arr_files.push(file);
				}

				if (data.node.children.length > 0) {
					for (var i = 0; data.node.children.length > i; i++) {
						var node_text = $('#jstree').jstree(true).get_node(data.node.children[i]);
						if(node_text.text.replace(/ /g, "-").split('.')[1] == 'docx'){
							arr_files.push(node_text.text.replace(/ /g, "-"));
						};
					}
				}

				if (data.node.text.replace(/ /g, "-").split('.')[1] != 'docx'){
					$("#only_folder").val('true');
				} else {
					$("#only_folder").val('');
				}
				console.log(arr_files, arr_folders.join(directory_separator));

				$("#download_path").val('');
				$("#download_path").val(arr_files.join(','));

				$("#selected_files").val(arr_files.join(','));

				$("#selected_folders").val(arr_folders.join(directory_separator));
				$("#download_folder").val('');
				$("#download_folder").val(arr_folders.join(directory_separator));

				$("#subdir").val('');
				$("#subdir").val(arr_folders.join(directory_separator));
				$('#path').val(arr_folders.join(directory_separator));
			}

			$('#jstree').on('changed.jstree', function (e, data) {
				if(data.node) {
					// $('#jstree').jstree(true).toggle_node(data.node);
					selectEvent(data);
				}
			});
			// }
			// generateJstree();


			function refreshJstree(){
				$.get('scan-dir-jstree.php', function(result){
					$("#jstree").jstree("destroy");
					$("#jstree").html('');
					$("#jstree").html(result);
				}).then(function(result){
					generateJstree();
				});
			}


			$("#download-doc").click(function(){
				event.preventDefault();
				if(($("#download_path").val() == '')) {
					swal('Please select file to download.!');
				} else {
					$("#download_doc").submit();
				}
			});


			$("#selectedFile").change(function(){
				var directory_separator = '\<?php echo DIRECTORY_SEPARATOR ?>';
				var doc_name = $("#selectedFile").val().split("\\").pop();
				if(/^[a-zA-Z0-9-_.]*$/.test(doc_name) == false) {
					swal("Filename should not contains special charecters, (a-Z 0-9 - _ allowed.!");
					$('#selectedFile').val('');
					return false;
				}
				if($("#selected_folders").val()){
					var re = new RegExp(doc_name.replace(/ /g, "-"), 'gi');
					if($("#selected_files").val().match(re)){
						swal({
							title: 'Are you sure?',
							text: 'File already exist on this directory. Do you want to replace doc?',
							type: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok'
						}).then(function () {
							var url = '<?php echo $_SERVER['PHP_SELF']; ?>';
							$("#dropzone").attr("action", url);
							$("#dropzone").submit();
						}, function (dismiss) {
							if (dismiss === 'cancel') {
								$('#jstree').jstree("deselect_all");
								$('#path').val('');
								$('#selectedFile').val('');
								window.location.reload();
							}
						});
					} else {
						var url = '<?php echo $_SERVER['PHP_SELF']; ?>';
						$("#dropzone").attr("action", url);
						$("#dropzone").submit();
					}
				} else {
					swal("Please select directory to upload.!");
					$('#selectedFile').val('');
				}
			})

			var myDropzone = new Dropzone("#dropzone", {
				autoProcessQueue: false,
				clickable: true
			});

			myDropzone.on("addedfile", function(data, xhr, formData){
				if($("#selected_folders").val()){
					if(/^[a-zA-Z0-9-_.]*$/.test(data.name) == false) {
						swal({
							text: 'Filename should not contains special charecters, (a-Z 0-9 - _ allowed.!',
							type: 'warning',
							showCancelButton: false,
							confirmButtonColor: '#3085d6',
							confirmButtonText: 'Ok'
						}).then(function () {
							$('#jstree').jstree("deselect_all");
							$('#path').val('');
							$('#selectedFile').val('');
							window.location.reload();
						});
						return false;
					}
					var re = new RegExp(data.name.replace(/ /g, "-"), 'gi');
					if($("#selected_files").val().match(re)){
						swal({
							title: 'Are you sure?',
							text: 'File already exist on this directory. Do you want to replace doc?',
							type: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok'
						}).then(function () {
							myDropzone.processQueue();
						}, function (dismiss) {
							if (dismiss === 'cancel') {
								$('#jstree').jstree("deselect_all");
								$('#path').val('');
								$('#selectedFile').val('');
								window.location.reload();
							}
						});
					} else {
						swal({
							title: 'Are you sure?',
							text: 'Do you want to upload this doc?',
							type: 'warning',
							showCancelButton: true,
							confirmButtonColor: '#3085d6',
							cancelButtonColor: '#d33',
							confirmButtonText: 'Ok'
						}).then(function () {
							myDropzone.processQueue();
						}, function (dismiss) {
							if (dismiss === 'cancel') {
								$('#jstree').jstree("deselect_all");
								$('#path').val('');
								$('#selectedFile').val('');
								window.location.reload();
							}
						});
					}
				} else {
					swal("Please select directory to upload.!");
				}
			});

			$('#jstree')
				.on('select_node.jstree', function(e, data){
	                $('#jstree').jstree(true).open_node(data.node,function(o_data){
	                	$('#jstree').on('after_open.jstree', function (e, a_data) {
	                		data.node.children = a_data.node.children;
	                		selectEvent(data);
	                		//toggleButton();
	                	});
	                });
				});

			myDropzone.on("complete", function(data) {
				var result = JSON.parse(data.xhr.response);
				$(".download-button").html('');
				$(".download-button").html('<a class="btn btn-primary btn-lg btn-block" href="'+result.documentDownloadUrl+'">Send to word</a>');
				// $(".right").html('');
				// $(".right").html('<div style="width: 100%; height: 90%; padding-left: 65px;"><iframe class="frame" width="100%" height="100%" src="'+result.html_preview+'"></iframe><div><font color="red">**This is basic web view of doc. Send this doc to word to see its proper format</font></div></div>');
				swal({
					title: result.status,
					text: result.message,
					html: '<p>'+result.message+'</p>',
					showCancelButton: false,
					confirmButtonColor: '#3085d6',
					confirmButtonText: 'Ok',
				}).then(function () {
					$('#path').val('');
					$('#selectedFile').val('');
					window.location.reload();
				});
			});

		});

		function remove_node(){
			event.preventDefault();
			if(($("#selected_folders").val() == '')) {
				swal('Please select file or directory to remove.!');
			} else {
				swal({
					title: 'Are you sure?',
					text: "You won't be able to revert this!",
					type: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, remove it!'
				}).then(function () {
					swal({
						title: 'Enter password to delete.',
						input: 'password',
						showCancelButton: true,
						confirmButtonText: 'Submit',
						showLoaderOnConfirm: true,
						preConfirm: function (password) {
							return new Promise(function (resolve, reject) {
								if (password === 'admin4321') {
									resolve();
								} else {
									reject('Incorrect password.!.');
								}
							})
						},
						allowOutsideClick: false
					}).then(function (password) {
						$("#remove_node").submit();
					});
				});
			}
		}

	</script>
</body>
</html>
	
