<?php
/* PPK JoyBlock DEMO based Bytom Blockchain */
/*         PPkPub.org  20180917             */  
/*    Released under the MIT License.       */

require_once "common_func.php";

/* CoomonDefine */
define('PLUS_SITE_HTDOCS_PATH', dirname(__FILE__).'/');   //主路径设置

define('ODIN_PPKJOY_ROOT','ppk:JOY/');
define('PPK_JOY_FLAG','PJOY');

define('PPK_DEFAULT_AP_GATEWAY','http://btmdemo.ppkpub.org:8088/');

define('BTM_ASSET_ID','ffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffffff'); 
define('TX_GAS_AMOUNT_mBTM',100);
define('BTM_NODE_API_URL','http://45.32.19.146:9888/');   //此处配置你的比原API的访问地址
define('BTM_EXPLORER_API_URL','https://blockmeta.com/api/v2/');

define('ODIN_PPKJOY_BTM_RESOURCE','ppk:JOY/asset/bytom/');
define('ODIN_BTM_ROOT','ppk:BTM/');
define('ODIN_BTM_ADDRESS',ODIN_BTM_ROOT.'address/');
define('ODIN_BTM_CONTRACT',ODIN_BTM_ROOT.'contract/');
define('ODIN_BTM_TRANSACTION',ODIN_BTM_ROOT.'tx/');
define('ODIN_BTM_ASSET',ODIN_BTM_ROOT.'asset/');

define('BTM_BONUS_TOKEN_AMOUNT',100);

//查询网络信息，注意需根据你的节点账户信息相应配置，可以多个账户，相应提供账户ID，密码和缺省比原地址
$tmp_url=BTM_NODE_API_URL.'net-info';
$obj_resp=commonCallBtmApi($tmp_url,"");
if(strcmp($obj_resp['status'],'success')!==0){
  echo "Network Error! Please retry later...";
  exit(-1);
}
$btm_netinfo=$obj_resp['data'];
$gStrBtmNetworkId=$btm_netinfo['network_id'];
if(strcasecmp($gStrBtmNetworkId,'mainnet')==0){
  //mainnet
  define('JOYBLOCK_TOEKN_ASSET_ID','1e7386cadeb795449a023971a6b78efdeb5963cb19ecb1f822ebf2464446f0f0');
  define('FUND_BTM_ADDRESS','YourBtmAdress');
  define('BTM_NODE_API_TOKEN','');
  define('BTM_NODE_API_ACCOUNT_ID_GUESS','0IJ01RCU00A02');
  define('BTM_NODE_API_ACCOUNT_PWD_GUESS','12345');
  define('BTM_NODE_API_ACCOUNT_ID_PUB','0IJ8PH48G0A02');
  define('BTM_NODE_API_ACCOUNT_PWD_PUB','12345');
  
  $gArrayNodeAccounts=array(
    array('id'=>'0IJ01RCU00A02','pwd'=>'12345','address'=>''),
    array('id'=>'0IJ8PH48G0A02','pwd'=>'12345','address'=>''),
    array('id'=>'','pwd'=>'12345','address'=>''),
    array('id'=>'','pwd'=>'12345','address'=>''),
    array('id'=>'','pwd'=>'12345','address'=>''),
  );
}else if(strcasecmp($gStrBtmNetworkId,'solonet')==0){
  //solonet
  define('JOYBLOCK_TOEKN_ASSET_ID','f3b8e4da6ed072743a6e3a9665e0ad727bb73892e55fdb5c25e6693c74f95b70');
  define('FUND_BTM_ADDRESS','sm1q7lr9lvdpljchgc8ten3scf3asflcwwrkp20zag');
  define('BTM_NODE_API_TOKEN','');

  $gArrayNodeAccounts=array(
    array('id'=>'0IIUI0SP00A02','pwd'=>'12345','address'=>'sm1q7lr9lvdpljchgc8ten3scf3asflcwwrkp20zag'),
    array('id'=>'0IMIUPSA00A02','pwd'=>'12345','address'=>'sm1qvw6d9jereapxxwtu3vw555a6d5cgrc5u24fpka'),
    array('id'=>'0IPBF361G0A02','pwd'=>'12345','address'=>'sm1qrlp2sskepg7zv2mjssg79z2ra3sdg474x2an2j'),
    array('id'=>'0IPBFF6L00A04','pwd'=>'12345','address'=>'sm1qfj6qql6pre5tmtqywszy2t6xr48mqchy5a0fd3'),
    array('id'=>'0IPBFNN4G0A06','pwd'=>'12345','address'=>'sm1qeq9zhh0d22j3vrl7dkw0uungcy5v0avg46r6t7'),
  );
}else{
  //testnet
  define('JOYBLOCK_TOEKN_ASSET_ID','351b94f47823e9e27f23d6a5fbff230745b9be3d5b0f0ad4680fbcea87a8e528'); //自己发行的id注册资产ID
  define('FUND_BTM_ADDRESS','tm1qzymnxuzlt6e8sjf4vc0ct6f6vkk25y27dtzdwe'); //资产发布者钱包地址
  define('BTM_NODE_API_TOKEN','');

  $gArrayNodeAccounts=array(
    array('id'=>'0IOE8PIMG0A02','pwd'=>'12345','address'=>'tm1qzymnxuzlt6e8sjf4vc0ct6f6vkk25y27dtzdwe'),
    array('id'=>'0IOEFJFRG0A04','pwd'=>'12345','address'=>'tm1q8sarfnju2gyft56hh38w8n0s8xwq4tcsfaeqq8'),
    array('id'=>'0IP461VU00A02','pwd'=>'12345','address'=>'tm1qyfce8yks4z7qvt2w2445jl6urt8ye6c2jru7mt'),
    array('id'=>'0IP46IDC00A04','pwd'=>'12345','address'=>'tm1q8jquakgharun8h5etd5x3gax94p3qs0xgv9p5e'),
    array('id'=>'0IP472OJ00A06','pwd'=>'12345','address'=>'tm1q3khpvmfpnmq967ftesd0h9q7fhzaq43djmwmpg'),
  );
}

function getNextAccountInfo(){
  global $gArrayNodeAccounts;
  global $gBtmAccountSnFile;
  
  $tmp_sn_filename = isset($gBtmAccountSnFile)>0 ? $gBtmAccountSnFile : PLUS_SITE_HTDOCS_PATH.'LastAccountSN.txt';
  //echo '$tmp_sn_filename=',$tmp_sn_filename;
  
  $nextAccountSN=1+intval(file_get_contents($tmp_sn_filename));
  if($nextAccountSN>=count($gArrayNodeAccounts))
     $nextAccountSN=0;
  file_put_contents($tmp_sn_filename,$nextAccountSN);
  return $gArrayNodeAccounts[$nextAccountSN];
}

function getBtmTransactionDetail($tx_id){
  $tmp_url=BTM_NODE_API_URL.'list-transactions';
  $tmp_post_data='{"id":"'.$tx_id.'","detail": true,"unconfirmed":true}';

  $obj_resp=commonCallBtmApi($tmp_url,$tmp_post_data);
  if(strcmp($obj_resp['status'],'success')===0){
      return $obj_resp['data'][0];
  }else{
      return null;
  }
}

function getRetireDataFromBtmTransaction($obj_tx_data){
  foreach($obj_tx_data['outputs'] as $tmp_out ){
    if($tmp_out['type']=='retire' && $tmp_out['asset_id']==JOYBLOCK_TOEKN_ASSET_ID ){
      $str_hex= $tmp_out['control_program'];
      //echo 'str_hex=',$str_hex;
      $len_flag=hexdec(substr($str_hex,2,2));
      //echo 'len_flag=',$len_flag;
      
      $data_hex_start_posn=0;
      if($len_flag>0x4b){
          $data_hex_len=($len_flag-0x4b)*2;
          $data_hex_start_posn=4+$data_hex_len;
          //echo 'data_hex_len=',$data_hex_len,', data_hex_start_posn=',$data_hex_start_posn;
      }else{
          $data_hex_start_posn=4;
      }
      
      if($data_hex_start_posn>0){ //符合特征
        return substr($str_hex,$data_hex_start_posn);
      }
    }
  }
  return null;
}

function parseSpecHexFromBtmTransaction($obj_tx_data,$str_flag){
  foreach($obj_tx_data['outputs'] as $tmp_out ){
    if($tmp_out['type']=='retire' && $tmp_out['asset_id']==JOYBLOCK_TOEKN_ASSET_ID ){
      $str_hex= $tmp_out['control_program'];
      //echo 'str_hex=',$str_hex;
      $str_flag_hex=strtohex($str_flag);
      $flag_posn=strpos($str_hex,$str_flag_hex);
      //echo 'flag_posn=',$flag_posn;
      if($flag_posn>0){ //符合特征
        return substr($str_hex,$flag_posn+strlen($str_flag_hex));
      }
    }
  }
  return null;
}

function parseGameRecordFromBtmTransaction($obj_tx_data){
  $str_hex=parseSpecHexFromBtmTransaction($obj_tx_data,PPK_JOY_FLAG);
  if(strlen($str_hex)>0){
    $obj_set=json_decode(hexToStr($str_hex),true);
    if(isset($obj_set['img_data_url'])>0){ //有效数据
      $obj_set['tx_id'] = $obj_tx_data['tx_id'];
      $obj_set['block_time'] = $obj_tx_data['block_time'];
      $obj_set['block_height'] = $obj_tx_data['block_height'];
      $obj_set['block_hash'] = $obj_tx_data['block_hash'];
      $obj_set['block_index'] = $obj_tx_data['block_index']; //position of the transaction in the block.

      return $obj_set;
    }
  }
  return null;
}

//检查指定OUTPUT_ID是否未被使用
function isBtmOuputUnspent($output_id,$is_contract){
  if(strlen($output_id)==0)
      return null;
  
  $tmp_url=BTM_NODE_API_URL.'list-unspent-outputs';
  $tmp_post_data='{"id":"'.$output_id.'","smart_contract":'.($is_contract?'true':'false').'}';

  $obj_resp=commonCallBtmApi($tmp_url,$tmp_post_data);
  if(strcmp($obj_resp['status'],'success')===0){
      return count($obj_resp['data'])>0 ? true:false;
  }else{
      return null;
  }
}

function sendBtmTransaction($tx_data,$current_account_info){
  $tmp_url=BTM_NODE_API_URL.'build-transaction';
  $obj_resp=commonCallBtmApi($tmp_url,$tx_data);

  if(strcmp($obj_resp['status'],'success')===0){
    $tmp_url=BTM_NODE_API_URL.'sign-transaction';
    $tmp_post_data='{"password":"'.$current_account_info['pwd'].'","transaction":'.json_encode($obj_resp['data']).'}';

    $obj_resp=commonCallBtmApi($tmp_url,$tmp_post_data);
    
    if(strcmp($obj_resp['status'],'success')===0){
        $tmp_url=BTM_NODE_API_URL.'submit-transaction';
        $tmp_post_data='{"raw_transaction":"'.$obj_resp['data']['transaction']['raw_transaction'].'"}';

        $obj_resp=commonCallBtmApi($tmp_url,$tmp_post_data);
    }
  }
  return $obj_resp;
}

function commonCallBtmApi(
         $api_url,    
         $post_data
    )
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $api_url);
    //设置头文件的信息不作为数据流输出
    curl_setopt($curl, CURLOPT_HEADER, 0);
    //设置获取的信息以文件流的形式返回，而不是直接输出。
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //设置post方式提交
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
    
    //执行命令
    $data = curl_exec($curl);
    //关闭URL请求
    curl_close($curl);

    return json_decode($data,true);
}



