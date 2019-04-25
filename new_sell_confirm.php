<?php
/*        PPK JoyAseet Swap Toolkit           */
/*         PPkPub.org  20190415           */  
/*    Released under the MIT License.     */
require_once "ppk_swap.inc.php";

if(strlen($g_currentUserODIN)==0){
  Header('Location: login.php');
  exit(-1);
}

$asset_id=safeReqChrStr('asset_id');

if(strlen($asset_id)==0){
  echo '无效的资产标识. Invalid Asset ID.';
  exit(-1);
}

$full_odin_uri=ODIN_BTM_ASSET.$asset_id.PPK_URI_RES_FLAG;
/*
//演示忽略
$tmp_data=getPPkResource(PPK_URI_PREFIX.$asset_id.PPK_URI_RES_FLAG);
if($tmp_data['status_code']!=200){
  echo '获取比原资产标识资源信息出错. Failed to get ODIN data.';
  exit(-1);
}
$full_odin_uri=$tmp_data['uri'];
$tmp_odin_info=@json_decode($tmp_data['content'],true);

//检查有权拍卖该标识
$tmp_user_info=getPubUserInfo($g_currentUserODIN);

if($tmp_user_info['register']!= $tmp_odin_info['register']){
  echo '当前用户无权拍卖不属于自己的数字资产. Unable to sell ODIN belong to others.';
  exit(-1);
}
*/

//检查是否已存在重复拍卖记录
//待加

//在本地数据库保存拍卖纪录
$coin_type=safeReqChrStr('coin_type');
$start_amount=safeReqNumStr('start_amount');
$recommend_names=safeReqChrStr('recommend_names');
$remark=safeReqChrStr('remark');
$bid_hours=safeReqNumStr('bid_hours');
$start_utc=time();

$end_utc = ($bid_hours>0) ? $start_utc + $bid_hours*60*60  : PPK_ODINSWAP_LONGTIME_UTC ;

$sql_str="insert into sells (seller_uri,full_odin_uri,asset_id ,recommend_names, remark, coin_type, start_amount, status_code, start_utc,end_utc) values ('$g_currentUserODIN','$full_odin_uri','$asset_id','$recommend_names','$remark','$coin_type','$start_amount','".PPK_ODINSWAP_STATUS_BID."','$start_utc','$end_utc')";
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
<p>比原资产[<?php echo $asset_id;?>]的拍卖信息已发布。<br><a href="sell.php?sell_rec_id=<?php echo $new_sell_rec_id;?>">点击这里查看</a></p>
<?php
require_once "page_footer.inc.php";
?>