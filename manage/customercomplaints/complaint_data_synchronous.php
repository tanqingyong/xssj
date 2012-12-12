<?php
require_once (dirname ( dirname ( dirname ( __FILE__ ) ) ) . '/app.php');

need_login ();

// 连接窝窝后台获取数据
$link_wwback = mysql_connect ( "10.8.210.199", "guochengdong", 'guoguo87&Das' ) or die ( "Could not connect: " . mysql_error () );
mysql_select_db ( 'wowotuan', $link_wwback ) or die ( 'Can\'t use wowotuan : ' . mysql_error () );
mysql_query ( "set names utf8", $link_wwback );
// 获取订单数据
$query_wwback_ordergoods = "SELECT jg.goods_id,jg.goods_sname,og.goods_number,jg.shop_price,og.order_id,jc.city_name
 FROM jeehe_goods jg , jeehe_city jc ,jeehe_order_goods og where jg.goods_id = og.goods_id  AND jg.city_id = jc.id";
// 获取退款数据
$query_wwback_refundment = "SELECT  m.order_id ,case  m.is_chaoqi WHEN 0 THEN '正常退款'
                                      when 1 THEN  '用户自助退款'
                                      WHEN 2 THEN  '强制退款'
                                      WHEN 3 THEN  '线下退款' END refund_type,sum(m.refundment_num) as refundment_num
 FROM jeehe_refundment m  where 1 = 1 ";
// 查询投诉表订单ID
$query_consult = "select distinct ordernum from ww_consult  WHERE 
     create_time >= FROM_UNIXTIME(UNIX_TIMESTAMP(SYSDATE())-3600*24,'%Y-%m-%d 00:00:00') ";
$result_consultnum = DB::GetQueryResult ( $query_consult, false );
// 删除订单表和退款表 里面 已有订单ID 重新插入该订单关联信息
// 只获取昨天0点以后的数据
$delete_order_goods = "DELETE from jeehe_order_goods where order_id 
      in(SELECT ORDERNUM FROM ww_consult WHERE 
     create_time >= FROM_UNIXTIME(UNIX_TIMESTAMP(SYSDATE())-3600*24,'%Y-%m-%d 00:00:00'))";
DB::Query ( $delete_order_goods );

$delete_refundment = "DELETE from jeehe_refundment where order_id 
      in(SELECT ORDERNUM FROM ww_consult WHERE 
     create_time >= FROM_UNIXTIME(UNIX_TIMESTAMP(SYSDATE())-3600*24,'%Y-%m-%d 00:00:00'))";
DB::Query ( $delete_refundment );

// 插入订单表相关数据
$insert_order_goods = "insert into jeehe_order_goods(goods_id,goods_sname,goods_number,shop_price
,order_id,city_name) values";
// 插入退款表相关数据
$insert_refundment_goods = "insert into jeehe_refundment(order_id,refund_type,refund_num) values";

// 查询订单信息时将产品ID存入变量中供后面计算总销售数量用
$new_goods_id = array ();
// 执行SQL拼接插入订单相关数据
foreach ( $result_consultnum as $result_con ) {
	$query_ordergoods = "$query_wwback_ordergoods and og.order_id = " . $result_con ['ordernum'];
	$query_refundment = "$query_wwback_refundment and m.order_id = " . $result_con ['ordernum'] . " group by m.order_id";
	$result_wwback = mysql_query ( $query_ordergoods, $link_wwback ) or die ( "Could not query query_ordergoods: " . mysql_error () );
	$result_refundment = mysql_query ( $query_refundment, $link_wwback ) or die ( "Could not query query_refundment: " . mysql_error () );
	$row_order = mysql_fetch_assoc ( $result_wwback );
	$row_refund = mysql_fetch_assoc ( $result_refundment );
	if ($row_order) {
		$goods_id = $row_order ['goods_id'];
		$goods_sname = $row_order ['goods_sname'];
		$goods_number = $row_order ['goods_number'];
		$shop_price = $row_order ['shop_price'];
		$order_id = $row_order ['order_id'];
		$city_name = $row_order ['city_name'];
		// 将新增的GoodsID插入数组中
		array_push ( $new_goods_id, $goods_id );
		// 将插入数据拼成字符串
		$insert_order .= "($goods_id,'$goods_sname',$goods_number,$shop_price,$order_id,'$city_name'),";
	}
	if ($row_refund) {
		$order_id = $row_refund ['order_id'];
		$refund_type = $row_refund ['refund_type'];
		$refund_num = $row_refund ['refundment_num'];
		// 将插入数据拼成字符串
		$insert_refund .= "($order_id,'$refund_type','$refund_num'),";
	}
}
// 将199上的数据插入到本地库中
$insert_order_goods .= substr ( $insert_order, 0, strlen ( $insert_order ) - 1 );
$insert_refundment_goods .= substr ( $insert_refund, 0, strlen ( $insert_refund ) - 1 );
DB::Query ( $insert_order_goods );
DB::Query ( $insert_refundment_goods );

// 删除指定表中的数据complaints_summary
$delete_synchronous = "delete from complaints_summary";
DB::Query ( $delete_synchronous );
// 根据本地订单表、退款表、投诉表关联 插入到指定表complaints_summary
$insert_synchronous = "
INSERT INTO complaints_summary
SELECT og.goods_id,og.goods_sname,og.goods_number,0 AS sales_count,og.shop_price,m.ORDERNUM as order_id,og.city_name,
       n.`NAME` as first_type ,n2.`NAME` as second_type,m.REGISTEREDTIME AS registered_time,
       m.CONSULTCONTENT as  consult_content,0 as consult_count,
       case WHEN jr.refund_type IS NULL THEN 1 ELSE 0 END  is_refund,
      jr.refund_type,jr.refund_num
         FROM ww_consult m LEFT  JOIN ww_consult_type n ON m.FIRSTTYPE = n.ID 
                           LEFT  JOIN ww_consult_type n2 ON m.SECONDTYPE = n2.ID 
                           LEFT JOIN jeehe_order_goods og ON m.ORDERNUM = og.order_id
                           LEFT JOIN jeehe_refundment jr ON m.ORDERNUM = jr.order_id
";
DB::Query ( $insert_synchronous );
// 更新complaints_summary中的商品销售数量 从199上查询
$query_wwback_salecount = "SELECT a.goods_id as goods_id, sum(a.goods_number) as sales_count
  FROM (SELECT  m.goods_id, og.goods_number
          from  jeehe_goods m 
         INNER JOIN jeehe_order_goods og ON m.goods_id = og.goods_id
         INNER JOIN jeehe_order_info oi ON og.order_id = oi.order_id
         WHERE   oi.email not in
               ('yangtao123@lmobile.cn', 'yangtao321@lmobile.cn',
                'yangtao111@lmobile.cn', 'wufei@55tuan.com',
                'gongmansha@55tuan.com', 'linpingping@55tuan.com',
                'panguozhang@gmail.com')
          AND  oi.pay_time>0 ";
$query_wwback_salecount_end = ") aGROUP BY a.goods_id";

// 清除数组中的重复值 防止重复查询
array_unique ( $new_goods_id );
foreach ( $new_goods_id as $new_goodsid ) {
	$query_salecount = "$query_wwback_salecount and m.goods_id = $new_goodsid" . $query_wwback_salecount_end;
	$result_salecount = mysql_query ( $query_salecount, $link_wwback ) or die ( "Could not query query_ordergoods: " . mysql_error () );
	$row_salecount = mysql_fetch_assoc ( $result_salecount );
	if($result_salecount){
	   $update_salescount = "update complaints_summary set sales_count = "
	   .$result_salecount['sales_count']." where goods_id = ".$result_salecount['goods_id'];
	   DB::Query ( $update_salescount );
	}
}
//更新产品总投次量
$update_consult_count = "UPDATE complaints_summary cs INNER JOIN
(SELECT m.goods_id,COUNT(m.registered_time) AS consult_count FROM complaints_summary m 
WHERE m.goods_id IS NOT NULL
GROUP BY m.goods_id ) cc ON cs.goods_id = cc.goods_id  set  cs.consult_count = cc.consult_count";
DB::Query($update_consult_count);

?>