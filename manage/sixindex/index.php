<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
redirect( WEB_ROOT . '/manage/sixindex/index_forecast.php');
?>