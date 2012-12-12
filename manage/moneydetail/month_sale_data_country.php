<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_login();
$sql = "select month,sum(month_goodsnum) as month_goodsnum,sum(month_money) as month_money,
sum(month_profile) as month_profile,sum(early_goodsnum) as early_goodsnum,sum(early_money) as early_money,
sum(early_profile) as early_profile,sum(mid_goodsnum) as mid_goodsnum,sum(mid_money) as mid_money,
sum(mid_profile) as mid_profile,sum(end_goodsnum) as end_goodsnum,sum(end_money) as end_money,
sum(end_profile) as end_profile
 from month_sale_data msd  where 1=1 ";

$sql_count = "select month from month_sale_data msd  where 1=1 ";
$monthstart = $_GET['monthstart'];
$monthend = $_GET['monthend'];


if($monthstart&&$monthend){

    if($monthstart==$monthend){
        $condition .= " and msd.month = '$monthstart'";
    }else{
        $condition .= " and msd.month between '$monthstart' and '$monthend' ";
    }
}else if( $monthstart && !$monthend ){
    $condition .= " and msd.month = '$monthstart'";
}else if( !$monthstart && $monthend ){
    $condition .= " and msd.month = '$monthend'";
}else{
    $monthstart = date('Y-01',time());
    $monthend = date('Y-m',time());
    $condition .= " and msd.month between '$monthstart' and '$monthend' ";
}

//权限处理
if($_SESSION['user_grade']>2){


$sql .= $condition;
$sql_count .= $condition." group by msd.month;";

$result = DB::Query($sql_count);
$count = mysql_num_rows($result);
$page_size = $_GET['pagesize']?intval($_GET['pagesize']):30;
list($pagesize, $offset, $pagestring) = pagestring($count, $page_size);
$sql .=" group by msd.month order by msd.month limit $offset,$pagesize";
$datas = DB::GetQueryResult($sql,false);
}

echo template('manage_month_country',array('datas'=>$datas, 'pagestring'=>$pagestring, 'monthstart'=>$monthstart,'monthend'=>$monthend,'page_size'=>$page_size));
?>