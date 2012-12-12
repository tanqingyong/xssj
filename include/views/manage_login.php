<script type="text/javascript">

function change_captcha(){
	$("#captcha_img").attr("src","<?php echo WEB_ROOT; ?>/manage/captcha.php?"+Math.random(1));
}

</script>
<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="manage">
    <div id="content" class="manage">
        <div class="box">
            <div class="box-content">
                                <div class="head"><h2>登录</h2></div>
                <div class="sect">
                    <form id="manage-user-form" method="post" action="/manage/login.php" class="validator">
                        <div class="field">
                            <label for="manage-login">登录名</label>
                            <input type="text" size="30" name="username" id="manage-username" class="f-input" datatype="require" require="true" />
                        </div>
                        <div class="field">
                            <label for="manage-password">密码</label>
                            <input type="password" size="30" name="password" id="manage-password" class="f-input" datatype="require" require="true" />
                        </div>
                        <div class="field">
                           <label for="manage-captcha">验证码</label>
                            <input type="text" name="captcha" id="manage-captcha" style="width:60px;" datatype="require" require="true" /> <img id="captcha_img" src="/manage/captcha.php"/>
                            <a href="javascript:void(0)" onclick="change_captcha()">看不清，换一张</a>
                        </div>
                        <div class="field">
                            <label for="manage-password"></label>
                            <input type="submit" value="登录" name="commit" id="login-submit" class="formbutton"/>
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

