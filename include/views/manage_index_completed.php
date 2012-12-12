<?php
$datas = $arr_var['datas'];
$date_from     = $arr_var['date_from'];
$date_end      = $arr_var['date_end'];
$filter_city   = $arr_var['filter_city'];
$filter_region = $arr_var['filter_region'];
?>
<script type="text/javascript">
function cha_xun(){
	if(check_date()){
	    $("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/sixindex/index_completed.php');
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
	if(check_date()){
	    $("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/sixindex/export_completed.php');
	    $("form:first").submit();
	}
}
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="coupons">
    <div class="dashboard" id="dashboard">
        <ul><?php echo mcurrent_sixindex('index_completed'); ?></ul>
    </div>
    <div id="content" class="signup-box">
        <div class="box clear">
            <div class="box-content">
            <div class="chaxun_box">
             <form name='data_summary_form' method='get'><br></br>
                  <div class="chaxun_boxt">
                   <dl>
				    <dt>支付时间段：</dt>
				    <dd><input type="text" style="width:87px" onfocus="WdatePicker()" name="date_from" id="date_from" 
                        value='<?php
                        echo $date_from;
                        ?>'/> --
                        <input type="text" style="width:87px;" onfocus="WdatePicker()" name="date_end" id="date_end" 
                        value='<?php
                        echo $date_end;
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
                  <div class="chaxun_over">
        <div class="div_ta" > 
        <!--如出现表格内没有内容，请用"&nbsp;"填充-->
        <table cellspacing="0" cellpadding="0">
            <tbody>
            <tr><th width="">大区</th><th width="">城市</th><th>实际销售额</th><th width="">预测销售额</th><th>实际正毛利</th><th>预测正毛利</th><th>实际负毛利</th><th width="">预测负毛利</th><th>实际毛利</th><th width="">预测毛利</th><th>实际毛利率</th><th width="">预测毛利率</th><th>实际预付款</th><th width="">预测预付款</th></tr>
            <?php foreach($datas as $data){?>
            <tr>
                <td><?php echo $data['region'];?></td>
                <td><?php echo $data['city'];?></td>
                <td><?php echo round($data['actual_price']*10)/10;?></td>
                <td><?php echo intval($data['pre_sale_amount']);?></td> 
                <td><?php echo round($data['actual_pos']*100)/100;?></td>
                <td><?php echo floatval($data['pre_positive_profile']);?></td>
                <td><?php echo abs(round($data['actual_lose']*100)/100);?></td>
                <td><?php echo floatval($data['pre_lose_profile']);?></td>
                <td><?php echo round($data['actual_pro']*100)/100;?></td>
                <td><?php echo floatval($data['pre_profile']);?></td>
                <td><?php echo round(floatval($data['actual_rate'])*10000)/100;?>%</td>
                <td><?php echo floatval($data['pre_profile_rate']);?>%</td>
                <td><?php echo 0;?></td>
                <td><?php echo floatval($data['pre_advance_payment']);?></td>
            </tr>
            <?php }?>
            <?php if(!$datas){?>
            <tr><td  colspan='15'>没有数据</td></tr>
            <?php }?>
        </tbody></table>
        </div>
    </div>
         </div>
           
              </div>
        </div>
    </div>
</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->