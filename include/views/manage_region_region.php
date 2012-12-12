<?php 
$region = $arr_var['region'];
$pagestring = $arr_var['pagestring'];

?>
<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="signup">
<div class="dashboard" id="dashboard">
<ul><?php echo mcurrent_region('region_list'); ?></ul>
</div>
<div id="content" class="signup-box">
<div class="box">
	<div class="box-content">
		<div class="chaxun_box">
			<p style="float:right" align="right"><a id="export_voucher" name="export_voucher" href="region_create.php">添加大区</a></p>
			<div style="clear:both"></div>
				<div class="chaxun_over"  >
			    	<div class="div_ta">
			    	<table cellspacing="0" cellpadding="0" style="width:890px">
			        	<tbody><tr>
			                <th width="50px">中文名称</th>
			                <th width="50px">城市数量</th>
			                <th width="100px">操作</th>
			            </tr>
			            	<?php
							 foreach ($region as $value) {
								echo "<tr class='alt'>";
								echo "<td>".$value['name']."</td>";
								echo "<td>".get_count_city_by_region_id($value['id'])."</td>";
								echo "<td class='op'><a href='city_list.php?id={$value['id']}'>查看城市</a>&nbsp;&nbsp;<a href='region_edit.php?id={$value['id']}'>编辑</a>&nbsp;&nbsp;<a href='region_delete.php?id={$value['id']}'>删除</a></td></tr>";
							}
							if(0==count($region)){
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
