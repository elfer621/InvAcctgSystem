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
			size: A4;
		}
		@media print {
		  .docsig{
			  bottom:200px!important;
			  
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
$results = $db->getWHERE("*","lab_xray_results","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}'");
$info = explode("|",$results['patient_info']);
?>
<body style="font-size:20px;">
<div  style="font-family:Arial, Verdana, Geneva, Helvetica, Sans-Serif;width:1000px;margin:0 auto;">
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
<h4>Findings:</h4>
<?=$results['findings']?>
<div style="clear:both;height:50px;"></div>
<h4>IMPRESSION:</h4>
<?=$results['impression']?>
<div style="clear:both;height:50px;"></div>
<div class="docsig" style="width:800px;position:absolute;bottom:50px;right:10px;text-align:center;">
<?php if($results['doctor_signature']=="DIOGENES R. LABAJO, MD, DPBR"){ ?>
<img src="../../../images/drlabajosig.png" style="width:300px;position:relative;bottom:-40px;"/>
<?php }else{ ?>
<img src="../../../images/DRALEXSIGNATURE.png" style="width:200px;position:relative;bottom:-25px;"/>
<?php } ?>
<div style="border-bottom:1px solid black;width:50%;margin:0 auto;"><?=$results['doctor_signature']?></div>
<div style="font-size:10px;width:50%;margin:0 auto;">Radiologist/Sonologist</div>
</div>

</div>
<?php
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