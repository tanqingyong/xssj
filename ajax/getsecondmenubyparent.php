<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
need_login();;
$parent_id = intval($_GET['parent_id']);
$menulist_html = '';
if( $parent_id > 0){
	$menulist_html .= '<select name="menu_id" class="se_ect3">';
	$menulist_html .= '<option></option>'.Utility::Option(get_second_menu_by_parent_id($parent_id)).'';
	$menulist_html .= '</select>&nbsp;';
    echo $menulist_html;
}else{
	$menulist_html .= '<select name="menu_id" class="se_ect3">';
	$menulist_html .= '<option></option>';
	$menulist_html .= '</select>&nbsp;';
	echo $menulist_html;
}
?>