<?php
/*      PPK JoyAsset SwapService Setting  */
/*         PPkPub.org  20190415           */  
/*    Released under the MIT License.     */

//ini_set("display_errors", "On"); 
//error_reporting(E_ALL | E_STRICT);

require_once('config.inc.php');
require_once "ppk_joyblock.inc.php";

define('DID_URI_PREFIX','did:'); //DID标识前缀
define('PPK_URI_PREFIX','ppk:'); //ppk标识前缀
define('PPK_URI_RES_FLAG','#');  //ppk标识资源版本前缀


//BTC参数
define('MIN_FEE_SATOSHI',1000); //给矿工的费用,单位satoshi，即0.00000001 BTC
define('DUST_SATOSHI',1000); //DUST交易费用,单位satoshi，即0.00000001 BTC
define('MAX_OP_RETURN_LENGTH',75);//OP_RETURN能存放数据的最大字节数
define('BTC_SATOSHI_UNIT',100000000);

define('PPK_ODINSWAP_FLAG','JOYASSET'); //备注信息的特别标志
define('PPK_ODINSWAP_SERVICE_URI_PREFIX','ppk:JOY/asset/'); //服务资源前缀
define('JOYDID_PPK_URI_PREFIX','ppk:joy/btmid/'); //用户PPK标识前缀

define('PPK_ODINSWAP_LONGTIME_UTC',2123456789); //不设置持续时间的拍卖结束最大时间戳值，对应2037-04-16 09:06

define('PPK_ODINSWAP_STATUS_BID',0); //状态定义:拍卖中
define('PPK_ODINSWAP_STATUS_ACCEPT',1); //状态定义:达成意向
define('PPK_ODINSWAP_STATUS_PAY',2); //状态定义:已付款
define('PPK_ODINSWAP_STATUS_TRANSFER',3); //状态定义:资产已过户
define('PPK_ODINSWAP_STATUS_CANCEL',4); //状态定义:交易取消
define('PPK_ODINSWAP_STATUS_EXPIRED',5); //状态定义:到期确拍中
define('PPK_ODINSWAP_STATUS_FINISH',9); //状态定义:已完成


$g_dbLink=@mysqli_connect($dbhost,$dbuser,$dbpass,$dbname) or die("Can not connect to the mysql server!");
@mysqli_query($g_dbLink,"Set Names 'UTF8'");

//用户信息
if(isset($_COOKIE["swap_user_uri"])){
  $g_currentUserODIN=$_COOKIE["swap_user_uri"];
  $g_currentUserName=$_COOKIE["swap_user_name"];
}else{
  $g_currentUserODIN='';
  $g_currentUserName='';
}

//$g_currentUserODIN=DID_URI_PREFIX.JOYDID_PPK_URI_PREFIX.'alice#'; //tm1qzymnxuzlt6e8sjf4vc0ct6f6vkk25y27dtzdwe
//$g_currentUserName='TesterAlice';

//$g_currentUserODIN=DID_URI_PREFIX.JOYDID_PPK_URI_PREFIX.'bob#'; //tm1q8sarfnju2gyft56hh38w8n0s8xwq4tcsfaeqq8
//$g_currentUserName='测试Bob';

$g_cachedUserInfos=array();

require_once('ppk_swap.function.php');

//自动更新相关数据记录
autoUpdateExpiredSells();