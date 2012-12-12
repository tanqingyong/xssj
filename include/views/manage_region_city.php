<?php
$area_id = $arr_var['area_id'];
$city = $arr_var['city'];
$name = $arr_var['name'];
$pagestring = $arr_var['pagestring'];
if(!is_null($area_id))
	$area_para = "&area_id=$area_id";
?>

<script>
function submit(){
	$("#region_city_form").submit();
}
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="signup">
<div class="dashboard" id="dashboard">
<ul><?php echo mcurrent_region('city_list'); ?></ul>
</div>
<div id="content" class="signup-box">
<div class="box">
	<div class="box-content">
		<div class="chaxun_box">
		<form id="region_city_form" name='region_city_form' method='get'><br></br>
		     <div class="chaxun_boxt">
                    <dl>
                        <dt>大区：</dt>
                        <dd><select id="id" name="id" class="se_ect1">
                                <option value=0></option>
                                <?php echo ''.Utility::Option(get_region_option(),$area_id); ?>
                            </select>
                        </dd>
                    </dl>
                    <dl>
                        <dt>城市：</dt>
                        <dd><input type="text" id="name" name="name" class="se_ect1" value="<?php echo $name;?>">
                        </dd>
                    </dl>
                  </div>
                    <p style="width:250px;">
                        <a href="javascript:void(0);" onclick="submit();">查询</a>
                    </p>
                  </form>
			<p style="float:right" align="right"><a id="export_voucher" name="export_voucher" href="city_create.php">添加城市</a></p>
			<div style="clear:both"></div>
				<div class="chaxun_over">
			    	<div class="div_ta">
			    	<table cellspacing="0" cellpadding="0" style="width:890px">
			        	<tbody><tr>
			            	<th width="50px">所属大区</th>
			                <th width="50px">中文名称</th>
			                <th width="100px">操作</th>
			            </tr>
			            	<?php
							 foreach ($city as $value) {
								echo "<tr>";
								echo "<td>".get_region_name_by_id($value['area_id'])."</td>";
								echo "<td>".$value['name']."</td>";
								echo "<td><a href='city_edit.php?id={$value['id']}$area_para'>编辑</a>&nbsp;&nbsp;<a href='city_delete.php?id={$value['id']}$area_para'>删除</a></td></tr>";
							}
							if(0==count($city)){
								echo "<tr><td colspan='3'>没有数据</td></tr>";
							}
			            	?>
			        	</tbody></table>
			        </div>
			    </div>
			</div>
		    <div class="sect">
				<table width ="948px"id="orders-list" cellspacing="0" cellpadding="0" border="0" class="coupons-table">
				<tr><td><?php echo $pagestring; ?></tr>
                </table>
			</div>
		</div>
	</div>
</div>
</div></div></div>
