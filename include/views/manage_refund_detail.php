<?php
$datas         = $arr_var['datas'];
$pagestring    = $arr_var['pagestring'];
$page_size     = $arr_var['page_size'];
$date_from     = $arr_var['date_from'];
$date_end      = $arr_var['date_end'];
$filter_city   = $arr_var['filter_city'];
$filter_region = $arr_var['filter_region'];
$goods_id = $arr_var['goods_id'];
$cate = $arr_var['cate'];
if($goods_id==0) $goods_id = '';
?>
<script type="text/javascript">
function GetParams() {
    var url = location.search; 
    var theRequest = new Array();
    var return_str = "";
    var pagesize = 0;
    if (url.indexOf("?") != -1) {
       var str = url.substr(1);
       strs = str.split("&");
       for(var i = 0; i < strs.length; i ++) {
          theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
          if(strs[i].split("=")[0]=='pagesize'){
              theRequest[strs[i].split("=")[0]]=$("#pagesize").val(); 
              pagesize=1;
          }
          if(i==strs.length-1){
              return_str +=strs[i].split("=")[0]+'='+theRequest[strs[i].split("=")[0]];
          }else{
              return_str +=strs[i].split("=")[0]+'='+theRequest[strs[i].split("=")[0]]+'&';
          }
       }
    }
    if(pagesize==0){
        if(return_str)
            return_str +="&pagesize="+$("#pagesize").val(); 
        else 
            return_str +="pagesize="+$("#pagesize").val(); 
    }
    return return_str;
}
function change_pagesize(){
	 var pagesize = $("#pagesize").val();
	 var url_arr = window.location.href.split('?');
	 var url = url_arr[0];
	 window.location.href=url+'?'+GetParams();
	 
}
function cha_xun(){
	//if(check_date()){
	    $("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/sixindex/refund_detail.php');
	    $("form:first").submit();
	//}
}

function check_date(){
    var date_from = $('#date_from').val();
    var date_end = $('#date_end').val();
    if( date_from && date_end ){
        var from_date = date_from.split("-");
        var end_date = date_end.split("-");
        if(date_from>date_end){
            alert("开始时间不能比结束时间大!");
            return false;
        }
        if(from_date[0]!=end_date[0]||from_date[1]!=end_date[1]){
            alert("查询时间段不能跨月!");
            return false;
        }    
    }
    return true;
}
$(document).ready(function(){
    $("#region").change(function(){
        var region = $("#region").val();
        $.post("<?php echo WEB_ROOT; ?>/ajax/getcities.php", { region_id : region, pagefrom : "data" },function(data){
            $("#city").html(data);
        });
    });
});
function getDaysInMonth(year,month){
	month = parseInt(month,10)+1;
	var temp = new Date(year, month, 0);
    return temp.getDate();
}
function export_excel(){
	//if(check_date()){
		$("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/sixindex/export_refund_detail.php');
	    $("form:first").submit();
	//}
}
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="coupons">
<div class="dashboard" id="dashboard">
<ul>
<?php echo mcurrent_sixindex('refund_detail'); ?>
</ul>
</div>
<div id="content" class="signup-box">
<div class="box clear">
<div class="box-content">
<div class="chaxun_box">
<form name='data_summary_form' method='get'><br></br>
<div class="chaxun_boxt">
<dl>
	<dt>退款时间段：</dt>
	<dd><input type="text" style="width: 87px" onfocus="WdatePicker()"
		name="date_from" id="date_from"
		value='<?php
				        echo $date_from;
				        ?>' /> -- <input type="text" style="width: 87px;"
		onfocus="WdatePicker()" name="date_end" id="date_end"
		value='<?php
				        echo $date_end;
				        ?>' /></dd>
</dl>
				        <?php if($_SESSION['grade']!=1 && $_SESSION['grade']!=2){?>
<dl>
	<dt>大区：</dt>
	<dd><select id="region" name="region" class="se_ect1">
	<?php if(!$filter_region){?>
		<option></option>
		<?php  echo ''.Utility::Option(get_region_option(),$filter_region).''; ?>
		<?php }else{
			echo ''.Utility::Option(get_region_option(),$filter_region).'';
		}
		?>
	</select></dd>
</dl>
		<?php }
		if($_SESSION['grade']!=1 ){
			?>
<dl>
	<dt>城市：</dt>
	<dd><select id="city" name="city" class="se_ect1">
	<?php if(!$filter_city){?>
		<option></option>
		<?php echo ''.Utility::Option(get_cities_by_region_id($filter_region?$filter_region:$_SESSION['area_id']),$filter_city).'';?>
		<?php }else{
			echo ''.Utility::Option(get_cities_by_region_id($filter_region?$filter_region:$_SESSION['area_id']),$filter_city).'';
			echo '<option>整个大区</option>';
		}?>
	</select></dd>
</dl>
		<?php }?>
<dl>
	<dt>产品ID：</dt>
	<dd><input type="text" style="width: 87px" name="goods_id"
		id="goods_id" value="<?php  echo $goods_id;?>" /></dd>
</dl>
<dl>
	<dt>类别：</dt>
	<dd><input name="cate" id="cate" value="<?php echo $cate;?>" /></dd>
</dl>
</div>
<p style="width: 250px;"><a href="javascript:void(0);"
	onclick="cha_xun();">查询</a> <a href="javascript:void(0)"
	onclick="export_excel();">导出</a></p>
</form>
<h1>查询结果</h1>
<div class="chaxun_over">
<div class="div_ta"><!--如出现表格内没有内容，请用"&nbsp;"填充-->
<table cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<th width="">大区</th>
			<th width="">销售城市</th>
			<th width="">来源城市</th>
			<th>销售人员编号</th>
			<th>销售人员</th>
			<th>商户ID</th>
			<th>商户名称</th>
			<th width="">产品ID</th>
			<th width="">商品名称</th>
			<th>一级分类</th>
			<th width="">二级分类</th>
			<th width="">结算单价</th>
			<th width="">销售单价</th>
			<th width="">退款数量</th>
			<th>退款金额</th>
			<th>退款毛利额</th>
			<th>折扣</th>
			<th>产品上线时间</th>
			<th>产品下线时间</th>
			<th>产品截止时间</th>
		</tr>
		<?php foreach($datas as $data){?>
		<tr>
			<td><?php echo $data['regionname'];?></td>
			<td><?php echo $data['city'];?></td>
			<td><?php echo $data['fromcity'];?></td>
			<td><?php echo $data['salesman_id'];?></td>
			<td><?php echo $data['salesman'];?></td>
			<td><?php echo $data['biz_id'];?></td>
			<td><?php echo $data['biz_name'];?></td>
			<td><?php echo $data['goods_id'];?></td>
			<td><?php echo $data['goods_name'];?></td>
			<td><?php echo $data['type1'];?></td>
			<td><?php echo $data['second_name'];?></td>
			<td><?php echo $data['cost_price'];?></td>
			<td><?php echo $data['sale_price'];?></td>
			<td><?php echo $data['refund_num'];?></td>
			<td><?php echo $data['refund_money'];?></td>
			<td><?php echo $data['refund_profile'];?></td>
			<td><?php echo round($data['zhekou']*10,1)."折";?></td>
			<td><?php echo $data['start_date'];?></td>
			<td><?php echo $data['end_date'];?></td>
			<td><?php echo $data['jiezhi_time'];?></td>

		</tr>
		<?php }?>
		<?php if(!$datas){?>
		<tr>
			<td colspan='15'>没有数据</td>
		</tr>
		<?php }?>
	</tbody>
</table>
</div>
</div>
</div>
<div class="sect">
<table width="948px" id="orders-list" cellspacing="0" cellpadding="0"
	border="0" class="coupons-table">

	<tr>
		<td style="text-align: right;">每页显示数<select id="pagesize"
			name="pagesize" class="se_ect1" onchange="change_pagesize();">
			<?php echo Utility::Option(array(10=>'10',20=>'20',30=>'30',40=>'40', 50=>'50'),$page_size); ?>
		</select></td>
		<td width="400px"><?php echo $pagestring; ?>
	
	</tr>
</table>
</div>

</div>
</div>
</div>
</div>
</div>
<!-- bd end --></div>
<!-- bdw end -->
