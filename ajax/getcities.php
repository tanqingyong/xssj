<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
need_login();;
$region_id = intval($_POST['region_id']);
$page_from = trim($_POST['pagefrom']);
if( $page_from != "user" ){
    echo '<option></option>'.Utility::Option(get_cities_by_region_id($region_id)).'';
}else{
	echo ''.Utility::Option(get_cities_by_region_id($region_id)).'';
}
exit;
?>