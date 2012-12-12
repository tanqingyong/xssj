<?php 
$id = $arr_var['id'];
$arr_edited_user = $arr_var['arr_edited_user'];
$action = $arr_var['action'];
$current_menu = $arr_var['current_menu'];
?>
<script type="text/javascript">
$(document).ready(function(){
	<?php if($action!='编辑'){?>
	$("input[name='username']").val("");
	<?php }?>
	$("input[name='password1']").val("");
	$("#grade").change(function(){
		 var grade = $("#grade").val();
		 if(grade == '1'){
			 $("#region").html("<?php  echo ''.Utility::Option(get_region_option(),$arr_edited_user['area_id']).''; ?>");
			 $("#city").html("<?php  echo ''.Utility::Option(get_cities_by_region_id(1),$arr_edited_user['city_id']).''; ?>");
			 $("#field_region").show();
			 $("#field_city").show(); 
		 }else if(grade == '2'){
			 $("#region").html("<?php  echo ''.Utility::Option(get_region_option(),$arr_edited_user['area_id']).''; ?>");
			 $('#field_region').show();
			 $("#city").html("");
			 $("#field_city").hide();
		 }else{
			 $('#region').html("");
			 $('#field_region').hide();
			 $("#city").html("");
			 $("#field_city").hide();
		 }
	});
	$("#region").change(function(){
		if($("#grade").val()!=1)
			return;
		var region = $("#region").val();
		 $.post("<?php echo WEB_ROOT; ?>/ajax/getcities.php", { region_id : region, pagefrom : "user" },function(data){
			 $("#city").html(data);
		 });
		
	});
});
function check_value(){
	if(!check_username()){
		$("#signup-username").focus();
		return false;
	}
	if(!check_password()){
		$("#signup-password").focus();
		return false;
	}
	if(!check_confirmpwd()){
		$("#signup-password-confirm").focus();
		return false;
	}
	return true;
}
function check_username(){
	var username = $("#signup-username").val();
	if(username=="<?php echo $arr_edited_user['username']; ?>")
		return true;
	if( username.length<4 || username.length>16 ){
		$("#username_msg").html("用户名长度必须在4-16位之间");
		$("#signup-username").attr('class','f-input errorInput')
        $("#signup-username").focus();
        return false;
    }
    $.post("<?php echo WEB_ROOT; ?>/ajax/selfvalidator.php", {name:"username",value:username },function(data){
            if( data == 0 ){
                $("#username_msg").html("用户名已存在");
                $("#signup-username").attr('class','f-input errorInput')
                $("#signup-username").focus();
                return false;
            }
    });
	$("#username_msg").html("");
	$("#signup-username").attr('class','f-input')
	return true;
}
function check_password(){
	var re1= new RegExp("[a-zA-Z]");
    var re2= new RegExp("[0-9]");
    var password = $("#signup-password").val();
    if(!password){
        return true;
    }
    if( password.length<8 ||!re1.test(password)||!re2.test(password)){
        if( password.length<8 ){
            $("#pwd_msg").html("密码必须大于8位");
        }
        if(!re1.test(password)||!re2.test(password)){
            $("#pwd_msg").html("密码必须包含一位数字和和一位字母");
        }
        $("#signup-password").attr('class','f-input errorInput')
        $("#signup-password").focus();
        return false;
    }else{
        $("#pwd_msg").html("");
        $("#signup-password").attr('class','f-input')
        return true;
    }
}
function check_confirmpwd(){
	var re1= new RegExp("[a-zA-Z]");
    var re2= new RegExp("[0-9]");
	var confirmpwd = $("#signup-password-confirm").val();
	var password = $("#signup-password").val();
	if(!confirmpwd && !password)
		return true;
	if( confirmpwd.length<8 || !re1.test(confirmpwd) || !re2.test(confirmpwd) || confirmpwd != password){
        if(confirmpwd.length<8){
            $("#cpwd_msg").html("密码必须大于8位");
        }
        if(!re1.test(confirmpwd) || !re2.test(confirmpwd)){
            $("#cpwd_msg").html("密码必须包含一位数字和和一位字母");
        }
        if(confirmpwd != password){
            $("#cpwd_msg").html("重复密码和密码不一致");
        }
        $("#signup-password-confirm").attr('class','f-input errorInput');
        $("#signup-password-confirm").focus();
        return false;
    }else{
        $("#cpwd_msg").html("");
        $("#signup-password-confirm").attr('class','f-input')
        return true;
    }
}
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="signup">
	<div class="dashboard" id="dashboard">
		<ul><?php echo mcurrent_user($current_menu); ?></ul>
	</div>
    <div id="content" class="signup-box">
        <div class="box">
            <div class="box-content">
                <div class="sect">
                    <form id="signup-user-form" method="post"  onsubmit="return check_value();" class="validator">
                        <input type="hidden" name='id' value= '<?php echo $id;?>'/>
                        <div class="field username">
                            <label for="signup-username">用户名</label>
                            <input type="text" size="30" name="username" id="signup-username" class="f-input" value="<?php echo $action=='编辑'?$arr_edited_user['username']:""; ?>"  onkeyup="check_username()"/><span id="username_msg" style="color:red" ></span>
                            <span class="hint">填写4-16个字符，一个汉字为两个字符</span>
                        </div>
                        <div class="field password">
                            <label for="signup-password">密码</label>
                            <input type="password" size="30" name="password1" id="signup-password" class="f-input" onkeyup="check_password()"/><span id="pwd_msg" style="color:red" ></span>
                            <span class="hint">
                            <?php 
                            	if ($action == '编辑') 
                            		echo '如果不想修改密码，请保持空白;';
                            	else 
                            		echo '密码至少8位，且必须含有字母和数字';
                            ?>
                            </span>
                        </div>
                        <div class="field password">
                            <label for="signup-password-confirm">确认密码</label>
                            <input type="password" size="30" name="password2" id="signup-password-confirm" class="f-input" onkeyup="check_confirmpwd()"/><span id="cpwd_msg" style="color:red" ></span>
                        </div>
						<div class="field city">
                            <label id="enter-address-city-label" for="signup-city">用户权限</label>
							<select id="grade" name="grade" class="f-city">
                            	<?php 
									echo ''.Utility::Option(array(3=>'管理员权限',4=>'总部权限',1=>'城市权限',2=>'大区权限'),$arr_edited_user['grade']).''; 
							     ?>
							</select>
                        </div>
                        <div id="field_region" class="field city" <?php if($arr_edited_user['grade'] == 3 || $arr_edited_user['grade'] == 4 || empty($arr_edited_user)){?> style="display:none;"<?php }?>>
                            <label id="enter-address-city-label" for="signup-city">大区</label>
                            <select id="region" name="region" class="f-city">
                            <?php if($arr_edited_user['area_id'])
                                      echo ''.Utility::Option(get_region_option(),$arr_edited_user['area_id']).''; 
                                  else
                                      echo '<option></option>';
                            ?>
                            </select>
                        </div>
                        <div id="field_city" class="field city" <?php if($arr_edited_user['grade'] != 1 || empty($arr_edited_user)){?> style="display:none;"<?php }?>>
                            <label id="enter-address-city-label" for="signup-city">城市</label>  
                            <select id="city" name="city" class="f-city">
                            <?php if($arr_edited_user['city_id'])
                                      echo ''.Utility::Option(get_city_option(),$arr_edited_user['city_id']).'';
                                  else
                                      echo '<option></option>'; 
                            ?>
                            </select>
                        </div>
                        <div class="field city">
                            <label id="enter-address-city-label" for="signup-city"></label>
							<input type="submit" value="<?php echo $action;?>" name="commit" id="signup-submit" class="formbutton"/>
                        </div>
                        <div class="act">
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->

