<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$name = trim(strval($_POST['name']));
$value = trim(strval($_POST['value']));
if($name=="username"){
	$slength = strlen($value);
	$u = Table::Fetch('users', $value, $name);
	if ( $u ) {echo 0;exit;}
	else {echo 1;exit;}
}
?>