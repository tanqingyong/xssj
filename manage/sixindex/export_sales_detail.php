<?php
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$sumary_table = array(  'regionname' => '大区',
                        'incity' => '上线城市',
						'fromcity' => '来源城市',
						'salesman'=>'销售人员',
						'salesman_id'=>'销售人员ID',
						'biz_id' => '商户ID',
                        'biz_name' => '商户名称',
                        'goodsid' => '产品ID',
                        'goodsname' => '商品名称',
                        'type1' => '一级类目',
                        'firstcategoryname' => '二级类目',
                        'cost_price'=>'结算单价',
                        'sale_price' =>'销售单价',
                        'offorderproductnum' => '销售数量',
                        'offordersale' => '销售金额',
                        'zhekou' => '折扣',
                        'positive_profile' => '正毛利',
                        'lose_profile' => '负毛利',
                        'profile' => '毛利',
                        'begintime' =>'产品上线时间',
                        'endtime' => '产品下线时间',
                        'jiezhi_time' => '产品截止时间'
                        
                      );
//wait for data
$sql = "select 
gc.id,gc.DATE,gc.goodsid,gc.goodsname,gc.incity,gc.fromcity,gc.salesman,gc.salesman_id
,gc.firstcategoryid,gc.firstcategoryname,gc.begintime,gc.endtime,gc.ordernum
,gc.orderproductnum,gc.ordersale,gc.addupordersale,gc.orderusernum,gc.offordernum
,gc.offaddupordersale,gc.offorderusernum,gc.PV,gc.UV,gc.IP,gc.sale_price
,gc.cost_price,gc.zhekou,gc.jiezhi_time,gc.biz_id,gc.biz_name
,sum(gc.offorderproductnum) as offorderproductnum
,round(sum(gc.offordersale),2) as offordersale
,round(sum(gc.positive_profile),2) as positive_profile
,round(sum(gc.lose_profile),2) as lose_profile
,round(sum(gc.profile),2) as profile,
gc.incity as incity, gc.fromcity  as fromcity,region.name as regionname,dim_goods.type1 from goods_city_day gc  
left join city on gc.incity  = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = gc.firstcategoryid where sale_price>0 ";
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
        $condition .= " and gc.DATE = '$date_from'";
    }else{
        $condition .= " and gc.DATE between '$date_from' and '$date_end' ";
    }
}else if( $date_from && !$date_end ){
    $condition .= " and gc.DATE = '$date_from'";
}else if( !$date_from && $date_end ){
    $condition .= " and gc.DATE = '$date_end'";
}else{
    $date_from = date('Y-m-01',time());
    $date_end = date('Y-m-t',time());
    $condition .= " and gc.DATE between '$date_from' and '$date_end' ";
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
	$condition .= " and gc.goodsid = $goods_id";
}
$cate=$_GET['cate'];
if($cate){
	$condition .= " and dim_goods.type1 ='$cate' ";
}

$sql .= $condition;
$sql .= " group by gc.goodsid,gc.incity ";
$excel_name = "销售数据详细".$date_from."-".$date_end;
 export_excel($sql,$sumary_table,$data_field_array,$excel_name);