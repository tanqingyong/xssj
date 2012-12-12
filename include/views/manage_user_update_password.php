<?php 
$arr_user = $arr_var['user'];
?>
<script type="text/javascript">
<!--
function check_value(){
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
function check_password(){
    var re1= new RegExp("[a-zA-Z]");
    var re2= new RegExp("[0-9]");
    var password = $("#signup-password").val();
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
//-->
</script>
<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="signup">
	<div class="dashboard" id="dashboard">
		<ul><?php echo mcurrent_user('update_password'); ?></ul>
	</div>
    <div id="content" class="signup-box">
        <div class="box">
            <div class="box-content">
                <div class="sect">
                    <form id="signup-user-form" method="post" action="/manage/user/update_password.php" onsubmit="return check_value();" class="validator">
                       
                        <div class="field username">
                            <label for="signup-username">用户名</label>
                            <input type="text" size="30" name="username" id="signup-username" class="f-input" disabled="disabled" value="<?php echo $arr_user['username']; ?>"  />
                        </div>
                        <div class="field password">
                            <label for="signup-password">密码</label>
                            <input type="password" size="30" name="password1" id="signup-password" class="f-input" onkeyup="check_password()"/><span id="pwd_msg" style="color:red" ></span>
                            <span class="hint">为了您的帐号安全，密码最少设置为8个字符，且至少包含一个数字和一个字母</span>
                        </div>
                        <div class="field password">
                            <label for="signup-password-confirm">确认密码</label>
                            <input type="password" size="30" name="password2" id="signup-password-confirm" class="f-input" onkeyup="check_confirmpwd()"/><span id="cpwd_msg" style="color:red" ></span>
                        </div>
                        <div class="field password">
                            <label for="signup-password-confirm"></label>
                            <input type="submit" value="修改" name="commit" id="signup-submit" class="formbutton" />
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

