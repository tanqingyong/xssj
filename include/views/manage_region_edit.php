<?php 
$id = $arr_var['id'];
$region = $arr_var['region'];
$action = $arr_var['action'];
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
                <div class="sect">
                    <form id="region-form" method="post"  class="validator">
                        <input type="hidden" name='id' value="<?php echo $id;?>"/>
                        <input type="hidden" name="old_area" value="<?php echo $region['name'];?>"/>
                        <div class="field">
                            <label for="name">中文名称</label>
                            <input type="text" size="30" name="name" id="name" class="f-input" value="<?php echo $region['name']; ?>"/>
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

