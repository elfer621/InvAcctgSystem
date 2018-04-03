<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Login Box HTML Code - www.PSDGraphics.com</title>

<link href="login-box.css" rel="stylesheet" type="text/css" />
<style>
body {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	color: #313030;
	background-color: #027fcf;
	background-image: url(../images/top-rept.jpg);
	background-repeat: repeat-x;
	background-position: left top;
	width: 1000px;
	margin: 0px auto;
}
</style>
</head>

<body>
<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();
if($_POST){
	print_r($_POST);
}
?>

<div style="padding: 100px 0 0 250px;">
	<form name="frmMainLogin" method="post">
		<div id="login-box">
			<H2>Login</H2>
			<div id="login-box-name" style="margin-top:20px;">User:</div><div id="login-box-field" style="margin-top:20px;"><input name="user" class="form-login" title="Username" value="" size="30" maxlength="2048" /></div>
			<div id="login-box-name">Password:</div><div id="login-box-field"><input name="password" type="password" class="form-login" title="Password" value="" size="30" maxlength="2048" /></div>
			<br />
			<!--<span class="login-box-options"><input type="checkbox" name="1" value="1"> Remember Me <a href="#" style="margin-left:30px;">Forgot password?</a></span>
			<br />-->
			<br />
			<a href="javascript:doLogin();"><img src="images/login-btn.png" width="103" height="42" style="margin-left:90px;" /></a>
		</div>
	</form>
</div>
<script>
	function doLogin(){
		
	}
</script>
</body>
</html>
