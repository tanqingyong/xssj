<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

$area_id = intval($_GET['id']);
$name = trim(strval($_GET['name']));
if(!empty($area_id))
	$condition = array('area_id'=>$area_id);
if(!empty($name))
	$condition = array('name'=>$name);

$count = Table::Count('city',$condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 20);

$city = DB::LimitQuery('city', array(
    'condition'=>$condition,
	'order' => 'order by area_id asc',
	'size' => $pagesize,
	'offset' => $offset,
));

echo template('manage_region_city',array('area_id'=>$area_id,'city'=>$city, 'pagestring'=>$pagestring, 'name'=>$name));
