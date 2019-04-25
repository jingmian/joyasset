<?php
/*        PPK JoyAseet Swap Toolkit           */
/*         PPkPub.org  20190415           */  
/*    Released under the MIT License.     */
require_once "ppk_swap.inc.php";

if(strlen($g_currentUserODIN)==0){
  Header('Location: login.php');
  exit(-1);
}

$bid_rec_id=safeReqNumStr('bid_rec_id');
$action_type=safeReqChrStr('action_type');
$seller_address=safeReqChrStr('seller_address');
$bidder_address=safeReqChrStr('bidder_address');
$service_uri=PPK_ODINSWAP_SERVICE_URI_PREFIX.'bid/'.$bid_rec_id.'#';

if(strlen($bid_rec_id)==0){
  echo '无效的纪录ID. Invalid record ID.';
  exit(-1);
}

if(strlen($action_type)==0){
  echo '无效的操作类型. Invalid action_type.';
  exit(-1);
}


$sqlstr = "SELECT bids.*,sells.seller_uri FROM bids,sells where sells.sell_rec_id=bids.sell_rec_id and bid_rec_id='$bid_rec_id';";
$rs = mysqli_query($g_dbLink,$sqlstr);
if (!$rs) {
  echo '指定纪录不存在. Not existed record.';
  exit(-1);  
}
$tmp_bid_record = mysqli_fetch_assoc($rs);
$asset_id=$tmp_bid_record['asset_id'] ;
$full_odin_uri=$tmp_bid_record['full_odin_uri'] ;
$coin_type=$tmp_bid_record['coin_type'] ;
$bid_amount=$tmp_bid_record['bid_amount'] ;

$bCurrentUserIsSeller = ($g_currentUserODIN==$tmp_bid_record['seller_uri']) ? true:false;
$bCurrentUserIsBidder = ($g_currentUserODIN==$tmp_bid_record['bidder_uri']) ? true:false;

if($action_type=='accept'){
    if( !$bCurrentUserIsSeller ){
      echo '只有拍卖方才能确认接受报价单. Only seller can accpet bid.';
      exit(-1);
    }
    
    //组织交易信息
    $array_witness_data=genAcceptBidArray($tmp_bid_record['seller_uri'], $tmp_bid_record['bidder_uri'], $asset_id,$full_odin_uri,$coin_type,$bid_amount,$service_uri);

}else if($action_type=='pay'){
    if( !($bCurrentUserIsBidder && ($tmp_bid_record['status_code']==PPK_ODINSWAP_STATUS_ACCEPT||$tmp_bid_record['status_code']==PPK_ODINSWAP_STATUS_PAY) ) ) {
      echo '只有已被接受的报价方才能确认付款. Only accepted bidder can pay for the bid.';
      exit(-1);
    }
    
    //组织交易信息
    $array_witness_data=genPayBidArray($tmp_bid_record['bidder_uri'],$tmp_bid_record['seller_uri'],  $asset_id,$full_odin_uri,$coin_type,$bid_amount,$service_uri);
}else{
    echo '无效的操作类型. Invalid action_type.';
    exit(-1);
}

$odin_tx_json_hex = strToHex(json_encode($array_witness_data));

require_once "page_header.inc.php";
?>
<div class="row section">
  <div class="form-group">
    <label for="top_buttons" class="col-sm-5 control-label"><h3>交易比原资产[<?php safeEchoTextToPage( $asset_id );?>]</h3></label>
    <div class="col-sm-7" id="top_buttons" align="right">
    </div>
  </div>
</div>

<form class="form-horizontal" >
<div class="form-group">
    <label for="bidder_address" class="col-sm-2 control-label">报价方比原地址</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="bidder_address"  id="bidder_address" value="<?php safeEchoTextToPage( $bidder_address );?>">
    </div>
</div>

<div class="form-group">
    <label for="bid_data_desc" class="col-sm-2 control-label">待存证到比原链上的信息</label>
    <div class="col-sm-10">
      <textarea class="form-control" rows=3 cols=100 id="bid_data_desc"><?php safeEchoTextToPage( $array_witness_data['data'] );?></textarea>
    </div>
</div>

<div class="form-group">
    <label for="seller_address" class="col-sm-2 control-label">拍卖方比原地址</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="seller_address"  id="seller_address" value="<?php safeEchoTextToPage( $seller_address );?>">
    </div>
</div>

<div class="form-group" align="center">
<div class="col-sm-offset-2 col-sm-10">
  <button id='send_trans_btn' class="btn btn-warning" type="button"  onclick="sendTX();return false;"  >确认并发送到比原链上公开存证</button><br>
  <!--注：确认报价或支付并存证到链上会花费若干交易费用，并需要PPkBrowser安卓版0.302以上支持，<a href="https://ppkpub.github.io/docs/DOC_PPk_Browser_Tutorial.pdf">请点击阅读这里的操作说明安装和使用</a>-->
</div>
</div>
</form>

<input type="hidden" id="game_trans_fee_btm" value="<?php echo TX_GAS_AMOUNT_mBTM/1000; ?>" >


<form class="form-horizontal" action="bid_action_ok.php" method="post" id="bid_action_form">
<input type="hidden" name="bid_rec_id" value="<?php echo $bid_rec_id;?>">
<input type="hidden" name="action_type" id="action_type"  value="<?php echo $action_type;?>">
<div class="form-group">
    <label for="signed_txid" class="col-sm-2 control-label">已发出的存证交易编号(TXID)</label>
    <div class="col-sm-10">
        <textarea class="form-control" rows=1 cols=100 name="signed_txid" id="signed_txid"></textarea>
    </div>
</div>

<input type="hidden" id="signed_tx_hex" value="">

<div class="form-group" align="center">
<div class="col-sm-offset-2 col-sm-10">
  <a class="btn btn-warning" role="button" href="#" onclick="doActionOK();return false;">自行更新存证交易记录</a>
</div>
</div>

</form>

<script src="js/common_func.js"></script>
<script type="text/javascript">
window.onload=function(){
    init();
}

function init(){
    console.log("init...");

    //需要钱包插件进行签名
    if(typeof(PeerWeb) !== 'undefined'){
        console.log("PeerWeb enabled");
    }else{
        console.log("PeerWeb not valid");
        document.getElementById("signed_tx_hex").value="PeerWeb extension not valid. Please visit by PPk Browser For Android v0.3.2 above.";
    }
    
}

function sendTX(){
  var pub_trans_data_hex = stringToHex(document.getElementById('bid_data_desc').value);

  document.getElementById("send_trans_btn").disabled=true;
  document.getElementById("send_trans_btn").value="正在自动生成用户标识,请稍候...";
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET","send_tx.php?pub_trans_data_hex="+pub_trans_data_hex);
  xmlhttp.send();
  xmlhttp.onreadystatechange=function()
  {
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
      document.getElementById("send_trans_btn").value=" 确认发布到比原链上 ";
      document.getElementById("send_trans_btn").disabled=false;
      console.log(xmlhttp.responseText);
      var obj_result = JSON.parse(xmlhttp.responseText);
      if( obj_result!=null && obj_result.status=='success'){
        document.getElementById("signed_txid").value=obj_result.data.tx_id;
        doActionOK();
      }else{
        alert("出错了！\n"+xmlhttp.responseText);
      }
    }
  }
  
    
}

function doActionOK(){
    var signed_txid = document.getElementById("signed_txid").value;
    if(signed_txid.length>0){
        document.getElementById("bid_action_form").submit();
    }else{
        alert("请输入有效的链上存证交易编号！");
    }
}


</script>
<?php
require_once "page_footer.inc.php";
?>