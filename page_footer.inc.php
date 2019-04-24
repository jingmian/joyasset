
<p align=center>
<?php if(strlen($g_currentUserODIN)>0){echo '<a href="user.php"><img src="image/user.png" width=16 height=16>我的帐号[',getSafeEchoTextToPage($g_currentUserODIN),']</a>';} else { echo '<a href="login.php">以兼容DID规范的ODIN标识登录</a>　|　<a href="new_user.php">注册新用户</a>';}   ?>
</p>

<p align=center><a href="./">参与竞拍</a>　|　<a href="new_sell.php">发布拍卖</a>　|<!--　<a href="buy_list.php">求购资产</a>　|-->　<a href="help.html">帮 助</a></p>

<h3 align="center"><font color="#F00">注意：此为演示版本，展示数据仅供测试，不作为真实交易依据！</font></h3>
<p align="center"><?php echo  '(Bytom network id: ',$gStrBtmNetworkId,')';?></p>
<div class="container-fluid footer">
PPkPub BytomAsset Auction Demo 0.1 &copy; 2019. Released under the <a href="http://opensource.org/licenses/mit-license.php">MIT License</a>.
</div>

</body>
</html>

