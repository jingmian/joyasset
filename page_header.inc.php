<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>比原数字资产拍卖交易工具（演示） - BytomAsset Auction Tool(Demo) </title>
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-default" role="navigation">
<div class="container-fluid">
  <!-- Brand and toggle get grouped for better mobile display -->
  <div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navbar-collapse-1">
      <span class="sr-only">Toggle navigation</span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
      <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand logo" href="./">数字资产拍卖</a>
  </div>

  <!-- Collect the nav links, forms, and other content for toggling -->
  <div class="collapse navbar-collapse" id="bs-navbar-collapse-1">
    <ul class="nav navbar-nav">
        <li><a href="./">参与竞拍</a></li>
        <li><a href="new_sell.php">发布拍卖</a></li>
        <!--<li><a href="buy_list.php">求购资产</a></li>-->
        <li ><?php if(strlen($g_currentUserODIN)>0){echo '<a href="user.php"><img src="image/user.png" width=16 height=16>我的帐号[',getSafeEchoTextToPage($g_currentUserODIN),']</a>';} else { echo '<a href="login.php">以兼容DID规范的ODIN标识登录</a></li><li><a href="new_user.php">注册新用户</a>';}   ?></li>
        <li><a href="help.html">帮助</a></li>
    </ul>
  </div>
</div>
</nav>
