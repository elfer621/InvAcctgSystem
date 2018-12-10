<?php
echo $_SESSION['error'];
$time = microtime(true);
ob_start("ob_gzhandler");
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
set_time_limit(0);
ini_set('memory_limit', '-1');
session_start();
require_once"settings.php";
require_once"class/dbUpdate.php";
require_once"class/dbConnection.php";
require_once"class/pagination.class.php";
$db=new dbConnect();
$p=new pagination();
$con=new dbUpdate();

if($_REQUEST['error_msg']){
	//echo "<script>alert('".$_REQUEST['error_msg']."');</script>";
	echo "<script>window.onload=function(){errorMsg('".$_REQUEST['error_msg']."');}</script>";
}
if(isset($_REQUEST['connect'])){
	if($_REQUEST['connect']=="main"){
		unset($_SESSION['connect']);
	}else{
		$_SESSION['connect']=$_REQUEST['connect'];
	}
}
//$con = $db->openDb($_SESSION['dbname']?$_SESSION['dbname']:"lizgan_main");
$db->openDb();
// if(!$con){
	// $db->openDb_local();
// }
$location = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if(isset($_REQUEST['xlogin'])){
	$qry = mysql_query("select * from tbl_user where user='".$_REQUEST['user']."' and password='".$_REQUEST['password']."' limit 1");
	$info = mysql_fetch_assoc($qry);
	if(mysql_num_rows($qry)>0){
		switch($_REQUEST['type_name']){
			case'reading_end_server':
				if($info['restriction_id']==1||$info['restriction_id']==4){
					$readingref = $db->getServerReadingnum();
					$qry_endreading2 = mysql_query("update tbl_reading_server set end_date=now(),end_time=curtime(),total_amount=(select sum(amount) as total_sales from `tbl_reading` where reading_num='".$db->getServerReadingnum()."' group by reading_num limit 1) where reading_num='".$db->getServerReadingnum()."'");
					if(!$qry_endreading2){
						echo "<script>window.onload=function(){alertMsg('".mysql_real_escape_string(mysql_error())."');}</script>";
					}else{
						$readingserver_type = "./reports/reading_end.php?readingnum=".$readingref."&all=true";
						echo "<script>window.onload=function(){loadiFrame('$readingserver_type');PrintIframe();};</script>";
						//session_destroy();
					}
				}else{
					echo "<script>alert('You are not allowed to perform this operation...');</script>";
				}
			break;
			case'reading_end':
				if($info['restriction_id']==1||$info['restriction_id']==4){
					// $total=$db->getWHERE("sum(amount) as total_sales","`tbl_sales_receipt_{$_SESSION['counter_num']}`","
									// where reading='".$_SESSION['readingnum']."' and counter_num='".$_SESSION['counter_num']."' and type!='VOID' group by reading,counter_num");
					$qry_endreading = mysql_query("update tbl_reading set end_date=now(),end_time=curtime(),
							amount=(select sum(amount) as total_sales from `tbl_sales_receipt_{$_SESSION['counter_num']}` 
									where reading='".$_SESSION['readingnum']."' and counter_num='".$_SESSION['counter_num']."' and type!='VOID' group by reading,counter_num limit 1) 
						where reading_num='".$_SESSION['readingnum']."' and counter='".$_SESSION['counter_num']."'");
					if(!$qry_endreading){
						echo "<script>window.onload=function(){alertMsg('".mysql_real_escape_string(mysql_error())."');}</script>";
					}else{
						$reading_type = "./reports/reading_end.php?readingnum={$_SESSION['readingnum']}&counter_num={$_SESSION['counter_num']}";
						echo "<script>window.onload=function(){loadiFrame('$reading_type');PrintIframe();};</script>";
					}
				}else{
					echo "<script>alert('You are not allowed to perform this operation...');</script>";
				}
			break;
			case'salesvoid':
				if($info['restriction_id']==1||$info['restriction_id']==4){
					echo "<script>window.onload=function(){clickDialog('dialogbox',450,150,'salesvoid','Sales Void');}</script>";
				}else{
					echo "<script>alert('You are not allowed to perform this operation...');</script>";
				}
			break;
			case'reprint':
				if($info['restriction_id']==1||$info['restriction_id']==4){
					echo "<script>window.onload=function(){reprintReports();}</script>";
				}else{
					echo "<script>alert('You are not allowed to perform this operation...');</script>";
				}
			break;
			case'discounting':
				if($info['restriction_id']==1||$info['restriction_id']==4){
					echo "<script>window.onload=function(){clickDialog('dialogbox',400,300,'discounting_frm&total_amount='+parseFloat(($('#xtotal').html()).replace(',','')),'Discounting');$('#disc_percent').focus();}</script>";
				}else{
					echo "<script>alert('You are not allowed to perform this operation...');</script>";
				}
			break;
			case'belowcost':
				if($info['restriction_id']==1||$info['restriction_id']==4){
					echo "<script>window.onload=function(){
							$.ajax({
								url: './content/pos_ajax.php?execute=pricechange&barcode={$_REQUEST['barcode']}&newprice={$_REQUEST['newprice']}',
								type:'POST',
								success:function(data){
									if(data=='success'){
										window.location=document.URL;
									}else{
										alert(data);
									}
								}
							});
						}</script>";
				}else{
					echo "<script>alert('You are not allowed to perform this operation...');</script>";
				}
			break;
			case'negativeQty':
				if($info['restriction_id']==1||$info['restriction_id']==4){
					echo "<script>window.onload=function(){qtychange('{$_REQUEST['bcode']}','{$_REQUEST['newqty']}');}</script>";
				}else{
					echo "<script>alert('You are not allowed to perform this operation...');</script>";
				}
			break;
			default:
				$_SESSION['xuser_pos']=$info['user'];
				$_SESSION['restrictionid']=$info['restriction_id'];
				$_SESSION['xid']=$info['id'];
				$_SESSION['complete_name'] = $info['first_name']." ".$info['last_name'];
				//$_SESSION['counter_num']=$info['counter_num'];
				//$_SESSION['readingnum']=$db->getReadingnum($_SESSION['counter_num']);
				if($_SESSION['restrictionid']==1&&$mode=="branch"){
					// echo "ProductName:".$con->updateItems("tbl_product_name")."\n";
					// echo "Barcode:".$con->updateItems("tbl_barcodes")."\n";
					// echo "Packages:".$con->updateItems("tbl_packages")."\n";
					//header("location: ./?page=main");
				}else if($_SESSION['restrictionid']==2){
					header("location: ./?page=sales");
				}
			break;
		}
	}else{
		echo "<script>alert('Wrong password!');</script>";
	}
}
if((!isset($_SESSION['xid']) or !isset($_SESSION['settings']))&&$_REQUEST['page']!="estore"){
	echo "<script>window.onload=function(){
		$('#xlogin').dialog({
			autoOpen: false,
			width: 490,
			height: 250,
			modal: true,
			resizable: false,
			closeOnEscape: false,
			title:'Login',
			open: function(event, ui) { $('.ui-dialog-titlebar-close').hide();$('.ui-widget-overlay').css({opacity:1}); }
		});
		htmlobj=$.ajax({url:'./content/pos_ajax.php?execute=xlogin',async:false});
		$('#xlogin').html(htmlobj.responseText);
		$('#xlogin').dialog('open');
	}</script>";
}
//reading area...
if(isset($_REQUEST['startreading'])){
	//$sql = "insert into tbl_reading set reading_num='".$db->readingNext($_SESSION['counter_num'])."',start_date='".$_REQUEST['date']."',counter='".$_SESSION['counter_num']."'";
	$sql = "insert into tbl_reading set reading_num='".$_SESSION['serverReading']."',start_date='".$_REQUEST['date']."',start_time=curtime(),counter='".$_SESSION['counter_num']."',cashier_name='{$_SESSION['complete_name']}'";
	$qry = mysql_query($sql);
	if(!$qry){
		//echo "<script>window.onload=function(){alertMsg('".mysql_real_escape_string(mysql_error())."');}</script>";
		$_SESSION['error']="StartReading: ".mysql_error();
	}else{
		$_SESSION['readingnum']=$db->getReadingnum($_SESSION['counter_num']);
	}
}
if($_REQUEST['startserverreading']){
	$sql = "insert into tbl_reading_server set start_date='".$_REQUEST['date']."',user='".$_SESSION['xuser_pos']."',start_time=curtime(),cashier_name='{$_SESSION['complete_name']}'";
	$qry = mysql_query($sql);
	if(!$qry){
		echo "<script>window.onload=function(){alertMsg('".mysql_real_escape_string(mysql_error())."');}</script>";
	}
}
//reading area...

if($_POST['changepass']){
	$qry = mysql_query("select * from tbl_user where user='".$_SESSION['xuser_pos']."' and password='".$_REQUEST['current_password']."' limit 1");
	IF(mysql_num_rows($qry)>0){
		if($_REQUEST['new_password1']==$_REQUEST['new_password2']){
			$qry_pass = mysql_query("update tbl_user set password='".$_REQUEST['new_password1']."' where user='".$_SESSION['xuser_pos']."'");
			if($qry_pass){
				echo "<script>alert('Password successfully change...');</script>";
			}
		}else{
			echo "<script>alert('New password doesn\'t match the second password...');</script>";
		}
	}ELSE{
		echo "<script>alert('Password authentication failed! You type wrong password...');</script>";
	}
	
}

switch($_REQUEST['page']){
	case'order':
		$content ="pos_content.php";
		//$content ="pos_content_rber.php";
	break;
	case'sales':
		$content ="pos_content.php";
		//$content ="pos_content_rber.php";
	break;
	case'estore':
		$content ="estore.php";
	break;
	default:
		$content ="dashboard.php";
	break;
}
require_once"main.php";
$db->closeDb();
unset($qry);unset($sql);unset($row);
echo "<p>Time elapsed: ",microtime(true) - $time, " seconds";
?>
<div id="iframeplaceholder" style="display:none;"></div>
<div id="iframeplaceholder2" style="display:none;"></div>