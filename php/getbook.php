<?php
	$isbn = $_GET['isbn'];
	// NO need test

	if($isbn == null) {
		echo 'you cannot direct visit this page,any problem contact freefcw#gmail.com';
		exit;
	}

	require_once('./common.php')
	require_once('./lib/simple_html_dom.php');
	
	$html = file_get_html('http://202.114.9.29/search*chx/i?SEARCH='.$isbn);
	
	$ret = $html->find('tr[class=bibItemsEntry]');

	if ($ret != null)
	{
		$data = array();
		$c = 0;
		foreach ($ret->children as $item) {
			if ($c === 0)
			{
				$c++;
				continue;
			}
		}

	}

	if ($ret == null)
	{
		$json->ok = -1;
		echo json_encode($json);
		exit;
	}
	
	$json = array();

	//count number
	$c = 0;
	foreach($ret->children as $e)
	{
		if($c == 0) {$c++;continue;};
		$o = new bookItem();
		$o->place = trim(str_replace('&nbsp;', ' ', $e->children(0)->plaintext));//no &nbsp;
//		var_dump(trim(html_entity_decode($e->children(0)->plaintext)));
		$o->i = $e->children(1)->children(1)->innertext;
		//$o->index = '<a href="http://202.114.9.29'.$t->href.'">'.$t->innertext.'</a>';
		$o->s = trim(str_replace('&nbsp;', ' ', $e->children(2)->plaintext));
		$json[] = $o;
		$c++;
	}
	
	//print_r($json);
	//exit;
	//header('Content-type: application/pdf');
	$k->ok = $c-1;
	$k->data = $json;
//	var_dump($k);exit;
	echo json_encode($k);
?>
