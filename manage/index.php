<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
redirect( WEB_ROOT . '/manage/dataanalysis/summary.php');
?>