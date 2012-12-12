<?php 
$users = $arr_var['users'];
$pagestring = $arr_var['pagestring'];
$filter_name = $arr_var['filter_name'];
$filter_grade = $arr_var['filter_grade'];
$filter_region = $arr_var['filter_region'];
$filter_online_status = $arr_var['filter_online_status'];
?>
<script type="text/javascript">
function cha_xun(){
	$("form:first").submit();
}

</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="coupons">
	<div class="dashboard" id="dashboard">
		<ul><?php echo mcurrent_user('manage'); ?></ul>
	</div>
    <div id="content" class="signup-box">
		<div class="box clear">
            <div class="box-content">
            <div class="chaxun_box">
             <form name='user_manage_form' method='get'><br></br>
                  <div class="chaxun_boxt">
				    <dl>
				    	<dt>用户名：</dt>
				        <dd><input type="text" name="filter_name" class="h-input" value="<?php echo htmlspecialchars($filter_name); ?>" >&nbsp;</dd>
				    </dl>
				    <dl>
				    	<dt>用户权限：</dt>
				    	<dd><select name="filter_grade" class="f-city">
				    	<?php echo Utility::Option(array(0=>'所有权限',1=>'城市权限',2=>'大区权限',3=>'管理员权限',4=>'总部权限'),$filter_grade); ?>
				    	</select>&nbsp;</dd>
				    </dl>
				    <dl>
				        <dt>大区</dt>
				        <dd><select name="filter_region" class="f-city">
				            <option></option>
				            <?php  echo ''.Utility::Option(get_region_option(),$filter_region).''; ?>
				            </select></dd>
				    </dl>
				     <dl>
				        <dt>在线状态</dt>
				        <dd><select name="filter_online_status" class="f-city">
				            <option></option>
				            <?php  echo ''.Utility::Option(get_all_online_status(),$filter_online_status).''; ?>
				            </select></dd>
				    </dl>
				  </div>
					<p style="width:250px;"><a href="javascript:void(0);" onclick="cha_xun();">查询</a></p>
				  </form>
				  	 <h1>查询结果</h1>	  
				  <div class="chaxun_over">
    	<div class="div_ta" > 
        <!--如出现表格内没有内容，请用"&nbsp;"填充-->
    	<table cellspacing="0" cellpadding="0" style="width:890px;">
        	<tbody>
        	<tr><th width="50">ID</th><th width="150" >用户名</th><th width="200">注册时间</th><th width="100">权限</th><th>大区/城市</th><th>在线状态</th><th width="100">操作</th></tr>
            <?php if($users){foreach($users as $index=>$one){?>
	           <tr <?php echo $index%2?'':'class="alt"'; ?> id="user-list-id-<?php echo $one['id']; ?>">
						<td><?php echo $one['id']; ?></td>
						<td><?php echo $one['username']; ?></td>
						<td><?php echo date('Y-m-d H:i', $one['create_time']); ?></td>
						<?php if($one['grade']==1) 
						          echo "<td>城市权限</td><td>{$one['city_name']}</td>"; 
						      else if($one['grade']==2)
						          echo "<td>大区权限</td><td>{$one['region_name']}</td>";
						      else if($one['grade']==4)
						          echo "<td>总部权限</td><td>全国</td>";
						      else
						          echo "<td>管理员权限</td><td>全国</td>";
						if($one ['online_status']==1){
							echo "<td style='color:red'>";
						} else {
							echo "<td>";
						}
							echo get_online_status($one ['online_status'])." </td>";        
						?>
						<td class="op" >
							<a href="/manage/user/edit.php?user_id=<?php echo $one['id']; ?>">编辑</a>&nbsp;&nbsp;
							<a href="/manage/user/delete_user.php?user_id=<?php echo $one['id']; ?>">删除</a>
						</td>
					</tr>
            <?php }}else{?>
            	 <tr><td  colspan='7'>没有用户</td></tr>
            <?php }?>
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
</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->

