<?php
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$sumary_table = array(  'regionname' => '大区',
                        'city' => '下单城市',
						'fromcity' => '来源城市',
						'salesman'=>'销售人员',
						'biz_id' => '商户ID',
                        'biz_name' => '商户名称',
                        'goods_id' => '产品ID',
                        'goods_name' => '商品名称',
                        'type1' => '一级类目',
                        'second_name' => '二级类目',
                        'cost_price'=>'结算单价',
                        'sale_price' =>'销售单价',
                        'verify_num' => '验证数量',
                        'verify_money' => '验证金额',
						'verify_profile' => '验证毛利',
                        'zhekou' => '折扣',                      
                        'start_date' =>'产品上线时间',
                        'end_date' => '产品下线时间',
                        'jiezhi_time' => '产品截止时间'
                        
                      );
//wait for data
$sql = "select 
gv.date,gv.goods_id,sum(gv.verify_num) as 'verify_num',sum(gv.verify_money) as 'verify_money',sum(gv.verify_profile) as 'verify_profile',
gv.city,gv.goods_name,gv.incity,gv.fromcity,gv.second_id,gv.second_name,gv.start_date,gv.end_date,
sum(gv.order_num) as 'order_num',sum(gv.order_goods_num) as 'order_goods_num',sum(gv.order_money) as 'order_money',sum(gv.order_user) as 'order_user',
sum(gv.suc_order) as 'suc_order',sum(gv.suc_goods_num) as 'suc_goods_num',
sum(gv.suc_money) as 'suc_money',sum(gv.suc_user) as 'suc_user',gv.sale_price,gv.cost_price,sum(gv.profile) as 'profile',
sum(gv.positive_profile) as 'positive_profile',sum(gv.lose_profile) as 'lose_profile',gv.zhekou,gv.jiezhi_time,gv.biz_id,gv.biz_name,gv.salesman,gv.salesman_id,

region.name as regionname,dim_goods.type1 from 
goods_verify gv left join city on gv.city = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = gv.second_id where 1=1 ";
$date_from = $_GET['date_from'];
$date_end = $_GET['date_end'];
$goods_id = intval($_GET['goods_id']);
if($date_from&&$date_end){
//    if(date('m',strtotime($date_from))!=date('m',strtotime($date_end))||date('Y',strtotime($date_from))!=date('Y',strtotime($date_end))){
//        Session::Set('error', '时间段不能跨月！');
//        redirect( WEB_ROOT . "/manage/sixindex/index_completed.php");
//    }
    if( $date_from > $date_end ){
        Session::Set('error', '开始时间不能比结束时间大！');
        redirect( WEB_ROOT . "/manage/sixindex/sales_detail.php");
    }
    if($date_from==$date_end){
        $condition .= " and gv.DATE = '$date_from'";
    }else{
        $condition .= " and gv.DATE between '$date_from' and '$date_end' ";
    }
}else if( $date_from && !$date_end ){
    $condition .= " and gv.DATE = '$date_from'";
}else if( !$date_from && $date_end ){
    $condition .= " and gv.DATE = '$date_end'";
}else{
    $date_from = date('Y-m-01',time());
    $date_end = date('Y-m-t',time());
    $condition .= " and gv.DATE between '$date_from' and '$date_end' ";
}

//权限处理
if($_SESSION['user_grade']>2){
    $filter_region = intval($_GET['region']);
    $filter_city = intval($_GET['city']);
    if($filter_city){
        $condition .= " and city.id = $filter_city ";
    }else if($filter_region){
        $cities = array();
        $cities = get_cities_by_region_id($filter_region);
        $cities = array_flip($cities);
        $city_str = '('.implode(',',$cities).')';
        $condition .= " and city.id in $city_str ";
    }
}else if($_SESSION['user_grade']==2){
    $filter_city = intval($_GET['city']);
    if($filter_city){
        $condition .= " and city.id = $filter_city ";
    }else{
        $cities = array();
        $cities = get_cities_by_region_id($_SESSION['area_id']);
        $cities = array_flip($cities);
        $city_str = '('.implode(',',$cities).')';
        $condition .= " and city.id in $city_str ";
    }
}else{
    $condition .= " and city.id = {$_SESSION['city_id']} ";
}
if($goods_id){
	$condition .= " and gv.goodsid = $goods_id";
}
$cate=$_GET['cate'];
if($cate){
	$condition .= " and dim_goods.type1 ='$cate' ";
}

$sql .= $condition."  group by gv.goods_id,gv.city ";

$excel_name = "验证数据详细".$date_from."-".$date_end;
  export_excel($sql,$sumary_table,$data_field_array,$excel_name);