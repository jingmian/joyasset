<?php
/*        PPK JoyAseet Swap Toolkit           */
/*         PPkPub.org  20190415           */  
/*    Released under the MIT License.     */
require_once "ppk_swap.inc.php";

$bid_rec_id=safeReqNumStr('bid_rec_id');

if(strlen($bid_rec_id)==0){
  echo '无效的纪录ID. Invalid record ID.';
  exit(-1);
}

$sqlstr = "SELECT bids.*,sells.seller_uri FROM bids,sells where sells.sell_rec_id=bids.sell_rec_id and bid_rec_id='$bid_rec_id';";
$rs = mysqli_query($g_dbLink,$sqlstr);
if (!$rs) {
  echo '指定纪录不存在. Not existed record.';
  exit(-1);  
}
$tmp_bid_record = mysqli_fetch_assoc($rs);
$sell_rec_id=$tmp_bid_record['sell_rec_id'];
$asset_id=$tmp_bid_record['asset_id'] ;
$full_odin_uri=$tmp_bid_record['full_odin_uri'] ;

$bCurrentUserIsSeller = ($g_currentUserODIN==$tmp_bid_record['seller_uri']) ? true:false;
$bCurrentUserIsBidder = ($g_currentUserODIN==$tmp_bid_record['bidder_uri']) ? true:false;

//$str_created_time = formatTimestampForView($tmp_user_info['block_time'],false);

require_once "page_header.inc.php";
?>
<div class="row section">
  <div class="form-group">
    <label for="top_buttons" class="col-sm-5 control-label"><h3>参拍报价单</h3></label>
    <div class="col-sm-7" id="top_buttons" align="right">
    </div>
  </div>
</div>

<form class="form-horizontal" action="bid_action.php" method="post" id="bid_form">
  <input type="hidden" name="bid_rec_id" value="<?php echo $bid_rec_id;?>">
  <input type="hidden" name="action_type" id="action_type" value="">

  <div class="form-group">
    <label for="asset_id" class="col-sm-2 control-label">标的比原资产ID</label>
    <div class="col-sm-10">
      <span id="asset_id"><a href="http://btmdemo.ppkpub.org:9888/dashboard/assets/<?php echo urlencode($asset_id);?>" target="_blank"><?php echo getSafeEchoTextToPage($asset_id);?></a></span>
    </div>
  </div>
  
  <div class="form-group">
    <label for="bidder_uri" class="col-sm-2 control-label">报价方身份标识</label>
    <div class="col-sm-10">
      <span id="bidder_uri"><a target="_blank" href="user.php?user_odin=<?php echo urlencode($tmp_bid_record['bidder_uri']);?>"><?php safeEchoTextToPage( $tmp_bid_record['bidder_uri'] );?></a></span>
    </div>
  </div>
  
  <div class="form-group">
    <label for="bid_amount" class="col-sm-2 control-label">报价金额</label>
    <div class="col-sm-10">
      <span id="bid_amount"><?php echo trimz($tmp_bid_record['bid_amount']);?> 比原币(BTM)</span>
    </div>
  </div>
  
  <div class="form-group">
    <label for="status_code" class="col-sm-2 control-label">状态</label>
    <div class="col-sm-10">
      <span id="status_code"><?php echo getStatusLabel($tmp_bid_record['status_code']);?></span>
    </div>
  </div>   

  
<?php
//只有对应拍卖方和投标者才能更新报价单
if($bCurrentUserIsSeller || $bCurrentUserIsBidder ){
    $tmp_bidder_info=getPubUserInfo($tmp_bid_record['bidder_uri']);
    $bidder_address=$tmp_bidder_info['register'];
    
    $tmp_owner_info=getPubUserInfo($tmp_bid_record['seller_uri']);
    $seller_address=$tmp_owner_info['register'];

?>
  <div class="form-group">
    <label for="bidder_address" class="col-sm-2 control-label">报价方比原地址</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" readonly  name="bidder_address" value="<?php safeEchoTextToPage( $bidder_address );?>" >
    </div>
  </div>
  
  <div class="form-group">
    <label for="remark" class="col-sm-2 control-label">报价说明<br></label>
    <div class="col-sm-10">
     <textarea class="form-control" id="remark" readonly rows=3 ><?php safeEchoTextToPage( $tmp_bid_record['remark'] );?></textarea>
    </div>
  </div>
  
  <div class="form-group">
    <label for="seller_address" class="col-sm-2 control-label">拍卖方比原地址</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" readonly  name="seller_address" value="<?php safeEchoTextToPage( $seller_address );?>" >
    </div>
  </div>

  <?php if($bCurrentUserIsSeller && $tmp_bid_record['status_code']==PPK_ODINSWAP_STATUS_BID){ ?>
  <div class="form-group" align="center">
    <a class="btn btn-warning" role="button" href="#" onclick="this.innerHTML='Waiting';this.disabled=true;doBidAction('accept');" >接受报价</a>
  </div>
  <?php } ?>
  
  <?php if($bCurrentUserIsSeller && $tmp_bid_record['status_code']==PPK_ODINSWAP_STATUS_PAY){ ?>
  <div class="form-group" align="center">
    <a class="btn btn-warning" role="button" href="bid_action_ok.php?bid_rec_id=<?php echo $bid_rec_id;?>&action_type=transfer" >已转移资产拥有权</a>
  </div>
  <?php } ?>
  
  <?php if($bCurrentUserIsBidder && $tmp_bid_record['status_code']==PPK_ODINSWAP_STATUS_ACCEPT){ ?>
  <div class="form-group" align="center">
    <a class="btn btn-danger" role="button" href="#" onclick="this.innerHTML='Waiting';this.disabled=true;doBidAction('pay');" >现在付款给拍卖方</a>
  </div>
  <?php } ?>
  
  <?php if($bCurrentUserIsBidder && $tmp_bid_record['status_code']==PPK_ODINSWAP_STATUS_TRANSFER){ ?>
  <div class="form-group" align="center">
    <a class="btn btn-warning" role="button" href="bid_action_ok.php?bid_rec_id=<?php echo $bid_rec_id;?>&action_type=finish" >确认收到资产，交易完成</a>
  </div>
  <?php 
  } 
}
?>

  <h3 align=center>相关链上存证信息</h3>
  <div class="form-group">
    <label for="accepted_txid" class="col-sm-2 control-label">拍卖方确认接受报价的记录</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" readonly  name="accepted_txid" value="<?php safeEchoTextToPage( $tmp_bid_record['accepted_txid'] );?>" >
      <br><a target="_blank" href="http://btmdemo.ppkpub.org:9888/dashboard/transactions/<?php echo urldecode($tmp_bid_record['accepted_txid']);?>">查看对应的链上存证信息</a>
    </div>
  </div>
  <div class="form-group">
    <label for="payment_txid" class="col-sm-2 control-label">报价方确认付款的记录</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" readonly  name="payment_txid" value="<?php echo $tmp_bid_record['payment_txid'];?>" >
      <br><a target="_blank" href="http://btmdemo.ppkpub.org:9888/dashboard/transactions/<?php echo urlencode($tmp_bid_record['payment_txid']);?>">查看对应的链上存证信息</a>
    </div>
  </div>
</form>

<p align=center><a href="sell.php?sell_rec_id=<?php echo $sell_rec_id;?>">点击返回所属拍卖纪录 [<?php safeEchoTextToPage( friendlyLongID($asset_id) );?>]</a></p>


<script type="text/javascript">

function doBidAction(action_type){
    document.getElementById("action_type").value=action_type;
    document.getElementById("bid_form").submit();
}
</script>
<?php
require_once "page_footer.inc.php";
?>