<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

is_manager();

/* build condition */
$conditions = array();

/* menu grade1 and menu array */
$menu_grade1_array = array();
$menu_array = array();

$sql = "SELECT u.username as username,ml.ip as ip,ml.action_time as action_time,m.parent_id as parent_id,m.id as menu_id
		FROM menu_log as ml 
	 	INNER JOIN menu as m ON ml.menu_id = m.id
	 	LEFT JOIN users as u ON u.id = ml.user_id WHERE 1=1 ";

$sql_count = "SELECT count(1) as count
			FROM menu_log as ml 
		 	INNER JOIN menu as m ON ml.menu_id = m.id
		 	LEFT JOIN users as u ON u.id = ml.user_id WHERE 1=1 ";

#menu of grade 1
$sql_menu_grade1 = "SELECT id,menu_name FROM menu WHERE menu_grade = 1";

#menu
$sql_menu = "SELECT id,menu_name FROM menu";


//menu of grade 1 query condition
$menu_parent_id = intval(trim($_GET['menu_parent_id']));
if($menu_parent_id > 0){
	$conditions['menu_parent_id'] = $menu_parent_id;
	$sql .=" AND m.parent_id=$menu_parent_id ";
	$sql_count .=" AND m.parent_id=$menu_parent_id ";
}

//menu of grade 2 query condition
$menu_id = intval(trim($_GET['menu_id']));
if($menu_id > 0){
	$conditions['menu_id'] = $menu_id;
	$sql .=" AND  ml.menu_id=$menu_id ";
	$sql_count .=" AND  ml.menu_id=$menu_id ";
}

// user id query conditon
$user_name = str_replace("'","‘",trim($_GET['user_name']));
//if($user_name != "" && $user_name != null)
if(!empty($user_name)){
	$conditions['user_name'] = $user_name;
	$sql .= " AND u.username='$user_name' ";
	$sql_count .= " AND u.username='$user_name' ";
}

$result = DB::GetQueryResult($sql_count,true);
$count = $result['count'];
$page_size = $_GET['pagesize']?intval($_GET['pagesize']):10;
list($pagesize, $offset, $pagestring) = pagestring($count, $page_size);

$sql .=" ORDER BY ml.action_time DESC";
$sql .=" limit $offset,$pagesize";
$menu_logs = DB::GetQueryResult($sql,false);


$menu_grade1 = DB::GetQueryResult($sql_menu_grade1,false);
foreach($menu_grade1 as $menu_g1){
	$menu_grade1_array[$menu_g1['id']] = $menu_g1['menu_name'];
}

$menus = DB::GetQueryResult($sql_menu,false);
foreach($menus as $menu){
	$menu_array[$menu['id']] = $menu['menu_name'];
}

echo  template('manage_menulog_search',array('menu_logs'=>$menu_logs,'pagestring'=>$pagestring,'menu_grade1_array'=>$menu_grade1_array,'menu_array'=>$menu_array,'conditions'=>$conditions));
?>