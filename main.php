<?php
    require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
	use \PhpOffice\PhpWord\PhpWord;
	use \PhpOffice\PhpWord\TemplateProcessor;
	require_once __DIR__.DIRECTORY_SEPARATOR.'ExtTemplateProcessor.php';
	use DocxMerge\DocxMerge;
	$base_doc_dir = __DIR__.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR;
	$base_doc_output_dir = __DIR__.DIRECTORY_SEPARATOR.'updated'.DIRECTORY_SEPARATOR;
	@chmod($base_doc_output_dir, 0777);
    $base_tmp_dir =  __DIR__.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;

    // if(isset($_POST['document_select']))
    // {
    //     $path = "../rescue-development/docs".DIRECTORY_SEPARATOR.$_POST['selected_file'].DIRECTORY_SEPARATOR;
    //     echo "<ul>";
    //     if ($handle = opendir($path)) {
            
    //         while (false !== ($file = readdir($handle)))
    //         {
    //             if(preg_match('/.*(\.doc)/', $file))
    //             {
    //                 $final_path = "docs".DIRECTORY_SEPARATOR.$_POST['selected_file'].DIRECTORY_SEPARATOR.$file;
    //                 $thelist .= '<li><a href="'.$final_path.'" title="'.$final_path.'">'.$file.'</a></li>'."<br/>";
    //             }
    //         }
            
            
    //         echo $thelist .= '<a href="'.$final_path.'">'.$file.'</a>'."<br/>";
    //         echo "</ul>";
            
    //         closedir($handle);
    //     }
    // }
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
		<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="bower_components/font-awesome.min.css">
		<link rel="stylesheet" href="bower_components/font-awesome.css">
		<link rel="stylesheet" href="bower_components/jstree-bootstrap-theme/dist/themes/proton/style.min.css">
		<link rel="stylesheet" type="text/css" href="bower_components/sweetalert/sweetalert2.min.css">

	</head>
    <body>
        <div class="">
            <form action="index-new.php" method="get" name="docform" enctype="multipart/form-data">
                <input type="hidden" name="dir" id="path" value="test1">
                <button type="submit" class="btn btn-primary btn-lg btn-block" id="send_to_word" name="document_select" value="Upload">Send to word</button>
            </form>
        </div>
    </body>
    <script>
        
    </script>
</html>