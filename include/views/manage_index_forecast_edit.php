<?php
$datas = $arr_var['datas'];
$action = $arr_var['action'];
?>
<script type="text/javascript">
function check_value(){
	var pre_sale_amount = $('#pre_sale_amount').val();
	var pre_positive_profile = $('#pre_positive_profile').val();
	var pre_lose_profile = $('#pre_lose_profile').val();
	var pre_advance_payment = $('#pre_advance_payment').val();
	if( pre_sale_amount == "" || parseInt(pre_sale_amount) <= 0 || isNaN(pre_sale_amount)){
		$('#pre_sale_amount').attr('class','f-input errorInput');
		$('#pre_sale_amount').focus();
		$("#pre_sale_amount_msg").html("请输入正确的销售额");
		return false;
	}
	if( pre_positive_profile == "" || parseInt(pre_positive_profile) < 0 || isNaN(pre_positive_profile)){
		$('#pre_positive_profile').focus();
		$('#pre_positive_profile').attr('class','f-input errorInput');
		$("#pre_positive_profile_msg").html("请输入正确的正毛利");
		return false;
	}
	if( pre_lose_profile == "" || parseInt(pre_lose_profile) < 0 || isNaN(pre_lose_profile)){
		$('#pre_lose_profile').focus();
		$('#pre_lose_profile').attr('class','f-input errorInput');
		$("#pre_lose_profile_msg").html("请输入正确的负毛利");
		return false;
	}
	if( pre_advance_payment == "" || parseInt(pre_advance_payment) < 0 || isNaN(pre_advance_payment)){
		$('#pre_advance_payment').focus();
		$('#pre_advance_payment').attr('class','f-input errorInput');
		$("#pre_advance_payment_msg").html("请输入正确的预付款");
		return false;
	}
	$("#pre_sale_amount_msg").html("");
	$("#pre_positive_profile_msg").html("");
	$("#pre_lose_profile_msg").html("");
	$("#pre_positive_profile_msg").html("");
	return true;
}
function check_input(idName, msg){
	var value = $("#"+idName).val();
	if( value == "" || (idName == "pre_sale_amount"&&parseInt(value) <=0)||(idName != "pre_sale_amount"&&parseInt(value) <0)|| isNaN(value)){
		$('#'+idName).attr('class','f-input errorInput');
		$('#'+idName).focus();
		$('#'+idName+'_msg').html(msg);
		return false;
	}else{
		$('#'+idName).attr('class','f-input');
		$('#'+idName+'_msg').html("");
	}
	return true;
}
function auto_change_data(){
	var sale_amount = $("#pre_sale_amount").val()?$("#pre_sale_amount").val():0;
	var positive_profile = $("#pre_positive_profile").val()?$("#pre_positive_profile").val():0;
	var lose_profile = $("#pre_lose_profile").val()?$("#pre_lose_profile").val():0;
	if( positive_profile>=0 && lose_profile>=0 && !isNaN(lose_profile) && !isNaN(positive_profile) ){
		var profile = Math.round((positive_profile-lose_profile)*100)/100;
		$("#pre_profile").val(profile);
	}else{
		$("#pre_profile").val("");
	}
	if(sale_amount>0 && positive_profile>=0 && lose_profile>=0 && !isNaN(sale_amount) && !isNaN(lose_profile) && !isNaN(positive_profile)){
	    var profile_rate = Math.round((profile/sale_amount)*10000)/100;
	    $("#pre_profile_rate").val(profile_rate+"%");
	}else{
		$("#pre_profile_rate").val("");
	}  
}
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="signup">
	<div class="dashboard" id="dashboard">
		<ul><?php echo mcurrent_sixindex('index_forecast'); ?></ul>
	</div>
    <div id="content" class="signup-box">
        <div class="box">
            <div class="box-content">
                <div class="sect">
                   <?php if($action == 'edit' && !$datas){?>
                    <span>对应预测数据不存在</span>
                   <?php }else{?>
                    <form id="signup-user-form" method="post"  onsubmit="return check_value();" class="validator">
                        <input type="hidden" name='id' value= '<?php echo $id;?>'/>
                         <div class="field">
                            <label for="signup-username">月份</label>
                            <input type="text" size="30" name="indicator_date" id="indicator_date" class="f-input" style="background-color:#BABABA;" value="<?php echo get_six_indicator_month($datas["indicator_date"]);?>" readonly="readonly";/>
                        </div>
                        <div class="field">
                            <label for="signup-username">大区</label>
                            <input type="text" size="30" name="region" id="region" class="f-input" style="background-color:#BABABA;" value="<?php echo $datas["region"];?>" readonly="readonly";/>
                        </div>
                        <div class="field">
                            <label for="signup-username">城市</label>
                            <input type="text" size="30" name="city" id="city" class="f-input" style="background-color:#BABABA;" value="<?php echo $datas["city"];?>" readonly="readonly";/>
                        </div>
                        <div class="field">
                            <label for="signup-password">预测销售额</label>
                            <input type="text" size="30" name="pre_sale_amount" id="pre_sale_amount" value="<?php echo floatval($datas["pre_sale_amount"]);?>" onkeyup="check_input('pre_sale_amount','请输入正确的销售额');" onblur="auto_change_data();" class="f-input"/><span id="pre_sale_amount_msg" style="color:red" ></span>
                        </div>
                        <div class="field">
                            <label for="signup-password-confirm">预测正毛利</label>
                            <input type="text" size="30" name="pre_positive_profile" id="pre_positive_profile" value="<?php echo floatval($datas["pre_positive_profile"]);?>" onkeyup="check_input('pre_positive_profile','请输入正确的正毛利');" onblur="auto_change_data();"  class="f-input""/><span id="pre_positive_profile_msg" style="color:red" ></span>
                        </div>
                        <div class="field">
                            <label for="signup-password-confirm">预测负毛利</label>
                            <input type="text" size="30" name="pre_lose_profile" id="pre_lose_profile" value="<?php echo floatval($datas["pre_lose_profile"]);?>" onkeyup="check_input('pre_lose_profile','请输入正确的负毛利')" onblur="auto_change_data();" class="f-input""/><span id="pre_lose_profile_msg" style="color:red" ></span>
                        </div>
                        <div class="field">
                            <label for="signup-password-confirm">预测毛利</label>
                            <input type="text" size="30" name="pre_profile" id="pre_profile" value="<?php echo floatval($datas["pre_profile"]);?>" style="background-color:#BABABA;" readonly="readonly" class="f-input""/>
                        </div>
                        <div class="field">
                            <label for="signup-password-confirm">预测毛利率</label>
                            <input type="text" size="30" name="pre_profile_rate" id="pre_profile_rate" value="<?php echo floatval($datas["pre_profile_rate"]);?>%" style="background-color:#BABABA;" readonly="readonly" class="f-input""/>
                        </div>
                        <div class="field">
                            <label for="signup-password-confirm">预测预付款</label>
                            <input type="text" size="30" name="pre_advance_payment" id="pre_advance_payment" value="<?php echo floatval($datas["pre_advance_payment"]);?>" onkeyup="check_input('pre_advance_payment','请输入正确的预付款')" class="f-input""/><span id="pre_advance_payment_msg" style="color:red" ></span>
                        </div>
                        <div class="field city">
                            <label id="enter-address-city-label" for="signup-city"></label>
							<input type="submit" value="提交" name="commit" id="signup-submit" class="formbutton"/>
                        </div>
                        <div class="act">
                            
                        </div>
                    </form>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>

</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->