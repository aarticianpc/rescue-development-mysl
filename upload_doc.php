<?php
	require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
	use \PhpOffice\PhpWord\PhpWord;
	use \PhpOffice\PhpWord\TemplateProcessor;
	$base_doc_dir = __DIR__.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR;
	$base_doc_output_dir = __DIR__.DIRECTORY_SEPARATOR.'updated'.DIRECTORY_SEPARATOR;
	$base_tmp_dir =  __DIR__.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
	@chmod($base_doc_output_dir, 0777);
	@chmod($base_tmp_dir, 0777);
	$documentUpdateName = '';
	$selected_dir = '';
	$errors = [];
	$success = [];
	$data = [];
	$makefile ='';

	\PhpOffice\PhpWord\Settings::setTempDir($base_tmp_dir);

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
				$doc_file = $target_dir . str_replace(' ','-',basename($_FILES["file"]["name"]));

				if (move_uploaded_file($_FILES["file"]["tmp_name"], $doc_file)) {
					$data['status'] = 'success';
					$data['message'] = "The file ". basename( $_FILES["file"]["name"]). " has been uploaded.";
				} else {
					$data['status'] = 'error';
					$data['message'] = "Sorry, there was an error uploading your file.";
				}
			} else {
				$data['status'] = 'error';
				$data['message'] = "File formate not supported, Please upload  'docx' files only.";
			}
		} else {
			$data['status'] = 'error';
			$data['message'] = "Please select doc file.";
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
			$documentHtmlPreview = '/phpoffice-demo/tmp/html_preview.html';
			$data['documentUpdateName'] = $documentUpdateName;
			$data['documentDownloadUrl'] = $documentDownloadUrl;

			$document = new TemplateProcessor($doc_file);
			$templateVariables = $document->getVariables();
			
			$xml_arr = (array)$xml;
			$search_keys = []; 
			$search_val = [];
			foreach ($templateVariables as $key) {
				if(!empty($xml_arr[$key])) {
					if(!is_array($xml_arr[$key])) {
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
					}
				} else {
					$search_keys[] = $key;
					$search_val[] = '';
				}
			}

			$document->setValue($search_keys, $search_val);
			ob_clean();	
			$document->saveAs($documentUpdateName);

			$updated_docx = \PhpOffice\PhpWord\IOFactory::load($documentUpdateName);
			\PhpOffice\PhpWord\Settings::setPdfRendererPath($base_tmp_dir);
			\PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
			$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($updated_docx, 'HTML');
			$objWriter->save('html_preview.html');
			$data['html_preview'] = 'html_preview.html';
		}
		
		echo json_encode($data);
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