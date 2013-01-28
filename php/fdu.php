<?php

class bookItem{
	//位置
	public $place;
	//索书号
	public $i;
	//当前状态,是否被借走
}
	require_once('./lib/simple_html_dom.php');
	
	if(isset($_GET['isbn']))
		$isbn = trim($_GET['isbn']);
	else{
		echo '<h2>you can\'t direct visit this page</h2>author: freefcw@gmail.com';
		exit;
	}
	$href = "http://202.120.227.6/F/?func=find-m&request=$isbn&find_code=ISB&adjacent=N&find_base=FDU01&find_base=FDU09";
	$html = file_get_html($href);
	
	$ret = $html->find('table td.td1[align=left]', 1);

	if ($ret == null)
	{
		$json->ok = 0;
		echo json_encode($json);
		exit;
	}
	
	$json = array();

	$doc_number = trim($ret->plaintext);

	$html = file_get_html("http://202.120.227.6/F/?func=item-global&doc_library=FDU01&doc_number=$doc_number");
	
	$tr = $html->find('table', 8)->find('tr');

	//count number
	$c = 0;
	foreach($tr as $e)
	{
		if($c == 0) {$c++;continue;};


		$o = new bookItem();
		$o->place = $e->children(7)->plaintext;//no &nbsp;
		$index  = $e->children(15)->plaintext;
		$o->s = $e->children(11)->plaintext;
		$json[] = $o;
		$c++;
	}
	
	//print_r($json);
	//exit;
	//header('Content-type: application/pdf');
	$k->index = $index;
	$k->href = $href;
	$k->ok = count($json);
	$k->data = $json;
	echo json_encode($k);
?>