<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

/* build condition */
$condition = array();
$sql= "select users.*,IF(users.online = 1 AND (UNIX_TIMESTAMP(NOW())-ml.last_operate_time) < 1800,1,0) AS online_status,region.name as region_name,city.name as city_name from users left join city on city.id = users.city_id
	   LEFT JOIN (SELECT user_id,max(action_time) as last_operate_time FROM menu_log GROUP BY user_id) AS ml ON users.id = ml.user_id
       left join region on region.id = users.area_id where 1=1 ";
$sql_count = "select count(1) as count from users left join city on city.id = users.city_id
	   LEFT JOIN (SELECT user_id,max(action_time) as last_operate_time FROM menu_log GROUP BY user_id) AS ml ON users.id = ml.user_id
       left join region on region.id = users.area_id where 1=1 ";
//name query condition
$filter_name = trim($_GET['filter_name']);
if(strlen($filter_name)){
	$sql .=" and users.username='$filter_name' ";
	$sql_count .=" and users.username='$filter_name' ";
}

//grade query condition
$filter_grade = (int)$_GET['filter_grade'];
if($filter_grade){
	$sql .= " and users.grade = $filter_grade ";
	$sql_count .= " and users.grade = $filter_grade ";
}
$filter_region = intval($_GET['filter_region']);
if($filter_region){
	$sql .= " and users.area_id = $filter_region";
	$sql_count .= " and users.area_id = $filter_region";
}
$filter_online_status = (int)$_GET['filter_online_status'];
if($filter_online_status){
	$filter_online = 1;
	if($filter_online_status == 2){
		$filter_online = 0;
	}
	$sql .= " and users.online = $filter_online ";
	$sql_count .= " and users.online = $filter_online ";
}

$result = DB::GetQueryResult($sql_count,true);
$count = $result['count'];
list($pagesize, $offset, $pagestring) = pagestring($count, 10);

$sql .=" limit $offset,$pagesize";
$users = DB::GetQueryResult($sql,false);
//var_dump($users);
echo  template('manage_user_manage',array('users'=>$users,'pagestring'=>$pagestring,'filter_grade'=>$filter_grade,'filter_online_status'=>$filter_online_status,'filter_name'=>$filter_name, 'filter_region' => $filter_region));
?>