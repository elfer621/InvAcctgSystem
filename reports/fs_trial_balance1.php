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
$output = preg_split( "/ (and) /", $_REQUEST['where'] );
$flag=false;
foreach($output as $key => $val){
	if($db->strpos_arr($val,array("Campus")) == true){
		$campus =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}elseif($db->strpos_arr($val,array("begdate")) == true){
		$begdate =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}elseif($db->strpos_arr($val,array("enddate")) == true){
		$enddate =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}else{
		if($flag)$where.=" and ";
		$where.= "$val";
		$flag=true;
	}
}
$where = $where?"where $where":"where a.fiscal_year='".date('Y')."'";
$report_type ="";
//$report_type ="and a.report_type!='PNL'";
$date1 = "and date < '".trim($begdate[1])."'";
$date2 = "and (date between '".trim($begdate[1])."' and '".trim($enddate[1])."')";

$sql = "select *,sum(begbal) sub_begbal,sum(total_dr) sub_dr,sum(total_cr) sub_cr,(sum(begbal)+sum(total_dr)-sum(total_cr)) sub_total from (
select * from (select (select variable_values from settings where variable_name='session_connect') cost_center,count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,sum(dr-cr) as begbal,0 as total_dr,0 as total_cr from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type $date1 group by account_code order by account_code) tbl1
 union 
select * from (select (select variable_values from settings where variable_name='session_connect') cost_center,count(*) num,a.account_type,a.account_code,a.account_group,a.account_desc,0 as begbal,sum(dr) as total_dr,sum(cr) as total_cr from tbl_journal_entry a left join tbl_chart_of_account b on a.account_code=b.account_code $where $report_type $date2 group by account_code order by account_code) tbl2
) tbl3 
group by account_code";
//echo $sql."<br/>";
$arrs=array();
if($_SESSION['connect']){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
}else{
	if($campus){
		$con->getBranch2(trim($campus[1]));
		$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
	}else{
		$arrs[]=$con->pdoStyle($GLOBALS['default_ip'],$GLOBALS['default_db'],$sql);
		if($_SESSION['settings']['connection_type']=="multiple"){
			foreach($_SESSION['conlist'] as $key => $val){
				$arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
			}
		}
	}
}
// echo count($arrs);
// exit;
$list = array();
foreach($arrs as $arr) {
	if(is_array($arr)) {
		$list = array_merge($list, $arr);
	}
}

//$list = $db->array_mesh($arrs[0],$arrs[1],$arrs[2],$arrs[3],$arrs[4],$arrs[5]);
// echo "<pre>";
// print_r($db->array_mesh($arrs[0],$arrs[1],$arrs[2],$arrs[3],$arrs[4],$arrs[5]));
// echo "</pre>";
?>
<div class="print" style="width:900px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<h2><?=$db->stockin_header;?><br/>Trial Balance<br/></h2>
	<div style="clear:both;height:10px;"></div>
	<?php
		foreach($output as $key => $val){
			echo $val."<br/>";
		}
	?>
	<div style="clear:both;height:5px;"></div>
	<table id="mytbl" class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Acct Group</th>
				<th>Account</th>
				<th>Bal Fwd</th>
				<th>Debit</th>
				<th>Credit</th>
				<th>Amount</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			$current_cat=null;
			$begbal=0;$dr=0;$cr=0;$total=0;
			$grand_begbal=0;$grand_dr=0;$grand_cr=0;$grand_total=0;
			//$number = mysql_num_rows(mysql_query($sql));
			$number =count($list);
			$i = 0;
			// echo "<pre>";
			// print_r($list);
			// echo "</pre>";
			// exit;
			usort($list, $db->make_cmp(['account_group' => "asc",'account_desc' => "desc"]));
			foreach($list as $key => $row){ 
				if($row['account_group']!=$current_cat){
					if($total!=0){ //for every grouping records
						echo "<tr><td colspan='2' style='text-align:right;'>$current_cat SubTotal</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($begbal,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($dr,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($cr,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($total,2)."</td></tr>";
					}
					echo "<tr><td colspan='4'><b>".$row['account_group']."</b></td></tr>";
					$current_cat=$row['account_group'];
					$begbal=0;$dr=0;$cr=0;$total=0;
				}
			?>
				<tr>
					<td><?=$row['account_group']?></td>
					<td><?="[{$row['account_code']}] ".$row['account_desc']." ({$row['num']})"?></td>
					<td style="text-align:right;"><?=number_format($row['sub_begbal'],2)?></td>
					<td style="text-align:right;"><?=number_format($row['sub_dr'],2)?></td>
					<td style="text-align:right;"><?=number_format($row['sub_cr'],2)?></td>
					<td style="text-align:right;"><?=number_format($row['sub_total'],2)?></td>
				</tr>
			<?php 
				$begbal+=$row['sub_begbal'];$dr+=$row['sub_dr'];$cr+=$row['sub_cr'];$total+=$row['sub_total'];
				$i ++;
				if($number==$i){ //for last grouping records
					echo "<tr><td colspan='2' style='text-align:right;'>$current_cat SubTotal</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($begbal,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($dr,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($cr,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($total,2)."</td></tr>";
				}
				$grand_begbal+=$row['sub_begbal'];$grand_dr+=$row['sub_dr'];$grand_cr+=$row['sub_cr'];$grand_total+=$row['sub_total'];
			} 
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='2'><b>Grand Total</b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_begbal,2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_dr,2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_cr,2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_total,2)?></b></td>
			</tr>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
