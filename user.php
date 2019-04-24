<?php
/*        PPK JoyAseet Swap Toolkit           */
/*         PPkPub.org  20190415           */  
/*    Released under the MIT License.     */
require_once "ppk_swap.inc.php";

$original_user_odin=originalReqChrStr('user_odin');

if(strlen($original_user_odin)==0)
    $original_user_odin=$g_currentUserODIN;

if(stripos($original_user_odin,PPK_URI_PREFIX)!==0 && stripos($original_user_odin,DID_URI_PREFIX)!==0){
  echo '无效的用户ODIN标识. Invalid User ODIN.';
  exit(-1);
}

$tmp_user_info=getPubUserInfo($original_user_odin);
$owner_address=$tmp_user_info['register'];

$str_created_time = formatTimestampForView($tmp_user_info['block_time'],false);

//统计该用户标识的相关拍卖和报价记录
$tmp_user_sell_stat=array(
    'total'=>0,
    'status_stat'=>array()
);
$tmp_user_bid_stat=array(
    'total'=>0,
    'status_stat'=>array()
);

$sqlstr = "select status_code,count(*) as counter from sells where  seller_uri='".addslashes($original_user_odin)."' group by status_code order by status_code;";
$rs = mysqli_query($g_dbLink,$sqlstr);
if (false !== $rs) {
    while($row = mysqli_fetch_assoc($rs)){
        $tmp_user_sell_stat['total'] += $row['counter'];
        $tmp_user_sell_stat['status_stat'][$row['status_code']] = $row['counter'];
    }
}
//print_r($tmp_user_sell_stat);

$sqlstr = "select status_code,count(*) as counter from bids where  bidder_uri='".addslashes($original_user_odin)."' group by status_code order by status_code;";
$rs = mysqli_query($g_dbLink,$sqlstr);
if (false !== $rs) {
    while($row = mysqli_fetch_assoc($rs)){
        $tmp_user_bid_stat['total'] += $row['counter'];
        $tmp_user_bid_stat['status_stat'][$row['status_code']] = $row['counter'];
    }
}
//print_r($tmp_user_bid_stat);

require_once "page_header.inc.php";
?>

<div id='pub_top'>
  <table width="100%" border="0">
  <tr>
  <td align="left" width="100">
  <img  style="float:left"  src="<?php safeEchoTextToPage( $tmp_user_info['avtar']);?>" width=64 height=64>
  </td>
  <td>
  <h1><?php safeEchoTextToPage( $tmp_user_info['name']);?></h1>
  </td>
  </tr>
  </table>
</div>

<div id='user_info'>
    <hr>
    <P>身份标识： <?php safeEchoTextToPage( $tmp_user_info['user_odin']); ?></p>
    <P>对应PPk协议URI： <?php safeEchoTextToPage( $tmp_user_info['full_odin_uri']); ?></p>
    <P>电子邮件： <?php safeEchoTextToPage( $tmp_user_info['email']); ?></p>
    <P>创建时间： <?php safeEchoTextToPage( $str_created_time); ?></p>
    <P>拥有者地址： <?php safeEchoTextToPage( $owner_address); ?></p>
    
    <P>发布拍卖总次数： <?php 
    echo '<a href="sell_list.php?seller_uri=',urlencode($tmp_user_info['user_odin']),'">',$tmp_user_sell_stat['total'],'</a> （好评率 ..%）, '; 
    if(count($tmp_user_sell_stat['status_stat'])>0){
        foreach($tmp_user_sell_stat['status_stat'] as $status_code=>$counter){
            echo getStatusLabel($status_code),'(<a href="sell_list.php?seller_uri=',urlencode($tmp_user_info['user_odin']),'&status_code=',$status_code,'">',$counter,'</a>) ';
        }
    }
    ?></p>
    <P>参与报价总次数： <?php 
    echo '<a href="bid_list.php?bidder_uri=',urlencode($tmp_user_info['user_odin']),'">',$tmp_user_bid_stat['total'],'</a> （好评率 ..%）, '; 
    if(count($tmp_user_bid_stat['status_stat'])>0){
        foreach($tmp_user_bid_stat['status_stat'] as $status_code=>$counter){
            echo getStatusLabel($status_code),'(<a href="bid_list.php?bidder_uri=',urlencode($tmp_user_info['user_odin']),'&status_code=',$status_code,'">',$counter,'</a>) ';
        }
    }
    ?></p>
<?php

//echo $tmp_user_info['user_odin'],',',$g_currentUserODIN; 
if($tmp_user_info['user_odin']==$g_currentUserODIN 
  || $tmp_user_info['user_odin'].'#'==$g_currentUserODIN 
  || $tmp_user_info['user_odin']==$g_currentUserODIN.'#' ){ 
  
?>

<p><a class="btn btn-warning" role="button"  href="logout.php">退出登录状态</a></p>
</div>
<!--
<h3>我的比原地址所注册数字资产</h3>
<div class="table-responsive">

<table class="table table-striped">
<thead>
    <tr>
        <th>短标识</th>
        <th>完整标识</th>
        <th>拍卖状态</th>
    </tr>
</thead>

<tbody>
<?php

//查询该用户标识的相关拍卖记录
$array_user_sells=array();
$sqlstr = "SELECT sells.*,view_sell_bid.* FROM sells left join (select sell_rec_id as bid_sell_rec_id,max(bid_amount) as max_bid_amount  from bids group by bid_sell_rec_id ) as view_sell_bid on view_sell_bid.bid_sell_rec_id=sells.sell_rec_id  where  sells.seller_uri='".addslashes($original_user_odin)."' ;";

$rs = mysqli_query($g_dbLink,$sqlstr);
if (false !== $rs) {
    while ($row = mysqli_fetch_assoc($rs)) {
        $array_user_sells[$row['asset_id']]=$row;
    }
}

$tmp_odin_list=getUserOwnedRootODINs($owner_address);
for($ss=0;$ss<count($tmp_odin_list) ;$ss++){
    $tmp_odin_info=$tmp_odin_list[$ss];
    
    $tmp_asset_id=$tmp_odin_info['short'];
    $full_odin_uri=PPK_URI_PREFIX.$tmp_odin_info['full'].PPK_URI_RES_FLAG;
    
    echo '<tr>';
    echo '<td>',getSafeEchoTextToPage($tmp_asset_id),'</td>';
    //echo '<td><a target="_blank" href="user.php?user_odin=', urlencode($full_odin_uri),'">',getSafeEchoTextToPage($full_odin_uri),'</a></td>';
    echo '<td>',getSafeEchoTextToPage($full_odin_uri),'</td>';
    
    if(isset($array_user_sells[$tmp_asset_id])){
        $row=$array_user_sells[$tmp_asset_id];
        
        echo '<td>';
        echo '<a class="btn btn-success" role="button" href="sell.php?sell_rec_id=',$row['sell_rec_id'],'">',getStatusLabel($row['status_code']),'</a><br>';
        
        echo '<font size="-1">起价：',trimz($row['start_amount']),' ',getSafeEchoTextToPage($row['coin_type']),'<br>';
        if(isset($row['max_bid_amount']))
            echo '最新报价：',trimz($row['max_bid_amount']),' ',getSafeEchoTextToPage($row['coin_type']);//,' 来自 <a href="user.php?user_odin=',urlencode($row['bidder_uri']),'">',$row['bidder_uri'],'</a><br>';

        echo '</font></td>';
    }else{
        echo '<td><a href="new_sell.php?asset_id=',urlencode($tmp_asset_id),'">发起拍卖</a></td>';
    }
    
    
    
}
?>
</tbody>
</table>
</div>
<?php
}
?>

-->
  <div class="form-group">
    <label for="remark" class="col-sm-2 control-label">用户定义原文</label>
    <div class="col-sm-10">
     <textarea class="form-control" id="original_content" rows=10 ><?php safeEchoTextToPage($tmp_user_info['original_content']);?></textarea>
    </div>
  </div>

<?php
require_once "page_footer.inc.php";
?>