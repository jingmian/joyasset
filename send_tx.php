<?php
/* PPK JoyAsset DEMO baes Bytom Blockchain */
/* Send BTM Transaction in AJAX mode */
require_once "ppk_swap.inc.php";

$pub_trans_data_hex = $_REQUEST['pub_trans_data_hex'];
if(strlen($pub_trans_data_hex)==0){
  safeEchoTextToPage('{"status":"fail","code":"PPK001","msg":"无效输入 Invalid Input!","error_detail":"无效输入 Invalid Input!"}');
  exit(-1);
}

//重复性提交检查
$new_user_uri = $_REQUEST['new_user_uri'];
if(strlen($new_user_uri)>0){
    $tmp_data=getPPkResource($new_user_uri);
    //print_r($tmp_data);
    if($tmp_data['status_code']!=404){
        echo '{"status":"fail","code":"PPK002","msg":"同名用户记录已存在 Username existed! ',$new_user_uri,'","error_detail":"同名用户记录已存在 Username existed!"}';
        exit(-1);
    }
}

$current_account_info=getNextAccountInfo();

$tmp_url=BTM_NODE_API_URL.'build-transaction';
$tmp_post_data='{
  "base_transaction": null,
  "actions": [
    {
      "account_id": "'.$current_account_info['id'].'",
      "amount": '.TX_GAS_AMOUNT_mBTM.'00000,
      "asset_id": "ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff",
      "type": "spend_account"
    },
    {
      "account_id": "'.$current_account_info['id'].'",
      "amount": 1,
      "asset_id": "'.JOYBLOCK_TOEKN_ASSET_ID.'",
      "type": "spend_account"
    },
    {
      "amount": 1,
      "asset_id": "'.JOYBLOCK_TOEKN_ASSET_ID.'",
      "arbitrary": "'.$pub_trans_data_hex.'",
      "type": "retire"
    }
  ],
  "ttl": 0,
  "time_range": '.time().'
  
}';

$obj_resp=sendBtmTransaction($tmp_post_data,$current_account_info);

echo json_encode($obj_resp);
/*
if(strcmp($obj_resp['status'],'success')!==0){
    echo "发送比原交易失败，请稍候重试！Failed to send transaction to Bytom blockchain!\n",json_encode($obj_resp);
    echo "Debug Account:", $current_account_info['id'];
    exit(-1);
}

echo '发送比原交易成功，交易ID：<a href="https://blockmeta.com/tx/',$obj_resp['data']['tx_id'],'" target="_blank">',$obj_resp['data']['tx_id'],'</a>',"<br><br>\n";
echo '请等待2-3分钟得到比原链出块确认，返回<a href="./">主页</a>查看。';
*/