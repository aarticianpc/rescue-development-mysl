<?php
	$base_doc_dir = __DIR__.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR;
	
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
<ul>
	<?php 
		$scan_dir = scandir($base_doc_dir);
		foreach ($scan_dir as $dir) {
			if($dir != '.' && $dir != '..') {
				$_realpath = realpath($base_doc_dir.DIRECTORY_SEPARATOR.$dir);
				if(is_dir($_realpath)) {
					echo '<li>'.$dir;
					$files = getDirContents($_realpath);
						echo '<ul>';
						foreach ($files as $file) {
							$old_name = $_realpath.DIRECTORY_SEPARATOR.$file;
							$file_info = pathinfo($old_name);
							$new_name = str_replace(' ', '-', $file_info['filename']);
							$new_file_name = $_realpath.DIRECTORY_SEPARATOR.$new_name.'.'.$file_info['extension'];
							rename($old_name, $new_file_name);
							echo '<li data-jstree=\'{"icon":"glyphicon glyphicon-file"}\'>'.str_replace('-', ' ', $new_name).'.'.$file_info['extension'].'</li>';
						}
						echo '</ul>';

					echo '</li>';
				}
			}	
		}
	?>
</ul>