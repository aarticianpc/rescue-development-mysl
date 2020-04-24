<?php
// $str = 'The following table contains a few values that can be edited by the PHPWord_Template class. You just can edit single-line of text elements. The format and the rest of the document stay untouched.!@@!companyName!@@!.You just have to use the PHPWord search pattern like ${myReplacedValue}.Data 1Value 1:${CompanyName} Value 2:${Value2}Value 3:${Value3}Value 4:${Value4}Value 5:${Value5}Data 2Value 6:${Value6}Value 7:${Value7}Value 8:${Value8}Value 9:${Value9}Value 10:${Value10}Today is ${weekday} and it is ${time}. Thanks for reading.';

// $replace = 'Closeline Settlements';
// $search = '${companyName}';

// print_r($str);
// echo '<br/>';
// $str = str_ireplace($search, $replace, $str);
// print_r($str);

// var_dump(extension_loaded('zip'));
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<script type="text/javascript" src="bower_components/jquery/dist/jquery.min.js"></script>
	<script type="text/javascript" src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="bower_components/jstree/dist/jstree.min.js"></script>
	<script type="text/javascript" src="bower_components/core.js"></script>
	<script type="text/javascript" src="bower_components/dropzone/dropzone.min.js"></script>
	<script type="text/javascript" src="bower_components/dropzone/dropzone-amd-module.min.js"></script>

	<script type="text/javascript" src="bower_components/sweetalert/sweetalert2.min.js"></script>

	<link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="bower_components/jstree/dist/themes/default/style.min.css">
	<link rel="stylesheet" type="text/css" href="bower_components/sweetalert/sweetalert2.min.css"><link rel="stylesheet" type="text/css" href="bower_components/dropzone/basic.min.css">
	<link rel="stylesheet" type="text/css" href="bower_components/dropzone/dropzone.min.css">
</head>
<body>
	<div id="tree">
		<ul>
			<li>Node 1</li>
			<li class="jstree-closed">Node 2</li>
		</ul>
	</div>
</body>
</html>


<script type="text/javascript">	
$('#tree').jstree({
  'core' : {
    'data' : {
      'url' : 'ajax_nodes.html',
      'data' : function (node) {
      	console.log(node);
        return { 'id' : node.id };
      }
    }
  }
});
</script>


