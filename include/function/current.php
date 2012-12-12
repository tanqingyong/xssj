<?php
//require_once(dirname(__FILE__).Des);

function current_backend() {
	global $INI;
	$a = array(
			'/manage/dataanalysis/index.php' => '流量数据',
			'/manage/sixindex/index.php' => '六大指标报表',
            '/manage/moneydetail/index.php' => '销售额毛利明细',
			'/manage/customercomplaints/index.php' => '客服投诉报表',
			'/manage/user/index.php' => '用户管理',
			);
	
	if(is_manager()){
		$a['/manage/region/index.php'] =  '大区配置';
	}
	$r = $_SERVER['REQUEST_URI'];
	if (preg_match('#/manage/(\w+)/#',$r, $m)) {
		$l = "/manage/{$m[1]}/index.php";
	} 
	return current_link($l, $a);
}



function current_link($link, $links, $span=false) {
	$html = '';
	$span = $span ? '<span></span>' : '';
	foreach($links AS $l=>$n) {
		if (trim($l,'/')==trim($link,'/')) {
			$html .= "<li class=\"current\"><a href=\"{$l}\">{$n}</a>{$span}</li>";
		}
		else $html .= "<li><a href=\"{$l}\">{$n}</a>{$span}</li>";
	}
	return $html;
}

/* manage current */





function mcurrent_user($selector=null) {
	$a = array('/manage/user/update_password.php'=>'修改密码');
	if(is_manager()){
		$a['/manage/user/create_user.php'] = '创建新用户';
		$a['/manage/user/manage.php'] =  '用户管理';
		$a['/manage/user/menulog_search.php'] =  '菜单访问日志';
	}
	
	$l = "/manage/user/{$selector}.php";
	return current_link($l,$a,true);
}

function mcurrent_dataanalysis($selector=null) {
    $a = array('/manage/dataanalysis/summary.php'=>'流量总计',
               '/manage/dataanalysis/detail.php'=>'流量详情查看'
        );
    $l = "/manage/dataanalysis/{$selector}.php";
    return current_link($l,$a,true);
}

function mcurrent_customercomplaints($selector=null) {
	$a = array('/manage/customercomplaints/complaint_details.php'=>'客服投诉统计'			
	);
	$l = "/manage/dataanalysis/{$selector}.php";
	return current_link($l,$a,true);
}

function mcurrent_sixindex($selector=null) {
    $a = array('/manage/sixindex/index_forecast.php'=>'六大指标预测报表',
               '/manage/sixindex/sales_detail.php'=>'产品销售明细报表',
    			'/manage/sixindex/verify_detail.php'=>'产品验证明细报表',
    			'/manage/sixindex/refund_detail.php'=>'产品退款明细报表',
    		   '/manage/sixindex/index_completed.php'=>'六大指标完成报表',
        );
    $l = "/manage/sixindex/{$selector}.php";
    return current_link($l,$a,true);
}
function mcurrent_moneydetail($selector=null) {
	if($_SESSION['user_grade']>2){
		 $a = array( '/manage/moneydetail/nationwide_day.php' => '全国日累计',
    		    '/manage/moneydetail/region_day.php' => '大区日累计',
    			'/manage/moneydetail/city_day.php' => '城市日累计',
			    '/manage/moneydetail/month_sale_data_country.php' => '全国旬月',
			    '/manage/moneydetail/month_sale_data_region.php' => '大区旬月',
			    '/manage/moneydetail/month_sale_data_city.php' => '城市旬月'
    	      );
	}else if($_SESSION['user_grade']==2){
		 $a = array( 
    		    '/manage/moneydetail/region_day.php' => '大区日累计',
    			'/manage/moneydetail/city_day.php' => '城市日累计',
			    '/manage/moneydetail/month_sale_data_region.php' => '大区旬月',
			    '/manage/moneydetail/month_sale_data_city.php' => '城市旬月'
              );
	}else{
	    $a = array(
    			'/manage/moneydetail/city_day.php' => '城市日累计',
			    '/manage/moneydetail/month_sale_data_city.php' => '城市旬月'
    		  );
	}
    $l = "/manage/moneydetail/{$selector}.php";
    return current_link($l,$a,true);
}
function mcurrent_region($selector=null) {
    $a = array('/manage/region/region_list.php'=>'大区列表','/manage/region/city_list.php'=>'城市列表');
    $l = "/manage/region/{$selector}.php";
    return current_link($l,$a,true);
}
?>

