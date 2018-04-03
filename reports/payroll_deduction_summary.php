<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<script type="text/javascript" src="../js/myjs.js"></script>
<link rel="stylesheet" href="../css/print.css" type="text/css" media="print" />
<style type="text/css">
	table.tbl,table.tbl2 {
		border-width: 0px;
		border-spacing: 0px;
		border-style: none;
		border-collapse: collapse;
		font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
		
	}
	table.tbl th,table.tbl2 th {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		height:20px;
		text-align:center;
		font-size:12px;
		padding:0 3px 0 3px;
	}
	table.tbl tbody td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
		font-size:12px;
		padding:0 3px 0 3px;
		text-align:right;
	}
	table.tbl2 td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
		text-align:center;
	}
	.lbl{
		float:left;margin-right:10px;width:120px;
	}
	body,td {
			font-size:12px !important;
		}
	td span {
		float:right;
	}
	tfoot td {
		border:1px solid gray;
	}
</style>

<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$con=new dbUpdate();
$db=new dbConnect();
$db->openDb();

$entries = $db->resultArray("*,group_concat(details,' -- <span>',format(amount,2),'</span>' SEPARATOR '</br>') list,sum(amount) total","tbl_payroll_deduction","where payrollid='{$_REQUEST['payrollid']}' group by empid");
?>
<div class="print" style="width:900px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<table id="mytbl" class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td colspan="4" style="text-align:center;"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;float:none;">Payroll Summary</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<th style="width:40px;">Emp ID</th>
				<th>Employee Name</th>
				<th>Deduction Details</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($entries as $key => $val){
				$emp = $db->getWHERE("*","tbl_employee","where id='{$val['empid']}'");
				echo '<tr>
						<td>'.$val['empid'].'</td>
						<td style="text-align:left;">'.$emp['first_name']." ".$emp['last_name'].'</td>
						<td style="text-align:left;">'.$val['list'].'</td>
						<td style="text-align:right;">'.number_format($val['total'],2).'</td>
					</tr>';
				$subtotal+=$val['total'];
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3" style="text-align:center;font-weight:bold;">Total</td>
				<td style="text-align:right;font-weight:bold;"><?=number_format($subtotal,2);?></td>
			</tr>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
