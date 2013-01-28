<?php

function makeisbn($isbn)
{
	$s = substr($isbn, 3, -1);
	//11除法
	//refer : 国际标准书号的知识　http://www.enjoykorea.net/12/viewspace-26689.html
	//refer :　ISBN出版者号与出版社对应表：http://joeey.blogbus.com/logs/5368842.html
	$yushu = 11-($s{0}*10+$s{1}*9+$s{2}*8+$s{3}*7+$s{4}*6+$s{5}*5+$s{6}*4+$s{7}*3+$s{8}*2)%11;
	switch($yushu){
		case 10 : $s.= 'X';
			break;
		case 11 : $s.= '0';
			break;
		default : $s.= $yushu;
	}
	$a[] = $isbn;
	$a[] = $s;
	return $a;
}
class bookItem{
	//位置
	public $place;
	//当前状态,是否被借走
	public $s;
}

	if(isset($_GET['isbn']))
		$isbn = trim($_GET['isbn']);
	else echo '<h2>you can\'t direct visit this page</h2>author: freefcw@gmail.com';
	//if($isbn = null) exit;
	//$isbn = '7-208-05160-7';
	//$isbn = '7-201-00006-3';
	$isbn = makeisbn($isbn);

	require_once './lib/simple_html_dom.php';
	$html = new simple_html_dom();

	$flag = false;
	foreach($isbn as $i)
	{
		$url = "http://202.114.34.15/opac/openlink.php?strText=$i&strSearchType=isbn";

		$html->load_file($url);

		//判断是是否存在
		//echo $i,'<br/>';
		if(strstr($html->find('#searchmain dt b', 0)->plaintext, '没有'))
			continue;
		else
		{
			$flag = true;
			$ccnu->index = substr(html_entity_decode($html->find('#bookdetail dt', 0)->find('text', 1)->plaintext), 6);
			$ccnu->href = 'http://202.114.34.15/opac/'.$html->find('#bookdetail dt b a', 0)->href;
			break;
		}
	}
	//尝试完毕各种isbn,仍然没有则表示找不到
	if(!$flag)
	{
		$cnuu->ok = -1;
		echo json_encode($ccnu);
		exit;
	}
	//$ccnu->index = $html->find('table', 1)->children(1)->children(5)->plaintext;

	$html->load_file($ccnu->href);

	$c = 0;
	foreach($html->find('table', 1)->children() as $e)
	{
		$c++;
		if($c == 1 )  continue;
		if($e->tag == 'script') break;
		$o = new bookItem();
		$o->place = $e->children(3)->plaintext;
		$o->s = $e->children(4)->plaintext;
		$data[] = $o;
	}
	$ccnu->ok = count($data);
	$ccnu->data = $data;
	echo json_encode($ccnu);
?>