<?php
$datas         = $arr_var['datas'];
$pagestring    = $arr_var['pagestring'];
$page_size     = $arr_var['page_size'];
$monthend      = $arr_var['monthend'];
$monthstart   = $arr_var['monthstart'];

global $option_year_month;

foreach($datas as $key=>$data ){
	$mg += $datas[$key]['month_goodsnum'];
	$mm += $datas[$key]['month_money'];
	$mp += $datas[$key]['month_profile'];
	$eg += $datas[$key]['early_goodsnum'];
	$em += $datas[$key]['early_money'];
	$ep += $datas[$key]['early_profile'];
	$midg += $datas[$key]['mid_goodsnum'];
	$midm += $datas[$key]['mid_money'];
	$midp += $datas[$key]['mid_profile'];
	$endg += $datas[$key]['end_goodsnum'];
	$endm += $datas[$key]['end_money'];
	$endp += $datas[$key]['end_profile'];
}

?>
<script type="text/javascript">
function time(obj1,obj2)
{
  if(obj1.value.length == 4){
	  obj2.value = obj1.value+obj2.value.substr(4,2);
	  
  }else if(obj1.value.length == 1){
	  obj2.value = obj2.value.substr(0,4)+"0"+obj1.value;
  }else{
	  obj2.value = obj1.value;
  }
}
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
	if(check_date()){
	    $("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/moneydetail/month_sale_data_country.php');
	    $("form:first").submit();
	}
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
	if(check_date()){
		$("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/moneydetail/export_month_sale_data_country.php');
	    $("form:first").submit();
	}
}
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="coupons">
    <div class="dashboard" id="dashboard">
        <ul><?php echo mcurrent_moneydetail('month_sale_data_country'); ?></ul>
    </div>
    <div id="content" class="signup-box">
        <div class="box clear">
            <div class="box-content">
            <div class="chaxun_box">
             <form name='data_summary_form' method='get'><br></br>
                  <div class="chaxun_boxt">
                   <dl>
					<dt>查询月份：</dt>
					<dd><input type="hidden" id="monthstart" name="monthstart" value='<?php echo $monthstart;?>' />
						<?php
							echo " <select onchange=\"time(this,monthstart)\">";
		                    echo ''.Utility::getChannel($option_year_month,$monthstart).'';
							echo " </select>";
						?>				 
				      到<input type="hidden" id="monthend" name="monthend" value='<?php echo $monthend;?>' />
						<?php
							echo " <select onchange=\"time(this,monthend)\">";
		                    echo ''.Utility::getChannel($option_year_month,$monthend).'';
							echo " </select>";
						?>				 
				 </dd> </dl>

                  </div>
                    <p style="width:250px;">
                        <a href="javascript:void(0);" onclick="cha_xun();">查询</a>
                        <a href="javascript:void(0)"  onclick="export_excel();">导出</a>
                    </p>
                  </form>
                     <h1>查询结果</h1>    
                  <div class="chaxun_over">
        <div class="div_ta" > 
        <!--如出现表格内没有内容，请用"&nbsp;"填充-->
        <table cellspacing="0" cellpadding="0" style="width:1000px">
            <tbody>
            <tr><th>月份</th><th>月销量</th><th>月销售额</th><th>月毛利</th><th>毛利率</th><th>上旬销量</th><th>上旬销售额</th><th>上旬毛利</th><th>中旬销量</th><th>中旬销售额</th><th>中旬毛利</th><th>下旬销量</th><th>下旬销售额</th><th>下旬毛利</th></tr>
            <?php foreach($datas as $data){?>
            <tr>
                <td style="width:80px"><?php echo $data['month'];?></td>
                <td><?php echo intval($data['month_goodsnum']);?></td>
                <td><?php echo intval($data['month_money']);?></td>
                <td><?php echo round($data['month_profile'],2);?></td> 
                <td><?php echo round($data['month_profile']/$data['month_money']*100,2);echo "%";?></td>
                <td><?php echo intval($data['early_goodsnum']);?></td>
                <td><?php echo intval($data['early_money']);?></td>
                <td><?php echo $data['early_profile'];?></td>
                <td><?php echo intval($data['mid_goodsnum']);?></td>
                <td><?php echo intval($data['mid_money']);?></td>
                <td><?php echo $data['mid_profile'];?></td>
                <td><?php echo intval($data['end_goodsnum']);?></td>
                <td><?php echo intval($data['end_money']);?></td>
                <td><?php echo $data['end_profile'];?></td>
            </tr>
            <?php } ?><!--
            <tr>
             <td><?php echo "合计";?></td>
                <td><?php echo $mg;?></td>
                <td><?php echo $mm;?></td>
                <td><?php echo $mp;?></td> 
                <td><?php echo round($mp/$mg,4)."%";?></td>
                <td><?php echo $eg;?></td>
                <td><?php echo $em;?></td>
                <td><?php echo $ep;?></td>
                <td><?php echo $midg;?></td>
                <td><?php echo $midm;?></td>
                <td><?php echo $midp;?></td>
                <td><?php echo $endg;?></td>
                <td><?php echo $endm;?></td>
                <td><?php echo $endp;?></td>
            </tr>
            --><?php if(!$datas){?>
            <tr><td  colspan='14'>没有数据</td></tr>
            <?php }?>
        </tbody></table>
        </div>
    </div>
         </div>
         <div class="sect">
                    <table width ="948px" id="orders-list" cellspacing="0" cellpadding="0" border="0" class="coupons-table">
                    
                    <tr>
                        <td style="text-align:right;">
                                                                    每页显示数<select id="pagesize" name="pagesize" class="se_ect1" onchange="change_pagesize();">
                                <?php echo Utility::Option(array(10=>'10',20=>'20',30=>'30',40=>'40', 50=>'50'),$page_size); ?>
                            </select>
                        </td>
                        <td width="400px"><?php echo $pagestring; ?></tr>
                    </table>
                </div>
           
              </div>
        </div>
    </div>
</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->