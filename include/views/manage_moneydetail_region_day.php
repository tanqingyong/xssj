<?php
$datas = $arr_var['datas'];
$pagestring = $arr_var['pagestring'];
$date_from = $arr_var['date_from'];
$date_end = $arr_var['date_end'];
$page_size = $arr_var['page_size'];
$sum_datas = $arr_var['sum_datas'];
$filter_region = $arr_var['filter_region'];
?>
<script type="text/javascript">
function cha_xun(){
	if(check_date()){
		$("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/moneydetail/region_day.php');
	    $("form:first").submit();
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
function export_excel(){
	if(check_date()){
		$("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/moneydetail/export_region_day.php');
	    $("form:first").submit();
	}
}
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="coupons">
    <div class="dashboard" id="dashboard">
        <ul><?php echo mcurrent_moneydetail('region_day'); ?></ul>
    </div>
    <div id="content" class="signup-box">
        <div class="box clear">
            <div class="box-content">
            <div class="chaxun_box">
             <form name='data_summary_form' method='get'><br></br>
                  <div class="chaxun_boxt"> 
                  <dl>
				    <dt>查询时间段：</dt>
				    <dd><input type="text" style="width:87px" onfocus="WdatePicker()" name="date_from" id="date_from" 
				        value='<?php
				        echo $date_from;
				        ?>'/> --
				        <input type="text" style="width:87px;" onfocus="WdatePicker()" name="date_end" id="date_end" 
				        value='<?php
				        echo $date_end;
				        ?>'/></dd>
				  </dl>
				  
				  <?php if( $_SESSION['grade']!=1 && $_SESSION['grade']!=2 ){?>
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
                            </select>
                        </dd>
                    </dl>
                    <?php }?>
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
        <table cellspacing="0" cellpadding="0">
            <tbody>
            <tr><th width="">日期</th><th>大区</th><th width="">销量</th><th width="">销售额</th><th>毛利</th><th>毛利率</th><th>累计销量</th><th>累计销售额</th><th>累计毛利</th><th>验证量</th><th>验证额</th><th>验证毛利</th><th>验证毛利率</th><th>累计验证量</th><th>累计验证额</th><th>累计验证毛利</th></tr>
            <?php foreach($datas as $data){?>
            <tr><td><?php echo $data['data_date'];?></td>
            	<td><?php echo $data['region'];?></td>
                <td><?php echo $data['goods_day'];?></td>
                <td><?php echo $data['sales_day'];?></td>
                <td><?php echo $data['profile_day'];?></td>
                <td><?php echo $data['profile_rate'];?>%</td>
                <td><?php echo $sum_datas[$data['data_date'].$data['region'].'goods_sum'];?></td>
                <td><?php echo $sum_datas[$data['data_date'].$data['region'].'sales_sum'];?></td>
                <td><?php echo $sum_datas[$data['data_date'].$data['region'].'profile_sum'];?></td>
                
                <td><?php echo $data['verify_num'];?></td>
                <td><?php echo $data['verify_sum'];?></td>
                <td><?php echo $data['verify_profile'];?></td>
                <td><?php echo round($data['verify_profile']*100/$data['sales_day'],2);?>%</td>
                <td><?php echo $sum_datas[$data['data_date'].$data['region'].'verify_num_sum'];?></td>
                <td><?php echo $sum_datas[$data['data_date'].$data['region'].'verify_sum_sum'];?></td>
                <td><?php echo $sum_datas[$data['data_date'].$data['region'].'verify_profile_sum'];?></td>
            </tr>
            <?php }?>
            <?php if(!$datas){?>
            <tr><td  colspan='9'>没有数据</td></tr>
            <?php }?>
        </tbody></table>
        </div>
    </div>
         </div>
           <div class="sect">
                    <table width ="948px"id="orders-list" cellspacing="0" cellpadding="0" border="0" class="coupons-table">
                    
                    <tr><td style="text-align:right;">
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