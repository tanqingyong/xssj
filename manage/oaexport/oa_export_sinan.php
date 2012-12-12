<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

//从QA提枚举数据
$link = mysql_connect("10.8.60.30", "oadata", 'W?K?X=P<n"') or die("Could not connect mysql3235: " . mysql_error());
mysql_select_db('mysql3235', $link) or die ('Can\'t use mysql3235 : ' . mysql_error());
mysql_query("set names utf8", $link);
$query_oa = "select * from udt_enumvalue;";
$result_oa = mysql_query($query_oa, $link) or die("Could not query udt_enumvalue: ". mysql_error());
if($result_oa){
	//删除本地枚举数据
	$localhost_link = mysql_connect("localhost", "yangzhimin", '!zW-ya9:') or die("Could not connect sinan: " . mysql_error());
	mysql_select_db('sinan', $localhost_link) or die ('Can\'t use sinan : ' . mysql_error());
	mysql_query("set names utf8", $localhost_link);
	
	$delete_oa = "delete from udt_enumvalue";
	mysql_query($delete_oa, $localhost_link) or die("Could not delete udt_enumvalue: ". mysql_error());
	//插入本地枚举数据
	$insert_oa = '';
	while ($row = mysql_fetch_array($result_oa, MYSQL_NUM)) {
		is_null($row[1])?$row[1]='':$row[1]=mysql_real_escape_string($row[1]);
		is_null($row[2])?$row[2]='':$row[2]=mysql_real_escape_string($row[2]);
		is_null($row[3])?$row[3]='':$row[3]=mysql_real_escape_string($row[3]);
		
		$insert_oa .= "($row[0],'$row[1]','$row[2]',$row[3]),";
	}
	$insert_oa = substr($insert_oa, 0, strlen($insert_oa)-1);
	$insert_oa = "insert into udt_enumvalue(enumid,value,name,f_state) values$insert_oa;";
	
	$result_oa = mysql_query($insert_oa, $localhost_link) or die("Could not insert udt_enumvalue: ". mysql_error());
	
	
	//从OA提取合同数据
	$query_oa = "select m.id,m.field25,m.field28,m.field115,d.field57,d.field64,d.field58,d.field59,d.field60,d.field66,d.field63,d.field62,m.field13
	 from utd_00222 d join utm_00222 m on d.masterid=m.id;";
	mysql_select_db('mysql3235', $link) or die ('Can\'t use mysql3235 : ' . mysql_error());
	$result_oa = mysql_query($query_oa, $link) or die("Could not query mysql3235 oa: ". mysql_error());
	//删除本地合同数据
	mysql_select_db('sinan', $localhost_link) or die ('Can\'t use sinan : ' . mysql_error());
	mysql_query("set names utf8", $localhost_link);
	
	$delete_oa = "delete from oa_contract";
	mysql_query($delete_oa, $localhost_link) or die("Could not query: ". mysql_error());
	//插入本地合同数据
	$insert_oa = '';
	while ($row = mysql_fetch_array($result_oa, MYSQL_NUM)) {
		//id contract_no contract_type city product_id product_name start_date end_date effective_start_date effective_end_date sell_price settlement_price partner_name
		is_null($row[0])?$row[0]='':$row[0]=$row[0];
		is_null($row[1])?$row[1]='':$row[1]=mysql_real_escape_string($row[1]);
		is_null($row[2])?$row[2]='NULL':$row[2]=$row[2];
		is_null($row[3])?$row[3]='NULL':$row[3]=$row[3];
		is_null($row[4])?$row[4]='':$row[4]=mysql_real_escape_string($row[4]);
		is_null($row[5])?$row[5]='':$row[5]=mysql_real_escape_string(trim($row[5]));
		is_null($row[6])?$row[6]='NULL':$row[6]="'$row[6]'";
		is_null($row[7])?$row[7]='NULL':$row[7]="'$row[7]'";
		is_null($row[8])?$row[8]='NULL':$row[8]="'$row[8]'";
		is_null($row[9])?$row[9]='NULL':$row[9]="'$row[9]'";
		is_null($row[10])?$row[10]=0.00:$row[10]=$row[10];
		is_null($row[11])?$row[11]=0.00:$row[11]=$row[11];
		is_null($row[12])?$row[12]='':$row[12]=mysql_real_escape_string($row[12]);

		$insert_oa .= "($row[0],'$row[1]',$row[2],$row[3],'$row[4]','$row[5]',$row[6],$row[7],$row[8],$row[9],$row[10],$row[11],'$row[12]'),";
	}
	$insert_oa = substr($insert_oa, 0, strlen($insert_oa)-1);
	$insert_oa = "insert into oa_contract(`id`, `contract_no`, `contract_type`, `city`, `product_id`, `product_name`, `start_date`, `end_date`, `effective_start_date`, `effective_end_date`, `sell_price`, `settlement_price`, `partner_name`) values$insert_oa;";
	
	$result_oa = mysql_query($insert_oa, $localhost_link) or die("Could not insert oa_contract: ". mysql_error());
	if(!$result_oa){
		$url = "http://60.28.195.138/submitdata/service.asmx/g_Submit?sname=dlwwtuan&spwd=12345678&scorpid=&sprdid=1012818&sdst=15801200209&smsg=sinan获取OA合同数据失败，请查证原因！";
		$timeout = 2;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		$result = curl_exec($ch);
		//echo "the result is ".$result;
	}
}
mysql_close($link);
mysql_close($localhost_link);
?>