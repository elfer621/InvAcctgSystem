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
	}else if($db->strpos_arr($val,array("asofdate")) == true){
		$asofdate =preg_split("/[=.>.!=.<]+/", str_replace(array("`","'"),array("",""),$val));
	}else{
		if($flag)$where.=" and ";
		$where.= "$val";
		$flag=true;
	}
}
$where = $where?"where $where":"where a.fiscal_year='".date('Y')."'";
$report_type ="and a.report_type!='PNL'";
if(date('Y')=='2017'){
	$date = "and (date <= '".trim($asofdate[1])."' and date >='2017-04-30')";	
}else{
	$date = "and date <= '".trim($asofdate[1])."'";
}
if(date('Y')=='2017'){
$daterev="(`date` <= '".trim($asofdate[1])."' and `date`>='2017-05-01')";
}else{
$daterev="`date` <= '".trim($asofdate[1])."'";
}
//
$sql = "select *,b.id,if(sub_total>0,sub_total,0) sub_dr,if(sub_total<0,sub_total*-1,0) sub_cr,sub_total  from 
	(
		select count(*) num,a.account_type,a.account_code,a.account_group,b.account_desc,sum(dr) sub_dr,sum(cr) sub_cr,sum(dr-cr) sub_total from 
		tbl_journal_entry a 
		left join tbl_chart_of_account b on a.account_code=b.account_code 
		$where $report_type $date and date!='0000-00-00' group by account_code order by account_code
	) tbl3 
	left join tbl_group_sorting b on tbl3.account_group=b.group_name 
	group by account_code order by b.id asc,tbl3.account_desc";
//echo $sql."<br/>";
$costcenter = "if(COALESCE(a.center,'')='',(select variable_values from settings where variable_name='session_connect'),a.center) cost_center";
$rev = "select 
			$costcenter,
			sum(cr-dr) net_income from 
		tbl_journal_entry a 
		left join tbl_chart_of_account b on a.account_code=b.account_code 
		where ($daterev) and a.report_type='PNL' and (a.type!='BEGBAL') and date!='0000-00-00'";
//echo $rev;
$arrs=array();
$income_arrs=array();
if($_SESSION['connect'] or $_SESSION['settings']['connection_type']!="multiple"){
	$con->getBranch2(trim($_SESSION['connect']));
	$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
	$income_arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$rev);
}else{
	if($campus){
		$con->getBranch2(trim($campus[1]));
		$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
		$income_arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$rev);
	}else{

		
		if($_SESSION['settings']['connection_type']=="multiple"){
			foreach($_SESSION['conlist'] as $key => $val){
				$arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
				$income_arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$rev);
			}
		}else{
			$arrs[]=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
			$income_arrs[]=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$rev);
		}
	}
}
$list = array();
foreach($arrs as $arr) {
	if(is_array($arr)) {
		$list = array_merge($list, $arr);
	}
}


$revlist = array();
foreach($income_arrs as $arr) {
	if(is_array($arr)) {
		$revlist = array_merge($revlist, $arr);
	}
}

// echo "<pre>";
// print_r($revlist);
// echo "</pre>";
$newlist = array();
foreach($list as $key => $val){
	$newlist[$val['account_code']]["id"]=$val["id"];
	$newlist[$val['account_code']]["account_type"]=$val["account_type"];
	$newlist[$val['account_code']]["account_code"]=$val["account_code"];
	$newlist[$val['account_code']]["account_group"]=$val["account_group"];
	$newlist[$val['account_code']]["sub_account"]=$val["sub_account"];
	$newlist[$val['account_code']]["sub_account_group"]=$val["sub_account_group"];
	$newlist[$val['account_code']]["account_desc"]=$val["account_desc"];
	$newlist[$val['account_code']]["num"]+=$val['num'];
	$newlist[$val['account_code']]["sub_begbal"]+=$val["sub_begbal"];
	$newlist[$val['account_code']]["sub_dr"]+=$val["sub_dr"];
	$newlist[$val['account_code']]["sub_cr"]+=$val["sub_cr"];
	$newlist[$val['account_code']]["sub_total"]+=$val["sub_total"];
}
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
				<td colspan="7" style="text-align:center;"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;">Balance Sheet</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<td colspan="7">
					<?php
						foreach($output as $key => $val){
							echo $val."<br/>";
						}
					?>
				</td>
			</tr>
			<tr>
				<th>Acct Group</th>
				<th>Account</th>
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
			usort($newlist, $db->make_cmp(['id' => "asc", 'account_desc' => "asc"]));
			foreach($newlist as $key => $row){ 
				if($row['account_group']!=$current_cat){
					if($total!=0){ //for every grouping records
						echo "<tr><td colspan='2' style='text-align:right;'>$current_cat SubTotal</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($dr,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($cr,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($total,2)."</td></tr>";
					}
					echo "<tr><td colspan='5'><b>".$row['account_group']."</b></td></tr>";
					$current_cat=$row['account_group'];
					$begbal=0;$dr=0;$cr=0;$total=0;
				}
			?>
				<tr>
					<td><?=$row['account_group']?></td>
					<td><?="[{$row['account_code']}] ".$row['account_desc']." ({$row['num']})"?></td>
					<td style="text-align:right;"><?=number_format($row['sub_dr'],2)?></td>
					<td style="text-align:right;"><?=number_format($row['sub_cr'],2)?></td>
					<td style="text-align:right;"><?=number_format($row['sub_total'],2)?></td>
				</tr>
			<?php 
				$begbal+=$row['sub_begbal'];$dr+=$row['sub_dr'];$cr+=$row['sub_cr'];$total+=$row['sub_total'];
				$i ++;
				if($number==$i){ //for last grouping records
					echo "<tr><td colspan='2' style='text-align:right;'>$current_cat SubTotal</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($dr,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($cr,2)."</td>
							<td style='text-align:right;border-top:1px solid black;'>".number_format($total,2)."</td></tr>";
				}
				$grand_begbal+=$row['sub_begbal'];$grand_dr+=$row['sub_dr'];$grand_cr+=$row['sub_cr'];$grand_total+=$row['sub_total'];
			} 
			?>
			<?php 
			// echo "<pre>";
			// print_r($revlist);
			// echo "</pre>";
			if($revlist){
				$revdr=0;$revcr=0;$revtotal=0;
				echo "<tr><td colspan='4'><b>REVENUE</b></td></tr>"; 
				foreach($revlist as $key => $val){
					// echo "<tr>
							// <td>REVENUE</td>
							// <td>{$val['cost_center']}</td>
							// <td>PROFIT/LOSS/VARIANCE</td>
							// <td style='text-align:right;'>".number_format($val['grand_dr'],2)."</td>
							// <td style='text-align:right;'>".number_format($val['grand_cr'],2)."</td>
							// <td style='text-align:right;'>".number_format($val['grand_total'],2)."</td>
						// </tr>";
					$revtotal+=(double)$val['net_income'];
				}
				if((double)$revtotal<0){
					$revdr=(double)$revtotal * -1;
					$revtotal=$revtotal*-1;
				}elseif((double)$revtotal>0){
					$revcr=(double)$revtotal;
					$revtotal=$revtotal*-1;
				}
				
				echo "<tr>
							<td>REVENUE</td>
							<td>PROFIT/LOSS/VARIANCE</td>
							<td style='text-align:right;'>".number_format($revdr,2)."</td>
							<td style='text-align:right;'>".number_format($revcr,2)."</td>
							<td style='text-align:right;'>".number_format($revtotal,2)."</td>
					</tr>";
				
			}
			?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan='2'><b>Grand Total</b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_dr+$revdr,2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_cr+$revcr,2)?></b></td>
				<td style='text-align:right;border-top:1px solid black;border-bottom:1px solid black;'><b><?=number_format($grand_total+$revtotal,2)?></b></td>
			</tr>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>
