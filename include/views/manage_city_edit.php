<?php
$id = $arr_var['id'];
$city = $arr_var['city'];
$action = $arr_var['action'];
?>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="signup">
	<div class="dashboard" id="dashboard">
		<ul><?php echo mcurrent_region('city_list'); ?></ul>
	</div>
    <div id="content" class="signup-box">
        <div class="box">
            <div class="box-content">
                <div class="sect">
                    <form id="region-form" method="post"  class="validator">
                        <input type="hidden" name='id' value="<?php echo $id;?>"/>
                        <input type="hidden" name="old_area" value="<?php echo $city['area_id'];?>"/>
                        <input type="hidden" name="old_city" value="<?php echo $city['name'];?>"/>
                        <div id="field_region" class="field">
                            <label id="enter-address-city-label" for="signup-city">大区</label>
                            <select id="area_id" name="area_id" class="f-city">
                            <option value=0></option>
                            <?php  echo ''.Utility::Option(get_region_option(),$city['area_id']).''; ?>
                            </select>
                        </div>
                        <div class="field">
                            <label for="name">中文名称</label>
                            <input type="text" size="30" name="name" id="name" class="f-input" value="<?php echo $city['name']; ?>"/>
                            <span class="hint">中文名称不能重复</span>
                        </div>
                        <div class="field">
                            <label for="signup-city"></label>
							<input type="submit" value="<?php echo $action;?>" name="commit" id="signup-submit" class="formbutton"/>
                        	<input type="button" value="返回" name="cancel" id="cancel" class="formbutton" onclick="javascript:history.go(-1);"/>
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

