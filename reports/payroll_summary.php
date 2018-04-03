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
	table.tbl td {
		border-width: 1px;
		border-style: none;
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
$header = $db->getWHERE("*","tbl_payroll_header","where id='{$_REQUEST['refid']}'");
$entries = $db->resultArray("a.*,b.assign_location","tbl_payroll_entry a left join tbl_employee b on a.empid=b.id","where refid='{$_REQUEST['refid']}'");
?>
<div class="print" style="width:1200px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<table id="mytbl" class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td colspan="20" style="text-align:center;"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;">Payroll Summary</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<td colspan="20" style="text-align:left;font-size:15px !important;"><h4>Payroll Period: <?=$header['begdate']." - ".$header['enddate']?><br/><?=$header['remarks']?></h4></td>
			</tr>
			<tr>
				<th style="width:40px;">Emp ID</th>
				<th>Employee Name</th>
				<th style="width:80px;">Daily Rate</th>
				<th style="width:80px;">Reg Pay</th>
				<th  style="width:80px;">OT Hours</th>
				<th  style="width:80px;">OT Amt</th>
				<th  style="width:80px;">Added</th>
				<th style="width:80px;">Gross Total</th>
				<th style="width:80px;">Adj</th>
				<th style="width:80px;">Sub Total</th>
				<td>&nbsp;</td>
				<th  style="width:80px;">SSS</th>
				<th  style="width:80px;">PhilHealth</th>
				<th  style="width:80px;">Pagibig</th>
				<th  style="width:80px;">Others</th>
				<th style="width:80px;">Less Total</th>
				<td>&nbsp;</td>
				<th  style="width:80px;">Net Total</th>
				<td>&nbsp;</td>
				<th  style="width:80px;">Cebu</th>
				<th  style="width:80px;">Surigao</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($entries as $key => $val){
				$ceb='';$surigao='';
				if($val['assign_location']=='Cebu'){
					$ceb=$val['nettotal'];
				}else{
					$surigao=$val['nettotal'];
				}
				echo '<tr>
						<td>'.$val['empid'].'</td>
						<td style="text-align:left;">'.$val['name'].'</td>
						<td>'.number_format($val['dailyrate'],2).'</td>
						<td>'.number_format($val['regtime'],2).'</td>
						<td>'.number_format($val['othours'],2).'</td>
						<td>'.number_format($val['otamt'],2).'</td>
						<td>'.number_format($val['added_amt'],2).'</td>
						<td style="font-weight:bold;">'.number_format($val['gross_total'],2).'</td>
						<td>'.number_format($val['adjustment'],2).'</td>
						<td>'.number_format($val['gross_total']+$val['adjustment'],2).'</td>
						<td>&nbsp;</td>
						<td>'.number_format($val['sss'],2).'</td>
						<td>'.number_format($val['philhealth'],2).'</td>
						<td>'.number_format($val['pagibig'],2).'</td>
						<td>'.number_format($val['others'],2).'</td>
						<td>'.number_format($val['lesstotal'],2).'</td>
						<td>&nbsp;</td>
						<td style="font-weight:bold;">'.number_format($val['nettotal'],2).'</td>
						<td>&nbsp;</td>
						<td>'.number_format($ceb,2).'</td>
						<td>'.number_format($surigao,2).'</td>
					</tr>';
				$total['regtime']+=$val['regtime'];
				$total['othours']+=$val['othours'];
				$total['otamt']+=$val['otamt'];
				$total['added_amt']+=$val['added_amt'];
				$total['gross_total']+=$val['gross_total'];
				$total['adjustment']+=$val['adjustment'];
				$total['subtotal']+=$val['gross_total']+$val['adjustment'];
				$total['sss']+=$val['sss'];
				$total['philhealth']+=$val['philhealth'];
				$total['pagibig']+=$val['pagibig'];
				$total['others']+=$val['others'];
				$total['lesstotal']+=$val['lesstotal'];
				$total['nettotal']+=$val['nettotal'];
				$total['ceb']+=(double)$ceb;
				$total['surigao']+=(double)$surigao;
			}
			?>
		</tbody>
		<tfoot>
			<?php
			echo '<tr>
						<th style="text-align:left;" colspan="3">Sub Total</th>
						<th>'.number_format($total['regtime'],2).'</th>
						<th>'.number_format($total['othours'],2).'</th>
						<th>'.number_format($total['otamt'],2).'</th>
						<th>'.number_format($total['added_amt'],2).'</th>
						<th>'.number_format($total['gross_total'],2).'</th>
						<th>'.number_format($total['adjustment'],2).'</th>
						<th>'.number_format($total['subtotal'],2).'</th>
						<td>&nbsp;</td>
						<th>'.number_format($total['sss'],2).'</th>
						<th>'.number_format($total['philhealth'],2).'</th>
						<th>'.number_format($total['pagibig'],2).'</th>
						<th>'.number_format($total['others'],2).'</th>
						<th>'.number_format($total['lesstotal'],2).'</th>
						<td>&nbsp;</td>
						<th>'.number_format($total['nettotal'],2).'</th>
						<td>&nbsp;</td>
						<th>'.number_format($total['ceb'],2).'</th>
						<th>'.number_format($total['surigao'],2).'</th>
					</tr>';
			?>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
