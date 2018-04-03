<?php
if($_REQUEST['error_msg']){
	//echo "<script>alert('".$_REQUEST['error_msg']."');</script>";
	//echo "<script>window.onload=function(){errorMsg('".$_REQUEST['error_msg']."');}</script>";
	//header("Location: ../?error_msg=Unable to connect!");
	echo "Unable to connect in Admin Office...";
}
?>