<?php
	require_once 'PHPWord.php';
	// error_reporting(E_ALL);
	// ini_set('display_errors', TRUE);
	// ini_set('display_startup_errors', TRUE);
	// date_default_timezone_set('Europe/London');
	if (PHP_SAPI == 'cli')
		die('This example should only be run from a Web Browser');

	$xml = simplexml_load_file("50828MDF-HQ.xml") or die("Error: Cannot create object");
	/** Include PHPExcel */
	
	$PHPWord = new PHPWord();
	// $companyNameDoc = $PHPWord->loadTemplate('companyName.docx');
	// $electionToDeclineDoc = $PHPWord->loadTemplate('Election-to-Decline-Owner-new.docx');
	$limitedPowerOfAttorneyDoc = $PHPWord->loadTemplate('LIMITED-POWER-OF-ATTORNEY-new.docx');

	$xml_arr = (array)$xml;

	foreach ($xml_arr as $key => $value) {
		if(!is_array($value)) {
			// $search_replace[$key] = $value;
			// $companyNameDoc->setValue($key, $value);
			// $electionToDeclineDoc->setValue($key, $value);
			if($value != strip_tags($value)) {

			} else {
				$limitedPowerOfAttorneyDoc->setValue($key, $value);
			}
		}
	}
	// $limitedPowerOfAttorneyDoc->setValueAdvanced($search_replace);
	
	// $companyNameDoc->save('companyName-updated.docx');
	// $electionToDeclineDoc->save('Election-to-Decline-Owner-updated.docx');
	$limitedPowerOfAttorneyDoc->save('LIMITED-POWER-OF-ATTORNEY-updated.docx');
?>
