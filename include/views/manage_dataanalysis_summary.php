<?php
$datas = $arr_var['datas'];
$pagestring = $arr_var['pagestring'];
$filter_date_start = $arr_var['filter_date_start'];
$filter_date_end = $arr_var['filter_date_end'];
$filter_city = $arr_var['filter_city'];
$filter_region = $arr_var['filter_region']; 
$page_size = $arr_var['page_size'];
$sum_string = $arr_var['sum_string'];
?>
<script type="text/javascript">
function cha_xun(){
	$("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/dataanalysis/summary.php');
    $("form:first").submit();
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
function export_excel(){
	$("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/dataanalysis/export_summary.php');
    $("form:first").submit();
}
$(document).ready(function(){
    $("#region").change(function(){
        var region = $("#region").val();
        $.post("<?php echo WEB_ROOT; ?>/ajax/getcities.php", { region_id : region, pagefrom : "data" },function(data){
            $("#city").html(data);
        });
    });
});
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="coupons">
    <div class="dashboard" id="dashboard">
        <ul><?php echo mcurrent_dataanalysis('summary'); ?></ul>
    </div>
    <div id="content" class="signup-box">
        <div class="box clear">
            <div class="box-content">
            <div class="chaxun_box">
             <form name='data_summary_form' method='get'><br></br>
                  <div class="chaxun_boxt">
                   
                     <dl>
                        <dt>按天查询：</dt>
				          <dd><input type="text" style="width:87px" onfocus="WdatePicker()" name="stratdate" id="stratdate" 
				        value='<?php
				        echo $filter_date_start;
				        ?>'/> --
				        <input type="text" style="width:87px;" onfocus="WdatePicker()" name="enddate" id="enddate" 
				        value='<?php
				        echo $filter_date_end;
				        ?>'/></dd>   
                    </dl>
                   <?php if($_SESSION['grade']!=1 && $_SESSION['grade']!=2){?>
                    <dl>
                        <dt>大区：</dt>
                        <dd><select id="region" name="region" class="se_ect1">
                              <?php if(!$filter_region){?>
                                <option></option>
                               <?php  echo ''.Utility::Option(get_region_option(),$filter_region).''; ?>
                               <?php }else{
                               	        echo '<option>全国</option>';
                               	        echo ''.Utility::Option(get_region_option(),$filter_region).'';  
                                     }
                               ?>
                            </select>
                        </dd>
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
                     <ul>
					    <li><em>●</em><strong>汇总金额</strong></li>
					    <li class="li_con">
					    <?php
					        echo $sum_string;
					    ?>
					    
					    </li>
					    <li class="li_tit" style="padding-top:20px"><em>●</em><strong>结果列表</strong></li>
                    </ul>  
                  <div class="chaxun_over">
        <div class="div_ta" > 
        <!--如出现表格内没有内容，请用"&nbsp;"填充-->
        <table cellspacing="0" cellpadding="0" style="width:890px;">
            <tbody>
            <tr><th width="70px">大区</th><th width="100px">城市</th><th width="90px">uv</th><th>ip</th><th>pv</th><th>日期</th><th width="90px">销售额</th><th width="90px">销售额/uv</th></tr>
            <?php foreach($datas as $data){?>
            <tr><td><?php echo $data['regionname'];?></td>
                <td><?php echo $data['city'];?></td>
                <td><?php echo $data['uv'];?></td>
                <td><?php echo $data['ip'];?></td>
                <td><?php echo $data['pv'];?></td>
                <td><?php echo $data['data_date'];?></td>
                <td><?php echo $data['suc_total_price'];?></td>
                <td><?php echo round($data['suc_total_price']*100/$data['uv'])/100;?></td>
            </tr>
            <?php }?>
            <?php if(!$datas){?>
            <tr><td  colspan='8'>没有数据</td></tr>
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