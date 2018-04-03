<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<head>
	<style type="text/css">
		table.tbl {
			border-width: 0px;
			border-spacing: 0px;
			border-style: none;
			border-collapse: collapse;
			font-family:Arial, Verdana, Geneva, Arial, Helvetica, Sans-Serif;
			
		}
		table.tbl thead th {
			border-width: 1px;
			border-style: solid;
			border-color: gray;
			height:20px;
			/*background-color:rgb(237,238,240);*/
			text-align:center;
			font-size:18px;
			padding:1px;
		}
		
		table.tbl td {
			font-size:18px;
			border-width: 1px;
			border-style: none;
			border-color: gray;
			background-color: white;
			height:20px;
			padding:1px;
		}
		.lbl{
			float:left;margin-right:10px;width:150px;
			font-weight:bold;
		}
		@page {
			size: Legal;
		}
		@media print{    
			.no-print, .no-print *
			{
				display: none !important;
			}
			.per_slip{
				height:800px;
				padding-top:5px;
				font-size:18px!important;
			}
			.brk {
				page-break-after:always;
			}
		}
</style>
</head>
<?php
session_start();
require_once"../../../settings.php";
require_once"../../../class/dbConnection.php";
require_once"../../../class/dbUpdate.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();
if($_REQUEST['skuid']){
	$results = $db->resultArray("*","lab_results","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}' and category_id='{$_REQUEST['catid']}' and skuid='{$_REQUEST['skuid']}'");
}else{
	$results = $db->resultArray("*","lab_results","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}'");
}
foreach($results as $key => $lab){
	if($i==3)$i=1;
	$info = explode("|",$lab['patient_info']);
?>
<body style="font-size:18px;">
<div class="per_slip" style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:1000px;margin:0 auto;">
<div style="border-bottom:1px solid #000;">
	<img src="../../../images/csacci_logo.jpg" style="width:110px;height:110px;float:left;"/>
	<div style="float:right;text-align:center;">
		<span style="text-align:center;font-size:25px;"><b>CEBU SPECIALISTS AMBULATORY CARE CENTER, INC.</b></span><br/>
		<span style="text-align:center;font-size:18px;">2nd Floor BAI Center, Cebu South Road</span><br/>
		<span style="text-align:center;font-size:18px;">Basak San Nicolas, Cebu City</span><br/>
		<span style="text-align:center;font-size:18px;">Tel #: (032) 417-5173 / (032) 343-4745</span><br/>
	</div>
	<div style="clear:both;height:10px;"></div>
</div>
<div style="clear:both;height:25px;"></div>
<div style="float:left;width:45%;">
	<div class="lbl">Name:</div>
	<div style="float:left;width:60%;"><?php echo strtoupper($info[0]." ".$info[1]); ?></div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">Req Physician:</div>
	<div style="float:left;width:85%;"><?php echo strtoupper($info[2]) ?></div>
</div>
<div style="float:right;width:45%;">
	<div class="lbl">Date:</div>
	<div style="float:left;"><?php echo $info[5] ?></div>
	<div style="clear:both;height:5px;"></div>
	<div class="lbl">Sex:</div>
	<div style="float:left;margin-right:10px;"><?php echo $info[4] ?></div>
	<div class="lbl">Age:</div>
	<div style="float:left;"><?php echo $info[3] ?></div>
</div>
<div style="clear:both;height:25px;border-bottom:1px solid #000;"></div>
<div style="font-size:15px;text-align:center;padding:20px 0;font-weight:bold;"><?=strtoupper($lab['report_title']);?></div>
<div style="clear:both;height:10px;"></div>

<?php
$category = explode("|",$lab['category_label']);
$fields_label = explode("|",$lab['fields_label']);
$unit_label = explode("|",$lab['unit_label']);
$reference_range = explode("|",$lab['reference_range']);
$results_val = explode("|",$lab['lab_results']);

if($lab['report_title']=="Urinalysis"){
	echo $tblhead = '<table cellspacing="0" cellpadding="0" style="'.((count($fields_label)>10)?"width:45%;float:left;margin-right:25px;":"width:100%").'" >
		<thead>
			<tr>
				<th style="border:none;">Parameter</th>
				<th style="border:none;width:150px;">Results</th>
			</tr>
			<tr><th colspan="4">&nbsp;</th></tr>
		</thead>
		<tbody>';

	for($x=0;$x<count($fields_label);$x++){
		if($category[$x]){
			echo "<tr><td colspan='4'><b>{$category[$x]}</b></td></tr>";
		}
		echo "<tr>
			<td style='padding-left:25px;'>{$fields_label[$x]}</td>";
		echo "<td style='text-align:center;border-bottom:1px solid #000;'>{$results_val[$x]}</td></tr>";
		if(count($fields_label)>10){
			if($x==(count($fields_label)/2)){
				echo "</tbody></table>";
				echo $tblhead;
				echo "<tr><td>&nbsp;</td></tr>";
			}
		}
	}
	echo "</tbody></table>";
}else{
	echo $tblhead = '<table cellspacing="0" cellpadding="0" style="'.((count($fields_label)>10)?"width:45%;float:left;margin-right:25px;":"width:100%").'" >
		<thead>
			<tr>
				<th style="border:none;">Parameter</th>
				<th style="border:none;width:150px;">Results</th>
				<th style="border:none;">Units</th>
				<th style="border:none;">Reference Range</th>
			</tr>
			<tr><th colspan="4">&nbsp;</th></tr>
		</thead>
		<tbody>';

	for($x=0;$x<count($fields_label);$x++){
		if($category[$x]){
			echo "<tr><td colspan='4'><b>{$category[$x]}</b></td></tr>";
		}
		echo "<tr>
			<td style='padding-left:25px;'>{$fields_label[$x]}</td>";
		echo "<td style='text-align:center;border-bottom:1px solid #000;'>{$results_val[$x]}</td>";
		echo "<td style='text-align:center;'>{$unit_label[$x]}</td>
				<td style='text-align:center;'>{$reference_range[$x]}</td>
			</tr>";
		if(count($fields_label)>10){
			if($x==(count($fields_label)/2)){
				echo "</tbody></table>";
				echo $tblhead;
				echo "<tr><td>&nbsp;</td></tr>";
			}
		}
	}
	echo "</tbody></table>";
}
?>
<div style="clear:both;height:50px;"></div>
<table style="width:100%;position:relative;bottom:10px;" border="0">
	<tr>
		<td style="text-align:center;">
			<div style="border-bottom:1px solid black;width:50%;margin:0 auto;"></div>
			<div style="font-size:10px;width:50%;margin:0 auto;">MEDICAL TECHNOLOGIST</div>
		</td>
		<td style="text-align:center;">
			<img src="../../../images/docsig.png" style="position:absolute;bottom:5px;right:135px;width:210px;" />
			<div style="border-bottom:1px solid black;width:50%;margin:0 auto;">Susan B. Abanilla, MD</div>
			<div style="font-size:10px;width:50%;margin:0 auto;">PATHOLOGIST<br/>LIC#041207<br/>PTR#1175017</div>
		</td>
	</tr>
</table>
<?php if($i==2){echo "<div class='brk'></div>";} ?>
</div>
<?php

	$i++;
}
$db->closeDb();
?>
<script>
onload=function(){
	//window.print();
	//self.close();
}
</script>
</body>
</html>