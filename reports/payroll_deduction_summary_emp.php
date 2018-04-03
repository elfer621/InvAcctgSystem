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

//$entries = $db->resultArray("*,group_concat(details,' -- <span>',format(amount,2),'</span>' SEPARATOR '</br>') list,sum(amount) total,concat(b.begdate,' - ',b.enddate) period","tbl_payroll_deduction a left join tbl_payroll_header b on a.payrollid=b.id","where empid='{$_REQUEST['empid']}' group by payrollid");
$emp = $db->getWHERE("*","tbl_employee","where id='{$_REQUEST['empid']}'");
//$entries = $db->resultArray("details,group_concat(b.begdate,' - ',b.enddate,' ',coalesce(a.rem,''),'<span>',format(amount,2),'</span>' SEPARATOR '</br>') list,sum(amount) total","tbl_payroll_deduction a left join tbl_payroll_header b on a.payrollid=b.id","where empid='{$_REQUEST['empid']}' group by details");
$entries = $db->resultArray("details,sum(adv_total) adv_total,sum(total) deduction_total,group_concat(list SEPARATOR '') list,(sum(adv_total) - sum(total)) bal",
"(select details,'' list,0 total,sum(amount) adv_total from tbl_payroll_advances a where empid='{$_REQUEST['empid']}' group by details
union
select details,group_concat(b.begdate,' - ',b.enddate,' ',coalesce(a.rem,''),'<span>',format(amount,2),'</span>' SEPARATOR '</br>') list,sum(amount) total,0 adv_total from tbl_payroll_deduction a left join tbl_payroll_header b on a.payrollid=b.id where empid='{$_REQUEST['empid']}' group by details
) tbl","group by details order by details");
// echo "<pre>";
// print_r($entries);
// echo "</pre>";
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
				<td colspan="5" style="text-align:center;"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;float:none;">Payroll Summary</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<td colspan="2">Employee Name</td>
				<td colspan="3" style="font-size:18px!important;"><?=$emp['first_name']." ".$emp['last_name']?></td>
			</tr>
			<tr>
				<th>Category</th>
				<th>Advances Total</th>
				<th>Deduction Details</th>
				<th>Deduction Total</th>
				<th>Balance</th>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach($entries as $key => $val){
				
				echo '<tr>
						<td style="text-align:left;">'.$val['details'].'</td>
						<td style="text-align:right;">'.number_format($val['adv_total'],2).'</td>
						<td style="text-align:left;">'.$val['list'].'</td>
						<td style="text-align:right;">'.number_format($val['deduction_total'],2).'</td>
						<td style="text-align:right;">'.number_format($val['bal'],2).'</td>
					</tr>';
				$total['adv_total']+=$val['adv_total'];
				$total['deduction_total']+=$val['deduction_total'];
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td style="text-align:center;font-weight:bold;">Total</td>
				<td style="text-align:right;font-weight:bold;"><?=number_format($total['adv_total'],2);?></td>
				<td></td>
				<td style="text-align:right;font-weight:bold;"><?=number_format($total['deduction_total'],2);?></td>
				<td></td>
			</tr>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
