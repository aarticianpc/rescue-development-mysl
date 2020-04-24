<?php
	require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
	use \PhpOffice\PhpWord\PhpWord;
	use \PhpOffice\PhpWord\TemplateProcessor;

	if(!empty($_POST['company_name'])) {
		// error_reporting(E_ALL);
		// ini_set('display_errors', TRUE);
		// ini_set('display_startup_errors', TRUE);
		// date_default_timezone_set('Europe/London');
		if (PHP_SAPI == 'cli') {
			die('This example should only be run from a Web Browser');
		}

		$xml = simplexml_load_file("50828MDF-HQ.xml") or die("Error: Cannot create object");
		/** Include PHPExcel */
		
		$documentName = $_POST['company_name'];
			
		$phpWord = new PhpWord();
		// $documentName = 'Election-to-Decline-Owner';
		// $documentName = 'companyName';
		// $documentName = 'LIMITED-POWER-OF-ATTORNEY-new';
		$documentUpdateName = $documentName.'-updated';
		$document = new TemplateProcessor($documentName.'.docx');
		$templateVariables = $document->getVariables();
		
		$xml_arr = (array)$xml;
		$search_keys = []; 
		$search_val = [];
		foreach ($templateVariables as $key) {
			if(!is_array($xml_arr[$key]) && !empty($xml_arr[$key])) {
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
		}
		
		$document->setValue($search_keys, $search_val);
		ob_clean();	
		$document->saveAs($documentUpdateName.'.docx');

	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Dynamic Word Docs</title>
</head>
<body>
	<form action="" method="post">
		<select name="company_name" id="">
			<option value="">Select Document type to generate it</option>
			<option value="companyName">Company Name</option>
			<option value="Election-to-Decline-Owner">Election to decline owner</option>
			<option value="LIMITED-POWER-OF-ATTORNEY-new">LIMITED POWER OF ATTORNEY</option>
		</select>
		<button type="submit">
			Generate
		</button>
	</form>
	<?php 
		if(!empty($documentUpdateName)){
			if(file_exists($documentUpdateName.'.docx')){
				?>
				<a href="<?php echo $documentUpdateName.'.docx'; ?>">Download</a>
				<?php	
			}
		}
	?>	
</body>
</html>