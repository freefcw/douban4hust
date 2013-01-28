<?php

class bookItem{
	//位置
	public $place;
	//当前状态,是否被借走
	public $status;
	public $index;
}
function get_sid($url)
{
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HEADER, 0);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	$page = curl_exec($curl);
	curl_close($curl);
	$str_start = 'http://opac.lib.whu.edu.cn:80/F/';
	$str_end = '?RN';
	$start = stripos($page, $str_start)+strlen($str_start);
	$length = stripos($page, $str_end) - $start;

	return substr($page, $start, $length);
}



function fetch_page($url)
{
	for ($i = 0; $i < 3; $i++)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$page = curl_exec($curl);
		curl_close($curl);

		if ($page != false) return $page;
	}
	return false;
}

function fetch_detail($url)
{
	for ($i = 0; $i < 3; $i++)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$page = curl_exec($curl);


		$start = stripos($page, 'url=') + strlen('url=');
		$length = stripos($page, 'GUEST') + strlen('GUEST') - $start;
		$newurl = substr($page, $start);
		$newurl = substr($page, $start, $length);

		curl_setopt($curl, CURLOPT_URL, $newurl);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HEADER, 0);

		$page = curl_exec($curl);
		curl_close($curl);
		if ($page != false) return $page;
	}
	return false;
}

function not_found()
{
	$result = new stdClass();
	$result->ok = -1;
	echo json_encode($result);
	exit();
}

	$url = 'http://opac.lib.whu.edu.cn/F?RN='.mt_rand();
	$sid = get_sid($url);

	$url = "http://opac.lib.whu.edu.cn/F/{$sid}?func=find-b&find_code=ISB&request={$isbn}&pds_handle=GUEST";

	$page = fetch_page($url);

	if ($page == false) not_found();

	$html = str_get_html($page);
	$item = $html->find('input[name=doc_number]',0);
	$doc_number = $item->value;

	$result = new stdClass();
	$detail_url = "http://opac.lib.whu.edu.cn/F/?func=item-global&doc_library=WHU01&doc_number={$doc_number}&pds_handle=GUEST";

	$newpage = fetch_detail($detail_url);
	if ($newpage == false) not_found();


	$html->load($newpage);

	$total = 0;
	$data = array();
	foreach ($html->find('table[width=99%]', 3)->children as $item)
	{

		if($item->tag != 'tr')	continue;
		$total++;
		if ($total == 1) continue;

		$object = new bookItem();
		$td = $item->find('td');

		$status = $td[2]->plaintext. $td[3]->plaintext;
		$index = $td[5]->plaintext;
		$place = $td[4]->plaintext;

		// array_push($data, (object)array('place'=>mb_convert_encoding($place, 'utf-8', 'gb2312'), 'status'=>mb_convert_encoding($status, 'utf-8', 'gb2312'), 'index'=>mb_convert_encoding($index, 'utf-8', 'gb2312')));
		array_push($data, (object)array('place'=>$place, 'status'=>$status, 'index'=>$index));
	}
	$result = new stdClass();
	$result->ok = count($data);
	$result->data = $data;
	$result->href = $detail_url;

	echo json_encode($result);

?>