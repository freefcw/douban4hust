<?php
class bookItem{
	//位置
	public $place;
	//索书号
	public $index;
	//当前状态,是否被借走
	public $status;
}

$html = file_get_html('http://202.114.9.29/search*chx/i?SEARCH='.$isbn);

$find = $html->find('table[class=bibItems]', 0);

$result = new stdClass();
if ($find != null)
{
	$data = array();
	$total = 0;
	foreach ($find->children as $item) {
		$total++;
		if ($total == 1) {
			continue;
		}
		$object = new bookItem();
		$object->place = trim(str_replace('&nbsp;', ' ', $item->children(0)->plaintext));
		$object->index = $item->children(1)->children(1)->innertext;
		$str = str_replace('&nbsp', ' ', $item->children(2)->plaintext);
		$str = str_replace(';', '', $str);
		$object->status = trim($str);
		array_push($data, $object);
	}
	$result->ok = $total - 1;
	$result->data = $data;
} else {
	$result->ok = -1;
}
echo json_encode($result);