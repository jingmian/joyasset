<?php
require_once "ppk_swap.inc.php";

switch($_REQUEST['backpage']){ //严格检查和组织网址，避免注入风险
    case 'new_bid':
        $back_url='new_bid.php?sell_rec_id='.safeReqNumStr('sell_rec_id');
        break;
    default:
        $back_url='./';
}
  

require_once "page_header.inc.php";
?>
<h3>在比原链上注册兼容DID的新用户标识</h3>
<form class="form-horizontal" >
<div class="form-group">
    <label for="user_name" class="col-sm-2 control-label">用户昵称</label>
    <div class="col-sm-10">
      <input type=text class="form-control"  id="user_name" value="" >
    </div>
</div>

<div class="form-group">
    <label for="user_email" class="col-sm-2 control-label">电子邮箱</label>
    <div class="col-sm-10">
      <input type=text class="form-control"  id="user_email" value="" >
    </div>
</div>

<div class="form-group">
    <label for="user_avtar_url" class="col-sm-2 control-label">头像URL</label>
    <div class="col-sm-10">
      <input type=text class="form-control"  id="user_avtar_url" value="http://ppkpub.org/images/user.png" >
    </div>
</div>

<div class="form-group">
    <label for="user_avtar_img" class="col-sm-2 control-label">头像预览</label>
    <div class="col-sm-10">
    <img id="user_avtar_img" width="128" height="128" src="http://ppkpub.org/images/user.png" >
    </div>
</div>

<div class="form-group">
    <label for="user_address" class="col-sm-2 control-label">比原钱包地址</label>
    <div class="col-sm-10">
      <input type=text class="form-control"  id="user_address" value="" >
    </div>
</div>

<div class="form-group">
    <label for="pub_key" class="col-sm-2 control-label">验证公钥</label>
    <div class="col-sm-10">
     <textarea class="form-control" id="pub_key" rows=3 >MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCFQC2D9HbsnmYBO7JfMP8skypDsHoZmbvHrjJE
Ovcb4+3BYfY8PNv9vXDiUXmbUNsJ5xaDyX0V15RiGks+E2BqknazIlXaT/BgCMcpy7JE08JnfZf+
4Y7kpGHgkwQ51tpvsFCDIPBeVTQNMe4kX2acAJLUIVr8x6EmeM6TdawrewIDAQAB</textarea>
     <span>公钥将与用户注册信息一起保存并被公开访问。</span>
    </div>
</div>
  
<div class="form-group">
    <label for="private_key" class="col-sm-2 control-label">签名私钥</label>
    <div class="col-sm-10">
     <textarea class="form-control" id="private_key" rows=3 >MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIVALYP0duyeZgE7sl8w/yyTKkOw
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
SnYXzZhpWg==</textarea>
     <span>此私钥为测试自动生成，请自行保存。</span>
    </div>
</div>  

<input type="hidden" id="game_trans_fee_btm" value="<?php echo TX_GAS_AMOUNT_mBTM/1000; ?>"> 
<input type="hidden" id="pub_trans_data_hex" value=""></p>

<p align=center><input type='button' class="btn btn-success"  id="send_trans_btn" value=' 在比原链上注册新用户标识 ' onclick='sendBtmTX();'></p>
</form>


<!--
<p>二维码（可使用比原链钱包APP来扫码发送交易）:</p>
<p><img id="game_trans_qrcode" border=0 width=250 height=250 src="image/star.png" title="qrcode"></p>
<p><input type=text id="qrcode_text" value="..." size=30></p>
<hr>
</p>
<p><a target="_blank" href="https://bytom.io/"><img src="https://bytom.io/wp-content/uploads/2018/04/logo-white-v.png" alt="下载比原链钱包" width=200 height=50></a>
</p> 
-->

<!--
<script src="https://cdn.jsdelivr.net/gh/ethereum/web3.js/dist/web3.min.js"></script>
-->
<script src="js/common_func.js"></script>
<script type="text/javascript">
var mObjUserInfo;
var mObjUserPubKey;
var mTempDataHex;

window.onload=function(){
    init();
    /*
    var test;
    
    test={"address":"1HVSDUmW3abkitZUoZsYMKZ2PbiKhr8Rdo"};
    callback_setNewAddress('OK',test);
    test={"status":"OK","register_num":"15","last_register_odin":{"full_odin":"559411.1747","short_odin":"39642","register":"1HVSDUmW3abkitZUoZsYMKZ2PbiKhr8Rdo","admin":"1HVSDUmW3abkitZUoZsYMKZ2PbiKhr8Rdo","block_index":"559411","block_hash":"0000000000000000000cddeb7a38abcba7bff08200b4127cbf37df1af958cbea","block_time":"1548044945"},"balance_satoshi":3000,"unconfirmed_tx_count":0};
    callback_getBtcAddressSummary('OK',test);
    */
    
    //var test={"odin_uri":"ppk:100#"};
    //callback_setNewOdin('OK',test);
}

function init(){
    console.log("init...");
    
    if(typeof(PeerWeb) !== 'undefined'){ //检查PPk开放协议相关PeerWeb JS接口可用性
        console.log("PeerWeb enabled");
    }else{
        console.log("PeerWeb not valid");
    }
}


function sendBtmTX() {
  if(document.getElementById('user_name').value.length == 0 ){
    alert("请输入有效的用户名称！");
    return false;
  }
  
  if(document.getElementById('user_address').value.length == 0 ){
    alert("请输入有效的比原钱包地址！");
    return false;
  }
  
  if(document.getElementById('pub_key').value.length == 0 ){
    alert("请输入有效的公钥！");
    return false;
  }
  
  if(document.getElementById('user_avtar_url').value.length == 0 ){
    alert("请输入有效的用户头像图片URL！");
    return false;
  }

  if(document.getElementById('game_trans_fee_btm').value.length == 0 ){
    alert('请输入有效的转账GAS费用，缺省为 <?php echo TX_GAS_AMOUNT_mBTM/1000; ?> BTM！');
    return false;
  }
  
  var new_user_did_uri=updateTransData();
  
  document.getElementById("send_trans_btn").disabled=true;
  document.getElementById("send_trans_btn").value="正在自动生成用户标识,请稍候...";
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET","send_tx.php?new_user_uri="+encodeURIComponent(new_user_did_uri)+"&pub_trans_data_hex="+document.getElementById("pub_trans_data_hex").value);
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
        var user_name=encodeURI(document.getElementById('user_name').value,"utf-8");
        setCookie('swap_user_uri',new_user_did_uri,365);
        setCookie('swap_user_name',user_name,365);
        setCookie('swap_user_avtar_url',document.getElementById('user_avtar_url').value,365);

        alert("创建用户成功\n兼容DID的ODIN标识："+new_user_did_uri);
        self.location="<?php echo $back_url;?>";
      }else{
        alert("出错了！\n"+xmlhttp.responseText);
      }
    }
  }
}


function updateTransData(){
  var game_trans_fee_btm = <?php echo TX_GAS_AMOUNT_mBTM/1000; ?>;
  
  var user_avtar_url=document.getElementById('user_avtar_url').value;
  if(user_avtar_url.length == 0 ){
    return false;
  }
  document.getElementById('user_avtar_img').src=user_avtar_url;
  
  var user_name=document.getElementById('user_name').value;
  if(user_name.length == 0 ){
    return false;
  }
  
  var user_address=document.getElementById('user_address').value;
  if(user_address.length == 0 ){
    return false;
  }
  
  var pub_key=document.getElementById('pub_key').value;
  if(pub_key.length == 0 ){
    return false;
  }
  
  var pub_key_pem=JSON.stringify("-----BEGIN PUBLIC KEY-----\n"+pub_key+"\n-----END PUBLIC KEY-----");
  
  var user_ppk_uri="<?php echo JOYDID_PPK_URI_PREFIX;?>"+encodeURI(user_name,"utf-8")+"#";
  var user_did_uri="<?php echo DID_URI_PREFIX;?>"+user_ppk_uri;
  var pubkey_did_uri=user_did_uri+"#keys-1";

  
  var user_email=document.getElementById('user_email').value;

  var str_setting='{"@context":["https://w3id.org/did/v1", "https://ppkpub.org/did/v1" ],"@type":"PPkDID","id": '+JSON.stringify(user_did_uri)+',"attributes":{"ppk_uri": '+JSON.stringify(user_ppk_uri)+',"name":'+JSON.stringify(encodeURI(user_name,"utf-8"))+',"wallet_address":'+JSON.stringify(encodeURI(user_address,"utf-8"))+',"email":'+JSON.stringify(encodeURI(user_email,"utf-8"))+', "avtar":'+JSON.stringify(user_avtar_url)+'},"authentication":[{"id": '+JSON.stringify(pubkey_did_uri)+',"type": "RsaVerificationKey2018","controller": '+JSON.stringify(user_did_uri)+',"publicKeyPem": '+pub_key_pem+'}],"service": [{"type": "PPkService","serviceEndpoint": "http://btmdemo.ppkpub.org/joy/ap/"}]}';
                         
 
  var game_trans_data="<?php  echo PPK_ODINSWAP_FLAG; ?>"+str_setting;
  console.log("game_trans_data="+game_trans_data);
  
  var pub_trans_data_hex = stringToHex(game_trans_data);

  document.getElementById('pub_trans_data_hex').value= pub_trans_data_hex;
  
  //var btm_uri='bytom:'+document.getElementById('guess_contract_uri').value+'?value='+game_trans_fee_btm+'&data='+pub_trans_data_hex;
  //document.getElementById('qrcode_text').value= btm_uri;
  //document.getElementById('game_trans_qrcode').src='http://qr.liantu.com/api.php?text='+encodeURIComponent(btm_uri);

   return user_did_uri;
}

function resetAll(){
  document.getElementById('game_trans_fee_btm').value=<?php echo TX_GAS_AMOUNT_mBTM/1000; ?>;
  document.getElementById('pub_trans_data_hex').value='';
  
  //document.getElementById('qrcode_text').value= '';
  //document.getElementById('game_trans_qrcode').src='star.png';

}

function setCookie(c_name, value, expiredays){
  var exdate=new Date();
  exdate.setDate(exdate.getDate() + expiredays);
  document.cookie=c_name+ "=" + escape(value) + ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}

function getCookie(c_name){
  if (document.cookie.length>0){ 
    c_start=document.cookie.indexOf(c_name + "=");
    if (c_start!=-1){ 
      c_start=c_start + c_name.length+1;
      c_end=document.cookie.indexOf(";",c_start);
      if (c_end==-1) 
        c_end=document.cookie.length    
      return unescape(document.cookie.substring(c_start,c_end));
    } 
  }
  return "";
}


</script>
<?php require_once "page_footer.inc.php";?>