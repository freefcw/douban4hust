<?php
	if (isset($_GET['isbn']) AND isset($_GET['school'])) {
		$isbn = $_GET['isbn'];
	} else {
		echo 'you cannot direct visit this page,any problem contact freefcw[#]gmail.com';
		exit;
	}
	$school = $_GET['school'];
	$all_school = array('hust', 'fdu', 'ccnu', 'whu');
	$filename = $school. '.php';
	if (array_search($school, $all_school) == false AND !file_exists($filename))
	{
		echo 'We Don\'t support your school, please contact freefcw[#]gmail.com';
		exit;
	}
	
	require_once('./lib/simple_html_dom.php');
	require_once($filename);
?>