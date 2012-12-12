<?php
require_once(dirname(dirname(__FILE__)) . '/export_excel.php');
need_login();
$sumary_table = array(  'regionname' => '大区',
                        'city' => '下单城市',
						'fromcity' => '来源城市',
						'salesman_id'=>'销售人员编号',
						'salesman'=>'销售人员',
						'biz_id' => '商户ID',
                        'biz_name' => '商户名称',
                        'goods_id' => '产品ID',
                        'goods_name' => '商品名称',
                        'type1' => '一级类目',
                        'second_name' => '二级类目',
                        'cost_price'=>'结算单价',
                        'sale_price' =>'销售单价',
                        'refund_num' => '退款数量',
                        'refund_money' => '退款金额',
						'refund_profile' => '退款毛利',
                        'zhekou' => '折扣',                      
                        'start_date' =>'产品上线时间',
                        'end_date' => '产品下线时间',
                        'jiezhi_time' => '产品截止时间'
                        
                        );
                        //wait for data
                        $sql = "select
gr.date,gr.goods_id,gr.refund_bishu,sum(gr.refund_num) as 'refund_num',gr.refund_money as 'refund_money',gr.refund_profile as 'refund_profile',gr.refund_user,gr.city,gr.goods_name,gr.incity,gr.fromcity,gr.second_id,gr.second_name,gr.start_date,gr.end_date,gr.sale_price,gr.cost_price,gr.zhekou,gr.jiezhi_time,gr.biz_id,gr.biz_name,gr.salesman,gr.salesman_id,region.name as regionname,dim_goods.type1 from 
goods_refund gr left join city on gr.city = city.name 
left join region on region.id = city.area_id 
left join dim_goods on dim_goods.type2_id = gr.second_id where 1=1 ";

                        $date_from = $_GET['date_from'];
                        $date_end = $_GET['date_end'];
                        $goods_id = intval($_GET['goods_id']);
                        if($date_from&&$date_end){

                        	if( $date_from > $date_end ){
                        		Session::Set('error', '开始时间不能比结束时间大！');
                        		redirect( WEB_ROOT . "/manage/sixindex/refund_detail.php");
                        	}
                        	if($date_from==$date_end){
                        		$condition .= " and gr.DATE = '$date_from'";
                        	}else{
                        		$condition .= " and gr.DATE between '$date_from' and '$date_end' ";
                        	}
                        }else if( $date_from && !$date_end ){
                        	$condition .= " and gr.DATE = '$date_from'";
                        }else if( !$date_from && $date_end ){
                        	$condition .= " and gr.DATE = '$date_end'";
                        }else{
                        	$date_from = date('Y-m-01',time());
                        	$date_end = date('Y-m-t',time());
                        	$condition .= " and gr.DATE between '$date_from' and '$date_end' ";
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
                        	$condition .= " and gr.goodsid = $goods_id";
                        }
                        $cate=$_GET['cate'];
                        if($cate){
                        	$condition .= " and dim_goods.type1 ='$cate' ";
                        }

                        $sql .= $condition."  group by gr.goods_id,gr.city ";

                        $excel_name = "退款数据详细".$date_from."-".$date_end;
                        export_excel($sql,$sumary_table,$data_field_array,$excel_name);