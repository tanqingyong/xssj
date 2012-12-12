<?php
$datas = $arr_var['datas'];
$pagestring = $arr_var['pagestring'];
$filter_goods = $arr_var['filter_goods'];
$filter_date_start = $arr_var['filter_date_start'];
$filter_date_end = $arr_var['filter_date_end'];
$filter_city = $arr_var['filter_city'];
$filter_region = $arr_var['filter_region']; 
$page_size = $arr_var['page_size'];
$cate = $arr_var['cate'];
?>
<script type="text/javascript">
function cha_xun(){
    $("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/dataanalysis/detail.php');
    $("form:first").submit();
}
function GetParams() {
    var url = location.search; 
    var theRequest = new Array();
    var return_str = "";
    var pagesize = 0;
    if (url.indexOf("?") != -1) {
       var str = url.substr(1);
       strs = str.split("&");
       for(var i = 0; i < strs.length; i ++) {
          theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);
          if(strs[i].split("=")[0]=='pagesize'){
              theRequest[strs[i].split("=")[0]]=$("#pagesize").val(); 
              pagesize=1;
          }
          if(i==strs.length-1){
              return_str +=strs[i].split("=")[0]+'='+theRequest[strs[i].split("=")[0]];
          }else{
              return_str +=strs[i].split("=")[0]+'='+theRequest[strs[i].split("=")[0]]+'&';
          }
       }
    }
    if(pagesize==0){
        if(return_str)
            return_str +="&pagesize="+$("#pagesize").val(); 
        else 
            return_str +="pagesize="+$("#pagesize").val(); 
    }
    return return_str;
 }

function change_pagesize(){
    var pagesize = $("#pagesize").val();
    var url_arr = window.location.href.split('?');
    var url = url_arr[0];
    window.location.href=url+'?'+GetParams();
 
}
function export_excel(){
    $("form:first").attr('action','<?php echo WEB_ROOT;?>/manage/dataanalysis/export_detail.php');
    $("form:first").submit();
}
$(document).ready(function(){
    $("#region").change(function(){
        var region = $("#region").val();
        $.post("<?php echo WEB_ROOT; ?>/ajax/getcities.php", { region_id : region, pagefrom : "data"},function(data){
            $("#city").html(data);
        });
    });
});
</script>

<div id="bdw" class="bdw">
<div id="bd" class="cf">
<div id="coupons">
    <div class="dashboard" id="dashboard">
        <ul><?php echo mcurrent_dataanalysis('detail'); ?></ul>
    </div>
    <div id="content" class="signup-box">
        <div class="box clear">
            <div class="box-content">
            <div class="chaxun_box">
             <form name='data_summary_form' method='get'><br></br>
                  <div class="chaxun_boxt">
                   <dl>
                        <dt>按天查询：</dt>
				          <dd><input type="text" style="width:87px" onfocus="WdatePicker()" name="stratdate" id="stratdate" 
				        value='<?php
				        echo $filter_date_start;
				        ?>'/> --
				        <input type="text" style="width:87px;" onfocus="WdatePicker()" name="enddate" id="enddate" 
				        value='<?php
				        echo $filter_date_end;
				        ?>'/></dd>   
                    </dl>
                    <dl>
                        <dt>产品ID：</dt>
                        <dd><input type="text" name="filter_goods" class="h-input" value="<?php echo $filter_goods; ?>" >&nbsp;</dd>
                    </dl>
                   
                    <?php if($_SESSION['grade']!=1 && $_SESSION['grade']!=2){?>
                   <dl>
                        <dt>大区：</dt>
                        <dd><select id="region" name="region" class="se_ect1">
                              <?php if(!$filter_region){?>
                                <option></option>
                               <?php  echo ''.Utility::Option(get_region_option(),$filter_region).''; ?>
                               <?php }else{
                                        echo ''.Utility::Option(get_region_option(),$filter_region).'';
                                        echo '<option>全国</option>'; 
                                     }
                               ?>
                            </select>
                        </dd>
                    </dl>
                    <?php }
                          if($_SESSION['grade']!=1 ){
                    ?>
                    <dl>
                        <dt>城市：</dt>
                        <dd><select id="city" name="city" class="se_ect1">
                                 <?php if(!$filter_city){?>
                                 <option></option>
                                 <?php echo ''.Utility::Option(get_cities_by_region_id($filter_region?$filter_region:$_SESSION['area_id']),$filter_city).'';?>
                                 <?php }else{ 
                                       echo ''.Utility::Option(get_cities_by_region_id($filter_region?$filter_region:$_SESSION['area_id']),$filter_city).'';
                                       echo '<option>整个大区</option>';  
                                 }?>
                            </select>
                        </dd>
                    </dl>
                    <?php }?>
                    <dl>
                        <dt>类别：</dt>
                        <dd><input name="cate" id="cate" value="<?php echo $cate;?>" /> 
                        </dd>
                    </dl>
                  </div>
                    <p style="width:250px;">
                        <a href="javascript:void(0);" onclick="cha_xun();">查询</a>
                        <a href="javascript:void(0)"  onclick="export_excel();">导出</a>
                    </p>
                  </form>
                     <h1>查询结果</h1>    
                  <div class="chaxun_over">
        <div class="div_ta" > 
        <!--如出现表格内没有内容，请用"&nbsp;"填充-->
        <table cellspacing="0" cellpadding="0">
            <tbody>
            <tr><th width="">日期</th><th width="">大区</th><th width="">城市</th><th width="">产品ID</th><th>产品名称</th><th width="">一级类目</th><th width="">二级类目</th><th width="">产品详情页访问数</th><th width="">下单数</th><th width="">下单商品数量</th><th width="">支付订单数</th><th width="">支付商品数量</th><th>独立购买用户数</th><th width="">销售额</th></tr>
            <?php foreach($datas as $data){?>
            <tr>
                <td><?php echo $data['date'];?></td>
                <td><?php echo $data['regionname'];?></td>
                <td><?php echo $data['incity'];?></td>
                <td><?php echo $data['goodsid'];?></td>
                <td><?php echo $data['goodsname'];?></td> 
                <td><?php echo $data['type1'];?></td>
                <td><?php echo $data['firstcategoryname'];?></td>
                <td><?php echo $data['uv'];?></td>
                <td><?php echo $data['ordernum'];?></td>
                <td><?php echo $data['orderproductnum'];?></td>
                <td><?php echo $data['offordernum'];?></td>
                <td><?php echo $data['offorderproductnum'];?></td>
                <td><?php echo $data['offorderusernum'];?></td>
                <td><?php echo $data['offordersale'];?></td>
            </tr>
            <?php }?>
            <?php if(!$datas){?>
            <tr><td  colspan='13'>没有数据</td></tr>
            <?php }?>
        </tbody></table>
        </div>
    </div>
         </div>
           <div class="sect">
                    <table width ="948px"id="orders-list" cellspacing="0" cellpadding="0" border="0" class="coupons-table">
                    
                    <tr>
                        <td style="text-align:right;">
                                                                    每页显示数<select id="pagesize" name="pagesize" class="se_ect1" onchange="change_pagesize();">
                                <?php echo Utility::Option(array(10=>'10',20=>'20',30=>'30',40=>'40', 50=>'50'),$page_size); ?>
                            </select>
                        </td>
                        <td width="400px"><?php echo $pagestring; ?></tr>
                    </table>
                </div>
           
              </div>
        </div>
    </div>
</div>
</div> <!-- bd end -->
</div> <!-- bdw end -->