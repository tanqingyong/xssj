<?php
$datas = $arr_var['datas'];
$month = $arr_var['month'];
$filter_city   = $arr_var['filter_city'];
$filter_region = $arr_var['filter_region'];
$sum_string = $arr_var['sum_string'];
?>
<script type="text/javascript">
function time(obj1,obj2)
{
  if(obj1.value.length == 4){
	  obj2.value = obj1.value+obj2.value.substr(4,2);
	  
  }else if(obj1.value.length == 1){
	  obj2.value = obj2.value.substr(0,4)+"0"+obj1.value;
  }else{
	  obj2.value = obj2.value.substr(0,4)+obj1.value;
  }
}
function cha_xun(){
    $("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/sixindex/index_forecast.php');
    $("form:first").submit();
}
function export_excel(){
    $("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/sixindex/export_forecast.php');
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
        <ul><?php echo mcurrent_sixindex('index_forecast'); ?></ul>
    </div>
    <div id="content" class="signup-box">
        <div class="box clear">
            <div class="box-content">
            <div class="chaxun_box">
             <form name='data_summary_form' method='get'><br></br>
                  <div class="chaxun_boxt">
                   <dl>
					<dt>预测年月：</dt>
					<dd><input type="hidden" id="month" name="month"
						value='<?php
						echo $month
						?>' /> <select onchange="time(this,month)">
						<?php
						global $option_year;
						echo "<option></option>";
						echo Utility::Option ( $option_year, intval(substr ( $month, 0, 4 )));
						?>
					</select> 年<select onchange="time(this,month)"><?php
					global $option_month;
					echo "<option></option>";
					echo Utility::Option ( $option_month, intval(substr ( $month, 4, 2 )));
					?>
					</select>月</dd>
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
                    <p style="width:400px;">
                        <a href="javascript:void(0);" onclick="cha_xun();">查询</a>
                        <?php if(date("d")>=25 && !$datas && $_SESSION['grade']==1 && $month == (date("Y").(date("m")+1))){?>
                            <a href="/manage/sixindex/index_forecast_add.php">创建</a>
                        <?php }?>
                        <a href="javascript:void(0);" onclick="export_excel();">导出</a>
                    </p>
                      </form>
                      <h1>导入六大指标</h1>
						<ul>
							<li class="li_con">
							  <form id="import_form" name="import_form" action="k3_department_import.php" enctype="multipart/form-data" method="POST">
								  <p style="width:500px">
								  	<font style="font-weight:bold;">选择文件：</font>
								  	<input type="file" id="excel_file" name="excel_file" class="multi" maxlength="100"/>
								  	<a href="javascript:document.import_form.submit();">导入数据</a>
								  </p>
							  </form>
							</li>
						</ul>
                     <h1>查询结果</h1>
                      <ul>
                        <li><em>●</em><strong>汇总金额</strong></li>
                        <li class="li_con">
                        <?php
                            echo $sum_string;
                        ?>
                        
                        </li>
                        <li class="li_tit"><em>●</em><strong>结果列表</strong></li>
                    </ul>      
                  <div class="chaxun_over">
        <div class="div_ta" > 
        <!--如出现表格内没有内容，请用"&nbsp;"填充-->
        <table cellspacing="0" cellpadding="0" style="width:890px;">
            <tbody>
            <tr><th width="">月份</th><th width="">大区</th><th width="">城市</th><th width="">预测销售额</th><th>预测正毛利</th><th width="">预测负毛利</th><th width="">预测毛利</th><th width="">预测毛利率</th><th width="">预测预付款</th><th>操作</th></tr>
            <?php foreach($datas as $data){?>
            <tr>
                <td><?php echo get_six_indicator_month($data['indicator_date']);?></td>
                <td><?php echo $data['region'];?></td>
                <td><?php echo $data['city'];?></td>
                <td><?php echo floatval($data['pre_sale_amount']);?></td> 
                <td><?php echo floatval($data['pre_positive_profile']);?></td>
                <td><?php echo floatval($data['pre_lose_profile']);?></td>
                <td><?php echo floatval($data['pre_profile']);?></td>
                <td><?php echo floatval($data['pre_profile_rate']);?>%</td>
                <td><?php echo floatval($data['pre_advance_payment']);?></td>
                <td><?php if((date("d")>=25&&$data['indicator_date']==date('Y').date('m')+1&&((!$data['is_modified_by_regioner'] &&$_SESSION['grade']!=3)&&$_SESSION['grade']!=4)||$_SESSION['grade']==3)){?>
                	  <a href="/manage/sixindex/index_forecast_edit.php?city=<?php echo urlencode($data['city']);?>&indicator_date=<?php echo $data['indicator_date'];?>">编辑</a>
                	<?php }?>
                </td>
            </tr>
            <?php }?>
            <?php if(!$datas){?>
            <tr><td  colspan='10'>没有数据</td></tr>
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