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
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$list = $con->resultArray($con->Nection()->query(
	"select a.*,b.customer_name,b.mall_unit_number,c.`Description` from tbl_soa_other_charges_daily a 
	left join tbl_customers b on a.custid=b.cust_id
	left join tbl_soa_other_charges_category_name c on a.category_id=c.category_id
	where a.forthemonth='{$_REQUEST['forthemonth']}'"
));
?>
<body style="margin:0 auto 0;width:900px;font-size:13px;">
		<div class="logo" style="text-align:center;width:100%;">
			<h1>Cebu Central Realty Corporation</h1>
			<span style="font-size:12px;">N. Bacalso cor. Leon Kilat Sts., Cebu City<br/>TIN : 005-255-946-000</span>
			<div style="clear:both;height:50px;"></div>
		</div>
		<hr/>
			<div style="clear:both;height:25px;"></div>
			<h2 style="text-align:center;">SECURITY REPORT</h2>
		<div style="clear:both;height:25px;"></div>
		<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<th>Date</th>
				<th>Tenant</th>
				<th>Time</th>
				<th>Location</th>
				<th>Violation</th>
				<th>Remarks</th>
			</tr>
			<?php
				foreach($list as $key => $val){
					echo "<tr>
						<td>{$val['date']}</td>
						<td>{$val['customer_name']}</td>
						<td>{$val['stime']}</td>
						<td>{$val['mall_unit_number']}</td>
						<td>{$val['Description']}</td>
						<td>{$val['reference']}</td>
					</tr>";
				}
			?>
		</table>
		<?php
		echo '<br/><br/><table class="tbl2" cellspacing="0" cellpadding="0" width="100%">
				<tr>
					<td>PREPARED BY:</td>
					<td>CHECKED & VERIFIED BY:</td>
					<td>NOTED BY:</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
				</tr>
				<tr>
					<td>____________________</td>
					<td>____________________</td>
					<td>____________________</td>
				</tr></table>';
		?>
</body>
</html>