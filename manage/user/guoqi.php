<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_login();

$int_user_id = Session::Get('user_id');
$arr_user = Table::FetchForce("users",$int_user_id);
$username=$arr_user['username'];
$usetime=$arr_user['create_time'];


$sql= DB::Update ( 'users', $int_user_id, array ('create_time'=>time(),'state'=>0 ) );



echo template('manage_guoqi',array('username'=>$username,'usetime'=>$usetime));


  Session::Set('user_id',"");
?>
