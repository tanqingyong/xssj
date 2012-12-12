<?php
$menu_logs = $arr_var ['menu_logs'];
$pagestring = $arr_var ['pagestring'];
$menu_grade1_array = $arr_var ['menu_grade1_array'];
$menu_array = $arr_var ['menu_array'];
$conditions = $arr_var ['conditions'];
?>
<script type="text/javascript">
function cha_xun(){
	$("form:first").submit();
}

var xmlHttp;
function selectsecondmenu()
{
	var parent_id = document.getElementById('menu_parent_id').value;
	xmlHttp=GetXmlHttpObject();
	if (xmlHttp==null){
  		alert ("Browser does not support HTTP Request")
  		return;
  	} 
  	
	var url="<?php echo WEB_ROOT;?>/ajax/getsecondmenubyparent.php";
	url=url+"?parent_id="+parent_id;
	xmlHttp.onreadystatechange=stateChanged;
	xmlHttp.open("GET",url,true);
	xmlHttp.send(null);
} 

function stateChanged() 
{ 
	if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete"){ 
		var msg =xmlHttp.responseText;
		document.getElementById('menu_id').innerHTML=msg;
	} 
}

function GetXmlHttpObject()
{
	var xmlHttp=null;
	try{
	 	// Firefox, Opera 8.0+, Safari
	 	xmlHttp=new XMLHttpRequest();
	}catch (e){
	 // Internet Explorer
		 try{
		  	xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		 }catch (e){
		  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
		 }
	}
	return xmlHttp;
}
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="coupons">
    <div class="dashboard" id="dashboard">
        <ul><?php echo mcurrent_user('menulog_search'); ?></ul>
    </div>
    <div id="content" class="signup-box">
        <div class="box clear">
            <div class="box-content">
            <div class="chaxun_box">
             <form name='menu_log_form' method='get'><br></br>
                  <div class="chaxun_boxt"> 
                  <dl>
				    <dt>一级菜单：</dt>
				    <dd><select id="menu_parent_id" name="menu_parent_id" class="se_ect3" onchange="javascript:selectsecondmenu()">
	                    	<option></option>
	                        <?php
								echo '' . Utility::Option ($menu_grade1_array,$conditions['menu_parent_id']) . '';
							?>
                         </select>
                     </dd>
				  </dl> 
                  <dl>
                        <dt>二级菜单：</dt>
                        <dd>
                        <div id="menu_id">
                        <select name="menu_id" class="se_ect3">
                        	<option></option>
                            <?php
								echo '' . Utility::Option (get_second_menu_by_parent_id($conditions['menu_parent_id']), $conditions['menu_id']) . '';
							?>
						</select>
						</div>
                        </dd>
                    </dl>
                   
                    <dl>
                        <dt>用户名：</dt>
                        <dd><input type="text" name="user_name"  
				        value="<?php echo $conditions['user_name']; ?>"/>
                        </dd>
                    </dl>
                   
			
                  </div>
                    <p style="width:250px;">
                    	<a onclick="cha_xun();" href="javascript:void(0);">查询</a>
                    </p>
                  </form>
                     <h1>查询结果</h1>  
                  <div class="chaxun_over">
        <div class="div_ta" > 
        <!--如出现表格内没有内容，请用"&nbsp;"填充-->
        <table cellspacing="0" cellpadding="0" style="width:890px;">
            <tbody>
            <tr><th width="">一级菜单</th><th>二级菜单</th><th>用户名</th><th width="">访问时间</th><th width="">来访IP</th></tr>
            <?php foreach($menu_logs as $data){?>
            <tr><td><?php echo $menu_array[$data ['parent_id']];?></td>
            	<td><?php echo $menu_array[$data ['menu_id']];?></td>
            	<td><?php echo $data['username'];?></td>
                <td><?php echo date ( 'Y-m-d H:i:s', $data ['action_time'] );?></td>
                <td><?php echo $data['ip'];?></td>
            </tr>
            <?php }?>
            <?php if(!$menu_logs){?>
            <tr><td  colspan='10'>没有数据</td></tr>
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