<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<style type="text/css">
		table.tbl {
			border:1px solid black;
			border-collapse: collapse;
			font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
		}
		table.tbl th,table.tbl2 th {
			border:1px solid black;
			height:20px;
			text-align:center;
		}
		table.tbl td {
			border:1px solid black;
			background-color: white;
			height:20px;
		}
		table.tbl2 td {
			border:none;
			background-color: white;
			height:20px;
		}
		.lbl{
			float:left;margin-right:10px;width:120px;
		}
		.logo {
			background-image: url('../images/emall_logo.jpg');
			background-size: 150px 130px;
			background-repeat: no-repeat;
			background-position: left center;
			
		}
		@media print {
			* {-webkit-print-color-adjust:exact;}
		}
	</style>
</head>
<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();
$info = $db->getWHERE("*","tbl_customers","where cust_id='{$_REQUEST['custid']}'");
$list = $con->resultArray($con->Nection()->query(
	"select a.* from tbl_soa_pdclist a 
	where custid='{$_REQUEST['custid']}'"
));
$seriesnum = $db->getWHERE("max(COALESCE(seriesnum,0))+1 nxtseries","tbl_soa_pdclist","group by custid order by seriesnum desc");
$series = $db->getWHERE("seriesnum","tbl_soa_pdclist","where custid='{$_REQUEST['custid']}' group by custid order by seriesnum desc");
// print_r($seriesnum);
// print_r($series);
if(!$series['seriesnum']){
	$q = mysql_query("update tbl_soa_pdclist set seriesnum={$seriesnum['nxtseries']} where custid='{$_REQUEST['custid']}'");
	if(!$q){
		echo mysql_error();
	}
}
?>
<body style="margin:0 auto 0;width:900px;font-size:13px;">
		<div class="logo" style="text-align:center;width:100%;">
			<h1>Cebu Central Realty Corporation</h1>
			<span style="font-size:12px;">N. Bacalso cor. Leon Kilat Sts., Cebu City<br/>TIN : 005-255-946-000</span>
			<div style="clear:both;height:50px;"></div>
		</div>
		<hr/>
			<div style="float:right;padding:10px;font-size:15px;">Series # <?=$series['seriesnum']?$db->customeFormat($series['seriesnum'],4):$db->customeFormat($seriesnum['nxtseries'],4)?></div>
			<div style="clear:both;height:25px;"></div>
			<h2 style="text-align:center;">ACKNOWLEDGEMENT RECEIPT</h2>
		<div style="clear:both;height:25px;"></div>
		<p style="font-size:15px;">This is to acknowledged receipt from <span style="font-weight:bold;"><?=$info['customer_name']?></span> of below item lists, as payment for Basic Rental.</p>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<th>For the Month</th>
				<th>Check Date</th>
				<th>Bank</th>
				<th>Check Num</th>
				<th>Amount</th>
			</tr>
			<?php
				$total=0;
				foreach($list as $key => $val){
					echo "<tr>
						<td>{$val['monthrent']}</td>
						<td>{$val['checkdate']}</td>
						<td>{$val['bank']}</td>
						<td>{$val['checknum']}</td>
						<td>".number_format($val['amount'],2)."</td>
					</tr>";
					$total+=$val['amount'];
				}
				echo "<tr><td colspan='4'>Total</td><td>".number_format($total,2)."</td></tr>";
			?>
		</table>
		<?php
		echo '<br/><br/><table class="tbl2" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td>Received By:</td>
				</tr>
				<tr>
					<td></td>
				</tr>
				<tr>
					<td>____________________</td>
				</tr></table>';
		?>
</body>
</html>