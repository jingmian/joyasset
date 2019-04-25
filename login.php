<?php
require_once "ppk_swap.inc.php";

switch($_REQUEST['backpage']){ //严格检查和组织网址，避免注入风险
    case 'new_bid':
        $back_url='new_bid.php?sell_rec_id='.safeReqNumStr('sell_rec_id');
        break;
    case 'new_sell':
        $back_url='new_sell.php';
        break;
    default:
        $back_url='./';
}
  

require_once "page_header.inc.php";
?>
<h3>使用兼容DID规范的ODIN标识登录</h3>

<form class="form-horizontal" >
<div class="form-group">
    <label for="exist_odin_uri" class="col-sm-2 control-label">用户标识</label>
    <div class="col-sm-10">
      <input type="text" class="form-control"  id="exist_odin_uri" value="did:ppk:joy/btmid/alice#"  onchange="getUserOdinInfo();"  >
    </div>
</div>
  
<div class="form-group">
    <label for="use_exist_odin" class="col-sm-2 control-label"></label>
    <div class="col-sm-10">
      <input type='button' class="btn btn-success"  id="use_exist_odin" value=' 验证该用户身份 ' onclick='checkExistODIN();' disabled="true"> <input type='button' class="btn btn-danger"  id="test_login_btn" value=' 体验测试点这里（无需验证直接登录） ' onclick='testLogin();' ><br>
      注：用户身份验证需要安装升级到PPkBrowser安卓版0.302以上版本，<a href="https://ppkpub.github.io/docs/DOC_PPk_Browser_Tutorial.pdf">请点击阅读这里的操作说明安装和使用。</a>
    </div>
</div>
 
<p align="center">对应的用户信息设置</p>
<div class="form-group">
    <label for="user_name" class="col-sm-2 control-label">用户昵称</label>
    <div class="col-sm-10">
      <input type=text class="form-control"  id="user_name" value="" >
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
</form>

<!--
<p>二维码（可使用APP来扫码登录）:</p>
<p><img id="game_trans_qrcode" border=0 width=250 height=250 src="image/star.png" title="qrcode"></p>
<p><input type=text id="qrcode_text" value="..." size=30></p>
<hr>
</p>
-->

<script src="js/common_func.js"></script>
<script type="text/javascript">
var mObjUserInfo;
var mObjUserPubKey;
var mTempDataHex;

//测试
function testLogin(){
    useExistODIN();
}


window.onload=function(){
    init();
    /*
    var test;
    
    test={"address":"1HVSDUmW3abkitZUoZsYMKZ2PbiKhr8Rdo"};
    callback_setNewAddress('OK',test);
    test={"status":"OK","register_num":"15","last_register_odin":{"full_odin":"559411.1747","asset_id":"39642","register":"1HVSDUmW3abkitZUoZsYMKZ2PbiKhr8Rdo","admin":"1HVSDUmW3abkitZUoZsYMKZ2PbiKhr8Rdo","block_index":"559411","block_hash":"0000000000000000000cddeb7a38abcba7bff08200b4127cbf37df1af958cbea","block_time":"1548044945"},"balance_satoshi":3000,"unconfirmed_tx_count":0};
    callback_getBtcAddressSummary('OK',test);
    */
    
    //var test={"odin_uri":"ppk:100#"};
    //callback_setNewOdin('OK',test);
}

function init(){
    console.log("init...");
    
    if(typeof(PeerWeb) !== 'undefined'){ //检查PPk开放协议相关PeerWeb JS接口可用性
        console.log("PeerWeb enabled");
        
        //读取PPk浏览器内置钱包中缺省用户身份标识
        PeerWeb.getDefaultODIN(
            'callback_getDefaultODIN'  //回调方法名称
        );
    }else{
        console.log("PeerWeb not valid");
        //alert("PeerWeb not valid. Please visit by PPk Browser For Android v0.2.6 above.");
        //document.getElementById("use_exist_odin").disabled=false;
    }
}

function callback_getDefaultODIN(status,obj_data){
    if('OK'==status){
        if(obj_data.odin_uri!=null || obj_data.odin_uri.trim().length>0){
            document.getElementById("exist_odin_uri").value=obj_data.odin_uri;
            getUserOdinInfo();
        }
    }else{
        alert("请先在浏览器里配置所要使用的ODIN标识！");
    }
}

//兼容DID的用户标识处理，得到以ppk:起始的URI
function getUserPPkURI(user_uri){ 
    if(user_uri.substr(0,"did:ppk:".length).toLowerCase()=="did:ppk:" ) { 
        user_uri=substr("did:".length,user_uri.length-"did:".length);
    }
    return user_uri;
}

function getUserOdinInfo(){
    document.getElementById("use_exist_odin").disabled=true;
    var exist_odin_uri=getUserPPkURI(document.getElementById("exist_odin_uri").value);

    //读取用户身份标识URI对应说明
    PeerWeb.getPPkResource(
        exist_odin_uri,
        'content',
        'callback_getUserOdinInfo'  //回调方法名称
    );
}

function callback_getUserOdinInfo(status,obj_data){
    if('OK'==status){
        try{
            var content=window.atob(obj_data.content_base64);
            //var content=obj_data.content_base64;
            //alert("type="+obj_data.type+" \nlength="+obj_data.length+"\nurl="+obj_data.url+"\ncontent="+content);
            mObjUserInfo = JSON.parse(content);
            
            var default_avtar_url='http://ppkpub.org/images/user.png';
            var exist_odin_uri=document.getElementById("exist_odin_uri").value;
            
            if(typeof(mObjUserInfo.name) !== 'undefined'){  //标准格式的PeerWeb用户定义
                document.getElementById("user_name").value=mObjUserInfo.name;
                document.getElementById("user_avtar_url").value=mObjUserInfo.avtar;
                document.getElementById('user_avtar_img').src=mObjUserInfo.avtar;
            }else if(typeof(mObjUserInfo.title) !== 'undefined'){  //直接使用ODIN标识的属性
                document.getElementById("user_name").value=mObjUserInfo.title.length>0 ? mObjUserInfo.title : exist_odin_uri ;
                document.getElementById("user_avtar_url").value=default_avtar_url;
                document.getElementById('user_avtar_img').src=default_avtar_url;
            }else{
                document.getElementById("user_name").value="anonymous匿名";
                document.getElementById("user_avtar_url").value=default_avtar_url;
                document.getElementById('user_avtar_img').src=default_avtar_url;
            }
            
            document.getElementById("use_exist_odin").disabled=false;
        }catch(e){
            alert("获得的用户信息有误!\n"+e);
        }
    }else{
        alert("无法获取对应用户信息！\n请检查确认下述ODIN标识:\n"+document.getElementById("exist_odin_uri").value);
    }
}

function authAsOdinOwner(){
    var exist_odin_uri=getUserPPkURI(document.getElementById("exist_odin_uri").value);
    var requester_uri='ppk:odinswap/';
    var auth_txt=requester_uri+','+exist_odin_uri+','+guid();  //需要签名的原文
    //alert('auth_txt:'+auth_txt);
    mTempDataHex = stringToHex(auth_txt);
    
    //请求用指定资源密钥来生成签名
    PeerWeb.signWithPPkResourcePrvKey(
        exist_odin_uri,
        requester_uri ,
        mTempDataHex,
        'callback_signWithPPkResourcePrvKey'  //回调方法名称
    );
}

function callback_signWithPPkResourcePrvKey(status,obj_data){
    try{
        if('OK'==status){
        
            //alert("res_uri="+obj_data.res_uri+" \nsign="+obj_data.sign+" \algo="+obj_data.algo);
            
            //验证签名
            PeerWeb.verifySign(
                mTempDataHex,
                mObjUserPubKey ,
                obj_data.sign,
                obj_data.algo,
                'callback_verifySign'  //回调方法名称
            );
        
        }else{
            alert("无法签名指定资源！\n请检查确认该资源已配置有效的验证密钥.");
        }
    }catch(e){
        alert("获得的签名信息有误!\n"+e);
    }
}

function callback_verifySign(status,obj_data){
    try{
        if('OK'==status){
            var user_uri=document.getElementById("exist_odin_uri").value;
            alert("验证用户身份成功\nODIN标识："+user_uri);
            useExistODIN();
        }else{
            alert("用户身份标识签名验证未通过！");
        }
    }catch(e){
        alert("验证签名信息有误!\n"+e);
    }
}

function guid() {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
        return v.toString(16);
    });
}

function checkExistODIN(){
    var user_uri=document.getElementById("exist_odin_uri").value.trim();
    if(typeof(mObjUserInfo.vd_set) !== 'undefined'){
        //设置有验证参数
        mObjUserPubKey=mObjUserInfo.vd_set.pubkey;
        //alert('mObjUserPubKey:'+mObjUserPubKey);
        authAsOdinOwner();
    }else{
        //未设置验证参数
        //alert("指定标识未设置密钥，无法验证身份！\nODIN标识："+user_uri+"\n请设置有效密钥后再重试。");
        alert("指定标识未设置密钥，将忽略验证仅供测试！\nODIN标识："+user_uri);
        useExistODIN();
    }
}

function useExistODIN(){
    var user_uri=document.getElementById("exist_odin_uri").value.trim();
    if(user_uri.length==0 
       || (
       user_uri.substr(0,"ppk:".length).toLowerCase()!="ppk:" 
       && user_uri.substr(0,"did:ppk:".length).toLowerCase()!="did:ppk:"
       )
      ) {
        alert("请输入有效的用户标识，如did:ppk:joy/btmid/alice#");
    }else{
        if( user_uri.substr(user_uri.length-1) !='#'){
            user_uri=user_uri+"#"; //尾部必须加上#
        }
        setCookie('swap_user_uri',user_uri,365);
        setCookie('swap_user_name',encodeURI(document.getElementById('user_name').value,"utf-8"),365);
        setCookie('swap_user_avtar_url',document.getElementById('user_avtar_url').value,365);

        self.location="<?php echo $back_url;?>";
    }
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
<?php
require_once "page_footer.inc.php";
?>
