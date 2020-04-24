<?php
	session_start();
	
	// $url =  "//{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	
	// $escaped_url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
	// $query_str = parse_url($escaped_url, PHP_URL_QUERY);
	// parse_str($query_str, $query_params);
	// echo $_GET['url'];
	$base_doc_dir = __DIR__.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.$_SESSION['url'].DIRECTORY_SEPARATOR.$_GET['folder'].DIRECTORY_SEPARATOR;
	$scan_dir = scandir($base_doc_dir);
	$data = array();


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

	foreach ($scan_dir as $key => $dir) {
		if($dir != '.' && $dir != '..') {
			$_realpath = realpath($base_doc_dir.DIRECTORY_SEPARATOR.$dir);
			$data_obj = new stdClass();
			if(is_dir($_realpath)) {
				$data_obj->text = $dir;
				$files = getDirContents($_realpath);
				$data_arr = array();
				if(!empty($files)){
					$data_obj->children = true;
				} else {
					$data_obj->children = false;
				}
				if(is_file($dir)){
					$data_obj->icon = "glyphicon glyphicon-file";
				}
				// foreach ($files as $file) {
				// 	$old_name = $_realpath.DIRECTORY_SEPARATOR.$file;
				// 	if(is_dir($old_name)){
				// 		$data_arr[] = $file;
				// 	}
				// 	if(is_file($old_name)) {
				// 		$files = getDirContents($_realpath);
				// 		$file_info = pathinfo($old_name);
				// 		$new_name = str_replace(' ', '-', $file_info['filename']);
				// 		$new_file_name = $_realpath.DIRECTORY_SEPARATOR.$new_name.'.'.$file_info['extension'];
				// 		rename($old_name, $new_file_name);
				// 		$data_arr[] = str_replace("-", " ", str_replace("'", '', $new_name)).'.'.$file_info['extension'];
				// 	}
				// }
			} else {
				$data_obj->text = $dir;
				$data_obj->children = false;
				$data_obj->icon = "glyphicon glyphicon-file";
			}
			$data[] = $data_obj;
		}
	}

	echo json_encode($data);