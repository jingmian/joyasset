<?php
/*      PPK JoyAsset SwapService DEMO         */
/*         PPkPub.org  20180925           */  
/*    Released under the MIT License.     */

require_once('common_func.php');

//获取数字资产信息
function getBytomAssetData($asset_id){
    //if(isset($g_cachedAssetInfos[$asset_id]))
    //    return $g_cachedAssetInfos[$asset_id];

    //测试返回
    $default_asset_info=array(
        'status_code' => 200, 
        'uri'=> ODIN_BTM_ASSET.$asset_id.'#',
        'content'=>array(
            'name'=>"TestAsset",
            'symbol'=>"TESTASSET",
            'description'=>"description",
            'avtar'=>"image/asset.png"
        )
    );
  
    return $default_user_info;
}

//按标识获取设备信息
function  getAssetInfo($asset_odin){
  $default_device_info=array(
    'asset_odin'=> $asset_odin,
    'name'=>"",
    'avtar'=>"image/asset.png"
  );
  
  if(stripos($asset_odin,ODIN_JOYIOT_BTM_RESOURCE)!==0){
    return $default_device_info;
  }
  $tmp_chunks=explode( "#",substr($asset_odin,strlen(ODIN_JOYIOT_BTM_RESOURCE)) );
  $odin_chunks=explode("/",$tmp_chunks[0]);
  $btm_tx_id=$odin_chunks[0];


  $tmp_tx_data=getBtmTransactionDetail($btm_tx_id);
  if($tmp_tx_data==null){
    return $default_device_info;
  } 
          
  $obj_set=parseAssetRecordFromBtmTransaction($tmp_tx_data);
  if($obj_set==null){
    $obj_set = $default_device_info;
  } 
  
  $obj_set['asset_odin']=$asset_odin;
  $obj_set['name']=@urldecode($obj_set['name']); //适配中文编码转换

  return $obj_set;
}

//从交易详情中解析出资产定义数据
function parseAssetRecordFromBtmTransaction($obj_tx_data){
  $str_hex=parseSpecHexFromBtmTransaction($obj_tx_data,PPK_JOYIOT_FLAG);
  if(strlen($str_hex)>0){
    $obj_set=json_decode(hexToStr($str_hex),true);
    //print_r($obj_set);
    if(isset($obj_set['@type']) && $obj_set['@type']=='JoyAsset' ){ //有效资产登记数据
      $obj_set['name']=@urldecode($obj_set['name']); //适配中文编码转换
      
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

/*
//解析PPK资源地址
function parsePPkURI($ppk_uri){
    if( 0!=strncasecmp($ppk_uri,PPK_URI_PREFIX,strlen(PPK_URI_PREFIX)) ){
      return null;
    }

    $odin_chunks=array();
    $parent_odin_path="";
    $resource_id="";
    $req_resource_versoin="";
    $resource_filename="";

    $tmp_chunks=explode("#",substr($ppk_uri,strlen(PPK_URI_PREFIX)));
    if(count($tmp_chunks)>=2){
      $req_resource_versoin=$tmp_chunks[1];
    }

    $odin_chunks=explode("/",$tmp_chunks[0]);
    if(count($odin_chunks)==1){
      $parent_odin_path="";
      $resource_id=$odin_chunks[0];
    }else{
      $resource_id=$odin_chunks[count($odin_chunks)-1];
      $odin_chunks[count($odin_chunks)-1]="";
      $parent_odin_path=implode('/',$odin_chunks);
    }
    
    return array(
            'root_odin':$odin_chunks[0],
            'parent_odin_path':$parent_odin_path,
            'resource_id':$resource_id,
            'resource_versoin':$req_resource_versoin,
        );
}
*/
//获取PPk资源信息
function  getPPkResource($ppk_uri){
    if( strcasecmp(substr($ppk_uri,0,strlen(DID_URI_PREFIX)),DID_URI_PREFIX)==0){ //兼容以did:起始的用户标识
        $ppk_uri=substr($ppk_uri,strlen(DID_URI_PREFIX));
    }
    //echo '$ppk_uri=',$ppk_uri;
    $ppk_url='http://test.ppkpub.org:8088/?pttp_interest='.urlencode('{"ver":1,"hop_limit":6,"interest":{"uri":"'.$ppk_uri.'"}}');
    $tmp_ppk_resp_str=file_get_contents($ppk_url);
    //echo '$ppk_url=',$ppk_url,',$tmp_ppk_resp=',$tmp_ppk_resp_str;
    $tmp_obj_resp=@json_decode($tmp_ppk_resp_str,true);
    $tmp_data=@json_decode($tmp_obj_resp['data'],true);
    
    return $tmp_data;
}


//按标识获取用户信息
function  getPubUserInfo($user_odin){
    if(isset($g_cachedUserInfos[$user_odin]))
        return $g_cachedUserInfos[$user_odin];

    $default_user_info=array(
        'user_odin'=> $user_odin,
        'full_odin_uri'=> "",
        'name'=>"",
        'email'=>"",
        'avtar'=>"image/user.png"
    );
  
    $tmp_data=getPPkResource($user_odin);
    //print_r($tmp_data);
    if($tmp_data['status_code']==200){
        $default_user_info['full_odin_uri']=$tmp_data['uri'];
        $tmp_user_info=@json_decode($tmp_data['content'],true);
        //print_r($tmp_user_info);
        $default_user_info['original_content']=$tmp_data['content'];
        if($tmp_user_info!=null){
            if(array_key_exists('@type',$tmp_user_info) && $tmp_user_info['@type']=='PPkDID' ){ //DID格式的用户身份定义
                $default_user_info['name']=$tmp_user_info['attributes']['name'];
                $default_user_info['email']=$tmp_user_info['attributes']['email'];
                $default_user_info['avtar']=$tmp_user_info['attributes']['avtar'];
                $default_user_info['register']=$tmp_user_info['attributes']['wallet_address'];
            }else if(array_key_exists('register',$tmp_user_info)){ //直接使用ODIN标识的属性
                $default_user_info['name']=$tmp_user_info['title'];
                $default_user_info['email']=$tmp_user_info['email'];
                $default_user_info['register']=$tmp_user_info['register'];
            }
        }
        $g_cachedUserInfos[$user_odin]=$default_user_info;
    }
    return $default_user_info;

}

//按用户BTC地址获取所拥有的全部ODIN根标识列表
function  getUserOwnedRootODINs($user_btc_address){
    $odin_list=array();

    $ppk_url='http://66.42.48.121/odin/query.php?address='.$user_btc_address;
    $tmp_ppk_resp_str=file_get_contents($ppk_url);
    //echo '$ppk_url=',$ppk_url,',$tmp_ppk_resp=',$tmp_ppk_resp_str;
    $tmp_obj_resp=@json_decode($tmp_ppk_resp_str,true);
    if($tmp_obj_resp['status']=='OK'){
        $odin_list=$tmp_obj_resp['list'];
    }
    
    return $odin_list;
}

//获取拍卖交易状态码对应文字名称
function getStatusLabel($status_code){
    switch($status_code){
        case PPK_ODINSWAP_STATUS_BID:
            return '报价中';
        case PPK_ODINSWAP_STATUS_ACCEPT:
            return '达成意向';
        case PPK_ODINSWAP_STATUS_PAY:
            return '已付款';
        case PPK_ODINSWAP_STATUS_TRANSFER:
            return '标的资产已过户';
        case PPK_ODINSWAP_STATUS_CANCEL:
            return '交易取消';
        case PPK_ODINSWAP_STATUS_EXPIRED:
            return '到期确拍中';
        case PPK_ODINSWAP_STATUS_FINISH:
            return '已完成';
        default:
            return '未知['.$status_code.']';
    }
} 


//自动更新已到期的拍卖纪录状态
function autoUpdateExpiredSells(){ 
    Global $g_dbLink;
    $sql_str="update sells set status_code='".PPK_ODINSWAP_STATUS_EXPIRED."' where end_utc<=".time()." and status_code=".PPK_ODINSWAP_STATUS_BID.";";
    //echo $sql_str;
    $result=@mysqli_query($g_dbLink,$sql_str);
}

//获取ODIN标识配置管理权限对应文字名称
function getOdinAuthSetLabel($set_code){
    switch($set_code){
        case 0:
            return '注册者或管理者任一方都可以修改配置';
        case 1:
            return '只有管理者能修改配置';
        case 2:
            return '注册者和管理者必须共同确认才能修改配置';
        default:
            return '无效设置['.$set_code.']';
    }
}

define('TEST_PRV_KEY',"-----BEGIN PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIVALYP0duyeZgE7sl8w/yyTKkOw
ehmZu8euMkQ69xvj7cFh9jw82/29cOJReZtQ2wnnFoPJfRXXlGIaSz4TYGqSdrMiVdpP8GAIxynL
skTTwmd9l/7hjuSkYeCTBDnW2m+wUIMg8F5VNA0x7iRfZpwAktQhWvzHoSZ4zpN1rCt7AgMBAAEC
gYB2mnoo0oar9A09GcKZogXuygq7dzAm60RN5ooNWyKp64WrNCO02ELDpkP83aJNEXn1ZYAPy18P
+vOzIk5IsXyF9kAtf0PvVOwDEmWB7IxkfORLos9kWXfPm6fzv3xBj3YWrn6VP6HH0Y1tnSHsq+Kd
aFq74zvaZ1nasr5Z+zrRAQJBAMDiMPv5oRNrMHtV8R/NUVW+fZoM8jRlTOdE+UyBVEQb5eIGESoe
22Ud69YinkIIeg/kHVqhiuvt8ZI0NmUtFIECQQCw2o5PBwiufZfwGGqt5OcX3OWNbPTKNtrP3gfW
PyHbMVolwj3n2Uk9T745+5+u+uaWw48JpJHeI5JyC8ib3JH7AkEAk0toOtPrtMeTU2xS4YVKSl9Y
zU57m5WMP8QFWO4eapCfYZZefzrnMfXChlkTX7vXctExtWdNjlO7uXmT3HmrgQJAEWA30S3ovXQb
hHxxpsoxpleOujl1R5TiJMA01uj3A5kyuTj/ahMgLgTytLGzO7btBu0J1bG0nzWxgsEDocSnFwJA
XFiR9ejUNCHivLHQNDAy0GUq+W+PiNof0AlWyHFK0BYMF1zL8HAN4QmF3bCb0/WwGTWmRd9phBxo
SnYXzZhpWg==
-----END PRIVATE KEY-----");

//构建包含报价确认信息的数据对象
function genAcceptBidArray( $source, $destination, $asset_id,$full_odin_uri,$coin_type,$bid_amount,$service_uri ){
  //组织交易信息数据块
  $max_user_input_length = MAX_OP_RETURN_LENGTH - strlen(PPK_ODINSWAP_FLAG);

  $str_odin_msg = PPK_ODINSWAP_FLAG  
      .",".$source
      ." sell BytomAsset[" .$asset_id
      ."] to ".$destination
      ." for ". trimz($bid_amount) 
      ." " . $coin_type 
      ." by " . $service_uri ;  
  
  $str_sign="SHA256withRSA:".rsaSign($str_odin_msg,TEST_PRV_KEY,'SHA256'); //生成测试签名
  $str_data=$str_odin_msg." ; sign=".$str_sign;
  
  //将原始字节字符串转换为用16进制表示
  $str_odin_hex=strToHex($str_data);
  //echo 'str_odin_hex=',$str_odin_hex;
  
  $tmp_array=array(
    'source' => $source,
    'destination' => $destination,
    'amount_satoshi' => DUST_SATOSHI,
    'fee_satoshi' => MIN_FEE_SATOSHI,
    'mark_hex' => '',
    'data' => $str_data,
    'data_hex' => $str_odin_hex,
  );

  //print_r($tmp_array);

  return $tmp_array;
}

//构建包含支付报价信息的数据对象
function genPayBidArray( $source, $destination, $asset_id,$full_odin_uri,$coin_type,$bid_amount,$service_uri ){
  //组织交易信息数据块
  $max_user_input_length = MAX_OP_RETURN_LENGTH - strlen(PPK_ODINSWAP_FLAG);

  $str_odin_msg = PPK_ODINSWAP_FLAG  
      .",".$source
      ." pay " . trimz($bid_amount) 
      ." ". $coin_type
      ." to ".$destination
      ." for BytomAsset[". ODIN_BTM_ASSET.$asset_id 
      ."] by " . $service_uri ;  
  
  $str_sign="SHA256withRSA:".rsaSign($str_odin_msg,TEST_PRV_KEY,'SHA256'); //生成测试签名
  $str_data=$str_odin_msg." ; sign=".$str_sign;
  
  //将原始字节字符串转换为用16进制表示
  $str_odin_hex=strToHex($str_data);
  //echo 'str_odin_hex=',$str_odin_hex;
  
  $amount_satoshi = ($coin_type==='BITCOIN') ? $bid_amount*BTC_SATOSHI_UNIT : DUST_SATOSHI;
  
  $tmp_array=array(
    'source' => $source,
    'destination' => $destination,
    'amount_satoshi' => $amount_satoshi,
    'fee_satoshi' => MIN_FEE_SATOSHI,
    'mark_hex' => '',
    'data' => $str_data,
    'data_hex' => $str_odin_hex,
  );

  //print_r($tmp_array);

  return $tmp_array;
}

//递归获得指定数字短标识的对应字母转义名称组合
$LetterEscapeNumSet = array(0=>"O",1=>"AIL",2=>"BCZ",3=>"DEF",4=>"GH",5=>"JKS",6=>"MN",7=>"PQR",8=>"TUV",9=>"WXY");

function getEscapedListOfShortODIN($short_odin){ 
    if(strlen($short_odin)>5){
        return array($short_odin);
    }
    $listEscaped=array();
    return  getEscapedLettersOfShortODIN($listEscaped,''.$short_odin,0,"");
}

function getEscapedLettersOfShortODIN($listEscaped,$original,$posn,$pref){ 
    Global $LetterEscapeNumSet;
    $tmpNum = 0 + substr($original,$posn,1);

    $tmpLetters=$LetterEscapeNumSet[$tmpNum];
    for($tt=0;$tt<strlen($tmpLetters);$tt++){
      $new_str=$pref.substr($tmpLetters,$tt,1);
      
      if($posn<strlen($original)-1){
        $listEscaped=getEscapedLettersOfShortODIN($listEscaped,$original,$posn+1,$new_str);
      }else{
        $listEscaped[]=$new_str;
      }
    }

    return $listEscaped;
}

//For generating signature using RSA private key
function rsaSign($data,$strValidationPrvkey,$algo){
    //$p = openssl_pkey_get_private(file_get_contents('private.pem'));
    $p=openssl_pkey_get_private($strValidationPrvkey);
    openssl_sign($data, $signature, $p,$algo);
    openssl_free_key($p);
    return base64_encode($signature);
}
