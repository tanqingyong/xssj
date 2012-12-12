<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

$count = Table::Count('region');
list($pagesize, $offset, $pagestring) = pagestring($count, 20);

$region = DB::LimitQuery('region', array(
	'order' => 'order by id asc',
	'size' => $pagesize,
	'offset' => $offset,
));

echo template('manage_region_region',array('region'=>$region, 'pagestring'=>$pagestring));
