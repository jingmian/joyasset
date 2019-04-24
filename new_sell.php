<?php
/*      PPK JoyAseet Swap Toolkit         */
/*         PPkPub.org  20190415           */  
/*    Released under the MIT License.     */
require_once "ppk_swap.inc.php";

$asset_id=safeReqChrStr('asset_id');
/*
if(strlen($asset_id)==0){
  echo '无效的资产标识. Invalid Asset ID.';
  exit(-1);
}

$tmp_data=getBytomAssetData($asset_id);

if($tmp_data['status_code']!=200){
  echo '获取比原资产标识资源信息出错. Failed to get Asset data.';
  exit(-1);
}
$full_odin_uri=$tmp_data['uri'];
$tmp_asset_info=@json_decode($tmp_data['content'],true);
        
//$str_created_time = formatTimestampForView($tmp_user_info['block_time'],false);
*/
require_once "coinprice.inc.php";
require_once "page_header.inc.php";
?>
<div class="row section">
  <div class="form-group">
    <label for="top_buttons" class="col-sm-5 control-label"><h3>发布拍卖比原数字资产</h3></label>
    <div class="col-sm-7" id="top_buttons" align="right">
    </div>
  </div>
</div>
<!--资产记录信息
<ul>
<li>资产ID : <?php safeEchoTextToPage( $asset_id);?></li>
<li>资产名称 : <?php safeEchoTextToPage( $tmp_asset_info['name']);?></li>
<li>资产代号 : <?php safeEchoTextToPage( $tmp_asset_info['symbol']);?></li>
<li>资产URI: <?php safeEchoTextToPage( $full_odin_uri);?></li>
<li>详细说明 : <?php safeEchoTextToPage( $tmp_asset_info['description']);?></li>
</ul>
-->
<?php
//检查该标识是否已在拍卖中
$sqlstr = "SELECT sells.sell_rec_id FROM sells where asset_id='$asset_id' and seller_uri='$g_currentUserODIN';";
$rs = mysqli_query($g_dbLink,$sqlstr);
if (!$rs) {
  echo '数据库查询有误，请稍候再试. DB failed,Please retry later!';
  exit(-1);  
}

$row= mysqli_fetch_assoc($rs);
if ($row) {
  echo '指定资产标识已有拍卖记录[<a href="sell.php?sell_rec_id=',$row['sell_rec_id'],'">',$row['sell_rec_id'],'</a>]，不能重复发布. Existed same sell record.';
  exit(-1);  
}

//检查有权拍卖该标识
//demo忽略
//$tmp_user_info=getPubUserInfo($g_currentUserODIN);

//if($tmp_user_info['register']!= $tmp_asset_info['register']){
//  echo '当前用户无权拍卖不属于自己的ODIN标识. Unable to sell ODIN belong to others.';
//  exit(-1);
//}

?>

<form class="form-horizontal" action="new_sell_confirm.php" method="post">
  <input type="hidden" name="form" value="new_sell">

  <div class="form-group">
    <label for="seller_odin" class="col-sm-2 control-label">发布者身份标识</label>
    <div class="col-sm-10">
      <span id="seller_odin"><?php safeEchoTextToPage( $g_currentUserODIN );?></span>
    </div>
  </div> 
  
  <div class="form-group">
    <label for="asset_id" class="col-sm-2 control-label">比原资产ID</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" placeholder="请输入比原数字资产的发行ID" name="asset_id" id="asset_id" value="<?php safeEchoTextToPage( $asset_id );?>">
      <br><a href="http://btmdemo.ppkpub.org:9888/" target="_blank">可以通过比原测试网客户端或钱包来创建自己的数字资产...</a>
    </div>
  </div>    
  
  <div class="form-group">
    <label for="recommend_names" class="col-sm-2 control-label">资产名称</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" placeholder="该数字资产的代称" name="recommend_names" id="recommend_names" value="">
    </div>
  </div>  
  
  <div class="form-group">
    <label for="coin_type" class="col-sm-2 control-label">出价币种</label>
    <div class="col-sm-10">
      <select class="form-control" name="coin_type" id="coin_type">
          <option value="BTM" selected="selected">比原币(BTM)</option>
          <!--<option value="ppk:bch/">比特现金</option>-->
      </select>
    </div>
  </div>
  
  <div class="form-group">
    <label for="start_amount" class="col-sm-2 control-label">起始报价</label>
    <div class="col-sm-10">
      <input type="text" class="form-control"  name="start_amount" id="start_amount" value="0.00"  onchange="updateRmbValue();" ><br>
      <font size="-1">约 ¥<span id='start_rmb_value'><?php echo ceil(0*$gArrayCoinPriceUSD['BTM']*$gFiatRateRmbUsd);?></span>元</font>（不填写或输入0表示无底价拍卖）
    </div>
  </div>
  
  <div class="form-group">
    <label for="remark" class="col-sm-2 control-label">详细说明</label>
    <div class="col-sm-10">
     <textarea class="form-control" name="remark" id="remark" rows=10 ></textarea>
     <span>可以填写对该数字资产的描述说明、拍卖方的联系方式如Email/微信/Telegram等。</span>
    </div>
  </div>
  
  <div class="form-group">
    <label for="bid_hours" class="col-sm-2 control-label">竞拍持续时间</label>
    <div class="col-sm-10">
      <select class="form-control" name="bid_hours" id="bid_hours">
          <option value="1">1小时</option>
          <option value="3">3小时</option>
          <option value="6">6小时</option>
          <option value="24">1天</option>
          <option value="48">2天</option>
          <option value="72">3天</option>
          <option value="168">1周（7天）</option>
          <option value="720" selected="selected">1个月（30天）</option>
          <option value="8760" >1年（365天）</option>
          <!--<option value="0">长期，可由拍卖方提前结束</option>-->
      </select>
    </div>
  </div>
  
  <div class="form-group" align="center">
    <div class="col-sm-offset-2 col-sm-10">
      <button class="btn btn-success btn-lg" type="submit"  onclick="this.innerHTML='Waiting';this.disabled=true;form.submit();" disabled2="disabled" >马上发布</button>
    </div>
  </div>

</form>

<script type="text/javascript">
function updateRmbValue(){
    var btc_value=document.getElementById("start_amount").value;
    document.getElementById("start_rmb_value").innerHTML= Math.ceil( btc_value * <?php echo $gArrayCoinPriceUSD['BTM']*$gFiatRateRmbUsd;?>);
}
</script>
<?php
require_once "page_footer.inc.php";
?>