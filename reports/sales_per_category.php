<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
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
			font-size:15px !important;
		}
</style>
<style type="text/css" media="print" >
	thead {display: table-header-group !important;}
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
$sql="select DATE_FORMAT(a.timestamp, '%m') ymDate,b.category_name,sum(a.qty * a.selling) total_sales from tbl_sales_items a 
left join tbl_category b on a.category_id=b.category_id 
where DATE_FORMAT(a.timestamp, '%Y')=year(now()) 
group by a.category_id,DATE_FORMAT(a.timestamp, '%Y-%m')";
$con->getBranch2(trim($_SESSION['connect']));
$res=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
// echo "<pre>";
// print_r($res);
// echo "</pre>";
foreach($res as $k=>$v){
	$final_res[$v['category_name']][$v['ymDate']]=$v['total_sales'];
}
// echo "<pre>";
// print_r($final_res);
// echo "</pre>";
?>
<div class="print" style="width:950px;font-family:FontA11, Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<table id="mytbl" class="print tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<td colspan="13"><h2><?=$db->stockin_header;?><br/><span style="font-size:18px;">Sales Per Category Summary</span><br/><span style="font-size:15px;"><?=strtoupper($_SESSION['connect'])?></span></h2></td>
			</tr>
			<tr>
				<th>Services</th>
				<?php 
				$month = array('01'=>'(01) January','02'=>'(02) February','03'=>'(03) March','04'=>'(04) April','05'=>'(05) May',
					'06'=>'(06) June','07'=>'(07) July','08'=>'(08) August','09'=>'(09) September','10'=>'(10) October','11'=>'(11) November','12'=>'(12) December');
				foreach($month as $key => $val){
					echo "<th>$val</th>";
				}
				?>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach(array_keys($final_res) as $x => $y){
				echo "<tr>";
				echo "<td>$y</td>";
				$side_total=0;
				foreach($month as $key => $val){
					echo "<td style='text-align:right;'>".($final_res[$y][$key]?number_format($final_res[$y][$key],2):"")."</td>";
					$side_total+=$final_res[$y][$key];
					$bottom_total[$key]+=$final_res[$y][$key];
				}
				echo "<td style='text-align:right;'>".number_format($side_total,2)."</td>";
				echo "</tr>";
			}
		?>
		</tbody>
		<tfoot>
		<?php
			echo "<tr>";
			echo "<th>Total</th>";
			$grand_total=0;
			foreach($month as $key => $val){
				echo "<th style='text-align:right;'>".number_format($bottom_total[$key],2)."</th>";
				$grand_total+=$bottom_total[$key];
			}
			echo "<th style='text-align:right;'>".number_format($grand_total,2)."</th>";
			echo "</tr>";
		?>
		</tfoot>
	</table>
	<div style="clear:both;height:10px;"></div>
	<div id="dialog"></div>
	<div id="dialog2"></div>
</div>

