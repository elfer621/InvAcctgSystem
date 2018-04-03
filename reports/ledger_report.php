<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$con=new dbUpdate();
$db->openDb();
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
$where = $where?"where $where":"where fiscal_year='".date('Y')."'";
$date = "and (`date` between '".trim($begdate[1])."' and '".trim($enddate[1])."')";
$sql = "select a.*,group_concat(concat(account_code,',',account_desc,',',dr,',',cr) SEPARATOR '|') entry,b.remarks,b.reference from (select a.* from tbl_journal_entry a $where $date) a left join tbl_vouchering b on a.refid=b.id and a.type=b.type group by a.refid";
//echo $sql;
$arrs=array();
// if($_SESSION['connect']){
	// $con->getBranch2(trim($_SESSION['connect']));
	// $arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
// }else{
	// if($_SESSION['settings']['connection_type']=="multiple"){
		// foreach($_SESSION['conlist'] as $key => $val){
			// $arrs[]=$con->pdoStyle($val['ipaddress'],$val['db_name'],$sql);
		// }
	// }else{
		// $arrs[]=$con->pdoStyle($_SESSION['default_ip'],$_SESSION['default_db'],$sql);
	// }
// }
$con->getBranch2(trim($_SESSION['connect']));
$arrs[]=$con->pdoStyle($con->ipadd2,$con->dbname2,$sql);
$list = array();
foreach($arrs as $arr) {
	if(is_array($arr)) {
		$list = array_merge($list, $arr);
	}
}
// $qry = mysql_query($sql);
// if(!$qry){
	// echo mysql_error();
// }
?>
<head>
	<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
	<script src="../jquery.table_navigation.js" type="text/javascript"></script>
	<script src="../js/myjs.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../styles.css" />
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
		}
		table.tbl td {
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
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
	</style>
</head>
<body class="print" style="margin:0 auto 0;width:1200px;font-size:12px;font-family:Calibri,Sans-Serif, Arial, Verdana, Geneva, Helvetica;">
	<fieldset class="menu" style="background-color:rgb(124, 187, 236);">
		<legend>Menu</legend>
		<input type="button" value="Export" onclick="ExportToExcel('mytbl');" style="float:left;width:100px;"/>
		<input type="button" value="Print" onclick="window.print();" style="float:right;width:100px;"/>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<h2><?=$db->stockin_header;?><br/>Review Journal Entry<br/></h2>
	<div style="clear:both;height:10px;"></div>
	<?php
		foreach($output as $key => $val){
			echo $val."<br/>";
		}
	?>
	<div style="clear:both;height:20px;"></div>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Account Code</th>
				<th>Accound Desc</th>
				<th>Reference</th>
				<th>DR</th>
				<th>CR</th>
			</tr>
		</thead>
		<tbody>
		<?php
			if(count($list)==0){
				echo "<tr><td colspan='8'>No records found...</td></tr>";
			}else{
			foreach($list as $key => $row){
		?>		
				<tr><th colspan='5' style="text-align:left!important;"><?="Entry #: ".$row['type']." ".$row['refid'] ." ".$row['date']." ".$row['reference']?></th></tr>
				<?php $dr=0;$cr=0;foreach(explode("|",$row['entry']) as $key => $val){ 
					$rec = explode(",",$val);
				?>
				<tr>
					<td><?=$rec[0]?></td>
					<td><?=$rec[1]?></td>
					<td><?=$rec[4]?></td>
					<td style='text-align:right;'><?=$rec[2]==0?"":number_format($rec[2],2)?></td>
					<td style='text-align:right;'><?=$rec[3]==0?"":number_format($rec[3],2)?></td>
				</tr>
				<?php $dr+=$rec[2];$cr+=$rec[3];} ?>
				<tr>
					<th colspan='3'><?=$row['remarks']?></th>
					<th style='text-align:right;font-weight:bold;'><?=number_format($dr,2)?></th>
					<th style='text-align:right;font-weight:bold;'><?=number_format($cr,2)?></th>
				</tr>
				<tr style="border:none;"><td colspan="5" style="border:none;color:red;">&nbsp;</td></tr>
			<?php }}	?>
		</tbody>
	</table>
</body>
