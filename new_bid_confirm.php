<?php
/*        PPK JoyAseet Swap Toolkit           */
/*         PPkPub.org  20190415           */  
/*    Released under the MIT License.     */
require_once "ppk_swap.inc.php";

$sell_rec_id=safeReqNumStr('sell_rec_id');
$coin_type=safeReqChrStr('coin_type');
$bid_amount=safeReqNumStr('bid_amount');

if(strlen($sell_rec_id)==0){
  echo '无效的纪录ID. Invalid record ID.';
  exit(-1);
}

$sqlstr = "SELECT * FROM sells where sell_rec_id='$sell_rec_id';";
$rs = mysqli_query($g_dbLink,$sqlstr);
if (!$rs) {
  echo '指定纪录不存在. Not existed record.';
  exit(-1);  
}
$tmp_sell_record = mysqli_fetch_assoc($rs);
$asset_id=$tmp_sell_record['asset_id'] ;
$full_odin_uri=$tmp_sell_record['full_odin_uri'] ;

//检查被允许参拍该标识
if($g_currentUserODIN == $tmp_sell_record['seller_uri']  ){
  echo '不能参拍自己的比原资产. Unable to bid asset belong to yourself.';
  exit(-1);
}

//检查是否达到底价
if( $bid_amount<=0 || $bid_amount < $tmp_sell_record['start_amount']  ){
  echo '报价数额需大于0且不能少于拍卖底价. Invalid bid amount.';
  exit(-1);
}

//$tmp_user_info=getPubUserInfo($g_currentUserODIN);
//echo '<p>参拍用户: ',$g_currentUserODIN,'  , ',$tmp_user_info['register'],'</p>';


//检查是否已存在重复拍卖记录
//待加

//在本地数据库保存拍卖纪录

$remark=safeReqChrStr('remark');

$bid_utc=time();

$sql_str="insert into bids (bidder_uri,sell_rec_id,full_odin_uri,asset_id ,remark, coin_type, bid_amount, status_code, bid_utc) values ('$g_currentUserODIN','$sell_rec_id','$full_odin_uri','$asset_id','$remark','$coin_type','$bid_amount','".PPK_ODINSWAP_STATUS_BID."','$bid_utc')";
//echo $sql_str;
$result=@mysqli_query($g_dbLink,$sql_str);
if(!$result)
{
    echo '无效参数. Invalid argus';
    exit(-1);
}
$new_sell_rec_id=mysqli_insert_id($g_dbLink);

require_once "page_header.inc.php";
?>
<p>参拍比原资产[<?php safeEchoTextToPage( $asset_id );?>]的报价已提交。<br><a href="sell.php?sell_rec_id=<?php echo $sell_rec_id;?>">点击这里查看</a></p>
<?php
require_once "page_footer.inc.php";
?>