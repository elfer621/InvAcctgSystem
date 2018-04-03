<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />

<!-- stylesheets -->
<link href="../canvasjs/assets/bootstrap.min.css" rel="stylesheet">
<link href="../canvasjs/assets/style.css" rel="stylesheet">
<link href="../canvasjs/assets/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<!-- scripts -->

<!--[if lt IE 9 ]> 
<script src="../canvasjs/assets/js/html5shiv.min.js"></script>
<script src="../canvasjs/assets/js/respond.min.js"></script>
<![endif]-->

<script src="../canvasjs/assets/js/jquery-1.12.4.min.js"></script>
<script src="../canvasjs/assets/js/bootstrap.min.js"></script>
<style type="text/css">
	body{
		font-size:15px;
	}
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
		border-style: solid;
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
	h2{
		background-color:#255892;
		color:white;
		font-size:18px;
		padding:5px;
	}
	ul {
		width:100%;
		padding:0;
	}
	
	li {
		list-style:none;
		background-color:#255892;
		color:white;
		font-size:18px;
		padding:5px;
		margin-bottom:5px;
	}
	li a,li a:hover {
		color:white;
	}
	
</style>
<script src="../canvasjs/canvasjs.min.js"></script>

</head>
<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();


?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
<ul>
	<li>1. Executive Summary / II. Rationale / Objective</li>
	<li><a href="#pg1">III. Compliance Rate</a></li>
	<li><a href="#pg2">IV. Age and Gender Breakdown</a></li>
	<li><a href="#pg3">V. Analysis of Results</a></li>
	<li><a href="#pg4">VI. Examinations</a></li>
	<li><a href="#pg5">VI.1 Physical Examination</a></li>
	<li><a href="#pg6">VI.2 Complete Blood Count  </a></li>
	<li><a href="#pg7">VI.3  Blood Chemistry</a></li>
	<li><a href="#pg8">VI.4  Urinalysis</a></li>
	<li><a href="#pg9">VI.5 Fecalysis</a></li>
	<li><a href="#pg10">VI.6 Chest X-ray</a></li>
	<li><a href="#pg11">VI.7 Electrocardiogram</a></li>
	<li><a href="#pg12">VI.8 Pap Smear</a></li>
	<li><a href="#pg13">VII. Body Mass Index</a></li>
	<li><a href="#pg14">VIII. Top Ten Findings</a></li>
	<li><a href="#pg15">IX. Case Discussion</a></li>
	<li><a href="#pg16">X. Glossary</a></li>
	<li><a href="#pg17">XI. Provider’s Information</a></li>
</ul>	
	
	
<!--------------------------------------------------->	
	<?php
		$res = $db->resultArray("count(*) num,if(compliance is null,'Not Started',compliance) status","data_teletech_patient","group by compliance");
		$total = 0;
		foreach($res as $key => $val){
			$dataCompliance[] = array(
				"y"=>$val['num'],
				"legendText" => $val['status'],
				"label"=>$val['status']
				);
			$rec[$val['status']]=$val['num'];
			$total+=$val['num'];
		}
	?>
	<h2 id="pg1">III. Compliance</h2>
		<p>Compliance reports refers to the Physical presence of an individual for Annual Health Check. A  thorough examination will provide an accurate reflection of  the medical picture of an  institution. Likewise a poorly attended Annual Health Check defeats the purpose  of a good Demographic report.</p>
	<h2>III. 1 Compliance Table</h2>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th rowspan="2">Target Population</th>
				<th rowspan="2">Actual</th>
				<th colspan="2">Variance</th>
				<th rowspan="2">% Total</th>
			</tr>
			<tr>
				<th>Incomplete</th>
				<th>Not Started</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?=$total?></th>
				<th><?=$rec['Complete']?></th>
				<th><?=$rec['Incomplete']?></th>
				<th><?=$rec['Not Started']?></th>
				<th><?=number_format(($rec['Complete']/$total)*100,2) ?> %</th>
			</tr>
		</tbody>
	</table>
	<h2>III. Compliance Chart</h2>
	<div id="compliance" style="min-height:400px;"></div>


<script type="text/javascript">
    $(function () {
        var chart = new CanvasJS.Chart("compliance", {
            theme: "light2",
            animationEnabled: true,
            title: {
                text: "Compliance Chart"
            },
			legend: {
				verticalAlign: "center",
				horizontalAlign: "left",
				fontSize: 20,
				fontFamily: "Helvetica"
			},
            data: [
            {
                type: "column",
				indexLabelFontFamily: "Garamond",
				indexLabelFontSize: 20,
				indexLabel: "{label} {y}",
                dataPoints: <?php echo json_encode($dataCompliance, JSON_NUMERIC_CHECK); ?>
            }
            ]
        });
        chart.render();
    });
</script>
	<h2>I. Rationale</h2>
		<p>A comprehensive physical examination provides an opportunity for the healthcare professional to obtain baseline information about the patient for future use, and to establish a relationship before problems happen. It provides an opportunity to answer questions and teach good health practices. Detecting a problem in its early stages can have good long-term results.</p>
	<h2>II. Objective</h2>
		<p>To promote health awareness among employees in the workplace.<br/>
		To prevent illnesses by catching them at an early stage & avoid the spread of infectious diseases<br/>
		To guard the health of  the workforce to minimize their absences due to preventable illnesses<br/>
		To identify the top illnesses in the workforce so as to find ways and means on how to prevent them<br/>
		To  assess the overall health status of the employees<br/>
		To foster healthy lifestyle among employees and support a healthy working Environment</p>
	<?php
		unset($res);
		unset($rec);
		$res = $db->resultArray("
		CASE
			WHEN age < 25 THEN 'Below 25'
			WHEN age BETWEEN 25 and 35 THEN '25 to 35'
			WHEN age BETWEEN 36 and 45 THEN '36 to 45'
			WHEN age > 45 THEN 'Above 45'
			WHEN age IS NULL THEN 'Not Filled In'
		END as age_range,
		COUNT(*) AS num,
		gender ",
		"data_teletech_patient",
		"group by age_range,gender");
		$grandtotal = 0;
		foreach($res as $key => $val){
			$rec['age_range'][$val['age_range']][$val['gender']] += $val['num'];
			$rec['genderonly'][$val['gender']] += $val['num'];
			$grandtotal += $val['num'];
			
			if($val['gender']=="Male"){
				$agegenderBreakdownM[]=array("y"=>$val['num'],"label"=>$val['age_range']);
			}else{
				$agegenderBreakdownF[]=array("y"=>$val['num'],"label"=>$val['age_range']);
			}
		}
		// echo "<pre>";
		// print_r($rec);
		// echo "</pre>";
		$dataAgegender = array(
		array("y" => number_format($rec['genderonly']['Male']/$grandtotal*100,2), "legendText" => "Male", "label" => "Male"),
		array("y" => number_format($rec['genderonly']['Female']/$grandtotal*100,2), "legendText" => "Female", "label" => "Female")
		);
	?>
	<h2 id="pg2">IV. Age and Gender Table</h2>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Age group</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['age_range'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<h2>IV. 1 Gender Breakdown</h2>
	<div id="agegender" style="min-height:400px;">&nbsp;</div>
	<h2>IV. 2 Age and Gender Breakdown</h2>
	<div id="agegenderBreakdown" style="min-height:400px;">&nbsp;</div>

</body>
<script type="text/javascript">
$(function () {
	var chart = new CanvasJS.Chart("agegender", {
		title: {
			text: "Gender Breakdown"
		},
		animationEnabled: true,
		legend: {
			verticalAlign: "center",
			horizontalAlign: "left",
			fontSize: 20,
			fontFamily: "Helvetica"
		},
		theme: "light2",
		data: [
		{
			type: "pie",
			indexLabelFontFamily: "Garamond",
			indexLabelFontSize: 20,
			indexLabel: "{label} {y}%",
			startAngle: -20,
			showInLegend: true,
			toolTipContent: "{legendText} {y}%",
			dataPoints: <?php echo json_encode($dataAgegender, JSON_NUMERIC_CHECK); ?>
		}
		]
	});
	chart.render();
	
	var chart2 = new CanvasJS.Chart("agegenderBreakdown", {
		exportEnabled: true,
		animationEnabled: true,
		title:{
			text: "Age and Gender Breakdown"
		},
		subtitles: [{
			text: "Click Legend to Hide or Unhide Data Series"
		}], 
		axisX: {
			title: "Age Group"
		},
		axisY: {
			title: "Male",
			titleFontColor: "#4F81BC",
			lineColor: "#4F81BC",
			labelFontColor: "#4F81BC",
			tickColor: "#4F81BC"
		},
		axisY2: {
			title: "Female",
			titleFontColor: "#C0504E",
			lineColor: "#C0504E",
			labelFontColor: "#C0504E",
			tickColor: "#C0504E"
		},
		toolTip: {
			shared: true
		},
		legend: {
			cursor: "pointer",
			itemclick: toggleDataSeries
		},
		data: [{
			type: "column",
			name: "Male",
			showInLegend: true,      
			yValueFormatString: "#,##0.#",
			dataPoints: <?php echo json_encode($agegenderBreakdownM, JSON_NUMERIC_CHECK); ?>
		},
		{
			type: "column",
			name: "Female",
			axisYType: "secondary",
			showInLegend: true,
			yValueFormatString: "#,##0.#",
			dataPoints: <?php echo json_encode($agegenderBreakdownF, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart2.render();
	function toggleDataSeries(e) {
		if (typeof (e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
			e.dataSeries.visible = false;
		} else {
			e.dataSeries.visible = true;
		}
		e.chart2.render();
	}
});
</script>
	<h2 id="pg3">V. Analysis of Results</h2>
		<p>Class A  -  Generally Physically fit.  Employees in good health , absence of any  physical defect or disease<br/>
			Class B  -  Physically under developed or with minor ailments or condition that can be cured within a     short period of time and will not adversely affect work efficiency<br/>
			Class C  -  Fit to work but with chronic conditions that would need periodic monitoring (Hypertension, Asthma, etc)<br/>
			UNFIT to Work  -  poses a health risk if the worker continues to work<br/>
			PENDING  -  Incomplete examination or conditions that would need further medical investigation </p>
	<?php
		unset($res);
		unset($rec);
		$res = $db->resultArray("
		COUNT(*) AS num,
		classification, 
		gender ",
		"data_teletech_patient",
		"group by classification,gender");
		$grandtotal = 0;
		foreach($res as $key => $val){
			$rec['classification'][$val['classification']][$val['gender']] += $val['num'];
			$grandtotal += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['classification']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['classification']);
			}
		}
		// echo "<pre>";
		// print_r($rec);
		// echo "</pre>";
	?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Classification</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['classification'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="analysis" style="min-height:400px;">&nbsp;</div>
<script type="text/javascript">
$(function () {
	
	var chart2 = new CanvasJS.Chart("analysis", {
		exportEnabled: true,
		animationEnabled: true,
		title:{
			text: "Analysis of Results"
		},
		subtitles: [{
			text: "Click Legend to Hide or Unhide Data Series"
		}], 
		axisX: {
			title: "Classification"
		},
		axisY: {
			title: "Male",
			titleFontColor: "#4F81BC",
			lineColor: "#4F81BC",
			labelFontColor: "#4F81BC",
			tickColor: "#4F81BC"
		},
		axisY2: {
			title: "Female",
			titleFontColor: "#C0504E",
			lineColor: "#C0504E",
			labelFontColor: "#C0504E",
			tickColor: "#C0504E"
		},
		toolTip: {
			shared: true
		},
		data: [{
			type: "column",
			name: "Male",
			showInLegend: true,      
			dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
		},
		{
			type: "column",
			name: "Female",
			axisYType: "secondary",
			showInLegend: true,
			dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
		}]
	});
	chart2.render();
});
</script>	
	<h2 id="pg4">VI. Examination</h2>
	<?php
	unset($res);
	unset($rec);
	$registered = $db->resultArray("
	COUNT(*) AS num,
	skuid,
	item_desc",
	"tbl_sales_items",
	"where receipt in (select receipt from data_teletech_patient where receipt is not null) group by skuid");
	
	$actual = $db->resultArray("
	COUNT(*) AS num,
	skuid",
	"lab_procedure_status ",
	"where receipt in (select receipt from data_teletech_patient where receipt is not null) group by skuid");
	// echo "<pre>";
	// print_r($actual);
	// echo "</pre>";
	foreach($actual as $key =>$val){
		$rec_actual[$val['skuid']]=$val['num'];
	}
	?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Examination</th>
				<th>Registered</th>
				<th>Actual</th>
				<th>Variance</th>
				<th>%</th>
				
			</tr>
		</thead>
		<tbody>
			<?php foreach($registered as $key => $val){ 
				if($val['item_desc']=="Pap Smear"){
					$papsmear = "<tr>
							<th style='text-align:left;'>{$val['item_desc']}</th>
							<th>{$val['num']}</th>
							<th>{$rec_actual[$val['skuid']]}</th>
							<th>".($val['num']-$rec_actual[$val['skuid']])."</th>
							<th>".number_format($rec_actual[$val['skuid']]/$val['num']*100,2)."%</th>
						</tr>";
				}
			?>
			<tr>
				<th style='text-align:left;'><?=$val['item_desc']?></th>
				<th><?=$val['num']?></th>
				<th><?=$rec_actual[$val['skuid']]?></th>
				<th><?=$val['num']-$rec_actual[$val['skuid']]?></th>
				<th><?=number_format($rec_actual[$val['skuid']]/$val['num']*100,2) ?> %</th>
			</tr>
			<?php 
				$recR[]=array("y"=>$val['num'],"label"=>$val['item_desc']);
				$recA[]=array("y"=>$rec_actual[$val['skuid']],"label"=>$val['item_desc']);
			} ?>
		</tbody>
	</table>
	<div id="examcompliance" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("examcompliance", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "Compliance Breakdown"
			},
			axisX: {
				title: "Examination"
			},
			axisY: {
				title: "Registered",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Actual",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Registered",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recR, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Actual",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recA, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>
	<h2 id="pg5">VI.1 PHYSICAL EXAMINATION</h2>
		<p>Physical examination or clinical examination is the process by which a doctor investigates  the body of a patient for signs of  diseases. It generally follows the taking of the medical history — an account of the symptoms as experienced by the patient that form part of their medical record.</p>
	<?php
		unset($res);
		unset($rec);
		$res = $db->resultArray("
		COUNT(*) AS num,
		physical_exam ",
		"data_teletech_patient",
		"group by physical_exam");
		// echo "<pre>";
		// print_r($rec);
		// echo "</pre>";
	?>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>TOP 5 FINDINGS</th>
				<th>Total</th>
				<th>%</th>
			</tr>
		</thead>
		<tbody>
			<?php 
			foreach($res as $key => $val){ 
			$rec[] = array("y"=>$val['num'],"legendText"=>$val['physical_exam'],"label"=>$val['physical_exam']);
			?>
			<tr>
				<th style='text-align:left;'><?=$val['physical_exam']?></th>
				<th><?=$val['num']?></th>
				<th><?=number_format($val['num']/$grandtotal*100,2)?> %</th>
			</tr>
			<?php 
				
			} ?>
		</tbody>
	</table>
	
	<div id="physical_exam" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">	
	$(function () {
		var chart = new CanvasJS.Chart("physical_exam", {
			title: {
				text: "PE Chart"
			},
			animationEnabled: true,
			legend: {
				verticalAlign: "center",
				horizontalAlign: "left",
				fontSize: 20,
				fontFamily: "Helvetica"
			},
			theme: "light2",
			data: [
			{
				type: "pie",
				indexLabelFontFamily: "Garamond",
				indexLabelFontSize: 20,
				indexLabel: "{label} {y}%",
				startAngle: -20,
				showInLegend: true,
				toolTipContent: "{legendText} {y}%",
				dataPoints: <?php echo json_encode($rec, JSON_NUMERIC_CHECK); ?>
			}
			]
		});
		chart.render();
	});
	</script>	
	<h2 id="pg6">VII.  BODY MASS INDEX (BMI)</h2>
	<p>Body Mass Index measures the relationship between weight and height and is one of the most accurate ways to determine if extra pounds pose health risks. A BMI score of 20-24 is associated with the lowest health risk, while a BMI in the range of 25-34.9 and who have a waist size of over 35 inches for women or 40 inches for men are considered to be at an especially increased risk for health problems. </p>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th rowspan="2">Classification</th>
				<th>BMI(kg/m<sup>2</sup>)</th>
			</tr>
			<tr><th>Principal cut-off points</th></tr>
		</thead>
		<tbody>
			<tr>
				<td>Underweight</td>
				<td><18.5</td>
			</tr>
			<tr>
				<td>Normalweight</td>
				<td>18.50-24.99</td>
			</tr>
			<tr>
				<td>Overweight</td>
				<td>25-29.9</td>
			</tr>
			<tr>
				<td>Obese</td>
				<td>30-34.9</td>
			</tr>
			<tr>
				<td>Severely Obese</td>
				<td>35-39.9</td>
			</tr>
			<tr>
				<td>Morbid Obese</td>
				<td>40+</td>
			</tr>
		</tbody>
	</table>
	<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		bmi,
		gender",
		"data_teletech_patient",
		"group by bmi,gender");
		
		foreach($res as $key => $val){
			$rec['bmi'][$val['bmi']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['bmi']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['bmi']);
			}
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>BMI</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['bmi'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="bmi" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("bmi", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "BMI BREAKDOWN"
			},
			axisX: {
				title: "BMI CHART"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>	
	
	<h2 id="pg7">VII.  BLOOD PRESSURE MEASUREMENT</h2>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Category</th>
				<th>Systolic Blood pressure</th>
				<th>Diastolic Blood pressure</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Normal</td>
				<td><120</td>
				<td><80</td>
			</tr>
			<tr>
				<td>Pre-hypertension</td>
				<td>120 - 139</td>
				<td>80 - 89</td>
			</tr>
			<tr>
				<td>Hypertension stage 1</td>
				<td>140 - 159</td>
				<td>90 - 99</td>
			</tr>
			<tr>
				<td>Hypertension stage 2</td>
				<td>>160</td>
				<td>>100</td>
			</tr>
		</tbody>
	</table>
	<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		bp,
		gender",
		"data_teletech_patient",
		"group by bp,gender");
		
		foreach($res as $key => $val){
			$rec['bp'][$val['bp']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['bp']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['bp']);
			}
		}
		
	?>
	<h2 id="pg8">HYPERTENSION CATEGROY BREAKDOWN</h2>
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Blood Pressure</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['bp'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="bp" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("bp", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "BLOOD PRESSURE CATEGORY CHART"
			},
			axisX: {
				title: "BP CHART"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>
	
	
	<h2 id="pg9">VI.2  VISUAL ACUITY EXAMINATION</h2>
	<p>Visual acuity is defined as the clarity or sharpness of vision, which is the ability of the eye to see and distinguish fine details. 
An Ophthalmologist or Optometrist measures visual acuity during routine eye exams using a wall chart with symbols or letters. The smallest line the patient can read on the chart determines the person’s visual acuity. 
A refractive error, or refraction error, is an error in the focusing of light by the eye and a frequent reason for reduced visual acuity.</p>

<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		color_perception,
		gender",
		"data_teletech_patient",
		"group by color_perception,gender");
		
		foreach($res as $key => $val){
			$rec['color_perception'][$val['color_perception']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['color_perception']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['color_perception']);
			}
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Visual Acuity</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['color_perception'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="color_perception" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("color_perception", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "Visual Acuity"
			},
			axisX: {
				title: "Visual Acuity"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>

<!----------------------------------------------------------------->
<h2 id="pg10">VI.2  COMPLETE BLOOD COUNT (CBC)</h2>
<p>Complete blood count or CBC is the best and most convenient  mechanism to detect abnormalities in a person’s blood. It begins with the quantitative evaluation of erythrocytes, leukocytes and platelets. It ends with the microscopic examination of the blood film to detect abnormalities.</p>

<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		cbc,
		gender",
		"data_teletech_patient",
		"group by cbc,gender");
		
		foreach($res as $key => $val){
			$rec['cbc'][$val['cbc']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['cbc']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['cbc']);
			}
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>CBC</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['cbc'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="cbc" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("cbc", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "Visual Acuity"
			},
			axisX: {
				title: "Visual Acuity"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>

<!---------------------------------------------->
<h2 id="pg11">VI.3  BLOOD CHEMISTRY</h2>
<p>One of the ways to monitor your health is to evaluate your blood chemistry periodically.  One of the more standard and complete tests is a Chemistry Panel, which is like a physical exam of the blood..</p>
<p>An annual analysis of your blood chemistry can be valuable in determining your overall health and wellness.  The purpose of this program is to provide you with education, so our focus is on information. Only your physician can diagnose a medical condition.Some of the blood chemistry analysis has direct relationships to nutritional deficiency, the risk of coronary heart disease, and the effects of either an active or sedentary lifestyle.  Certain changes in blood chemistry can come about by following sound nutritional and exercise guidelines.  Other changes have to do with bodily malfunctions that can not be modified by lifestyle changes.  They can only be treated by your doctor.</p>

<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		if(blood_chem is null or blood_chem ='','Pending',blood_chem) blood_chem,
		gender",
		"data_teletech_patient",
		"group by blood_chem,gender");
		
		foreach($res as $key => $val){
			$rec['blood_chem'][$val['blood_chem']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['blood_chem']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['blood_chem']);
			}
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Blood Chemistry</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['blood_chem'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="blood_chem" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("blood_chem", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "Blood Chemistry"
			},
			axisX: {
				title: "Blood Chemistry"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>
<!---------------------------------------------->
<h2 id="pg12">VI.4  URINALYSIS (UA)</h2>
<p>Urinalysis is the physical, chemical, and microscopic examination of urine. It involves a number of tests to detect and measure various compounds that pass through the urine.</p>
<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		if(urinalysis is null or urinalysis ='','Pending',urinalysis) urinalysis,
		gender",
		"data_teletech_patient",
		"group by urinalysis,gender");
		
		foreach($res as $key => $val){
			$rec['urinalysis'][$val['urinalysis']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['urinalysis']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['urinalysis']);
			}
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Urinalysis</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['urinalysis'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="urinalysis" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("urinalysis", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "UA Chart"
			},
			axisX: {
				title: "UA Chart"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>
<!---------------------------------------------->
<h2 id="pg13">VI.5 FECALYSIS</h2>
<p>Fecalysis is the analysis of human stool to determine the presence of certain pathologic conditions in the patient. Evaluates stool color, consistency, parasite identification and early  detection of gastro -intestinal problems.</p>
<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		if(fecalysis is null or fecalysis ='','Pending',fecalysis) fecalysis,
		gender",
		"data_teletech_patient",
		"group by fecalysis,gender");
		
		foreach($res as $key => $val){
			$rec['fecalysis'][$val['fecalysis']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['fecalysis']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['fecalysis']);
			}
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Fecalysis</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['fecalysis'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="fecalysis" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("fecalysis", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "Fecalysis Chart"
			},
			axisX: {
				title: "Fecalysis Chart"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>
	
<!---------------------------------------------->
<h2 id="pg14">VI.6 CHEST X-RAY (CXR)</h2>
<p>A Chest X-ray shows the heart, lungs, airway, blood vessels and lymph nodes.  It also shows the bones of the spine and chest, including the breastbone,  ribs and collarbone. This diagnostic modality can help detect some  problems with the organs and structures inside the chest. Usually,  the picture taken is from the back of the chest. If the results from a  chest x-ray are not normal or do not give enough information about  the chest problem, more specific X-rays are requested  like apicolordotic view, lateral view and spot film or other tests may be done such as CT Scan (Computed Tomography), ultrasound,  ECG or MRI. The chest X-ray is done to find lung conditions like pneumonia,  tuberculosis and other related lung problems. Abnormal Findings Results from XRAY shall be subject for further evaluation/workup.</p>
<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		if(cxr is null or cxr ='','Pending',cxr) cxr,
		gender",
		"data_teletech_patient",
		"group by cxr,gender");
		
		foreach($res as $key => $val){
			$rec['cxr'][$val['cxr']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['cxr']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['cxr']);
			}
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Chest X-Ray (CXR)</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['cxr'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="cxr" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("cxr", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "Chest X-Ray Chart"
			},
			axisX: {
				title: "Chest X-Ray Chart"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>	
<!---------------------------------------------->
<h2 id="pg15">VI.7 ELECTROCARDIOGRAM (ECG)</h2>
<p>An electrocardiogram (ECG) is a test that checks for the heart's electrical activity.
It is useful in:<br/>
Checking the early signs and symptoms of heart disease.<br/>
Find the cause of unexplained chest pain, which could be  caused by a heart attack, inflammation of the sac surrounding the heart (pericarditis), or  angina. <br/>
Finding the cause of symptoms of heart disease, such as  shortness of breath, dizziness, fainting, or rapid,  irregular heartbeats (palpitations) that is usually common in workplace.<br/>
Monitoring medications<br/>
Monitor implants in the heart, such as  pacemakers<br/>
*For male and Female 35 years old and above only.</p>
<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		if(ecg is null or ecg ='','Pending',ecg) ecg,
		gender",
		"data_teletech_patient",
		"group by ecg,gender");
		
		foreach($res as $key => $val){
			$rec['ecg'][$val['ecg']][$val['gender']] += $val['num'];
			
			if($val['gender']=="Male"){
				$recM[]=array("y"=>$val['num'],"label"=>$val['ecg']);
			}elseif($val['gender']=="Female"){
				$recF[]=array("y"=>$val['num'],"label"=>$val['ecg']);
			}
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>ECG</th>
				<th>Male</th>
				<th>% Male</th>
				<th>Female</th>
				<th>% Female</th>
				<th>Total</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($rec['ecg'] as $key => $val){ 
				$subtotal = $val['Male']+$val['Female'];
				$male += $val['Male'];
				$female += $val['Female'];
			?>
			<tr>
				<th><?=$key?></th>
				<th><?=$val['Male']?></th>
				<th><?=number_format($val['Male']/$grandtotal*100,2)?> %</th>
				<th><?=$val['Female']?></th>
				<th><?=number_format($val['Female']/$grandtotal*100,2) ?> %</th>
				<th><?=$subtotal?></th>
				<th> <?= number_format($subtotal/$grandtotal * 100,2)?> %</th>
			</tr>
			<?php } ?>
		</tbody>
		<tfoot>
			<tr>
				<th>Total</th>
				<th><?=$male?></th>
				<th><?=number_format($male/$grandtotal*100,2)?> %</th>
				<th><?=$female?></th>
				<th><?=number_format($female/$grandtotal*100,2) ?> %</th>
				<th><?=$grandtotal?></th>
				<th> <?= number_format(($male+$female)/$grandtotal * 100,2)?> %</th>
			</tr>
		</tfoot>
	</table>
	<div id="ecg" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart2 = new CanvasJS.Chart("ecg", {
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "ECG Chart"
			},
			axisX: {
				title: "ECG Chart"
			},
			axisY: {
				title: "Male",
				titleFontColor: "#4F81BC",
				lineColor: "#4F81BC",
				labelFontColor: "#4F81BC",
				tickColor: "#4F81BC"
			},
			axisY2: {
				title: "Female",
				titleFontColor: "#C0504E",
				lineColor: "#C0504E",
				labelFontColor: "#C0504E",
				tickColor: "#C0504E"
			},
			toolTip: {
				shared: true
			},
			data: [{
				type: "column",
				name: "Male",
				showInLegend: true,      
				dataPoints: <?php echo json_encode($recM, JSON_NUMERIC_CHECK); ?>
			},
			{
				type: "column",
				name: "Female",
				showInLegend: true,
				dataPoints: <?php echo json_encode($recF, JSON_NUMERIC_CHECK); ?>
			}]
		});
		chart2.render();
	});
	</script>	
<!---------------------------------------------->
<h2>VI.8 PAP SMEAR</h2>
<p>A routine examination for  women’s reproductive health. It is vital in preventing the spread of diseases that can be transmitted through sexual contact and the detection of cervical cancer, which is a disease that has claimed many lives over the years.  The test is simply where a doctors get sample tissues from a female patient's cervix and see if there are any infections or abnormal cells growing that can possibly lead to cervical cancer. This method can also give important details on the overall reproductive health of a woman.</p>
<h2>PAP SMEAR BREAKDOWN</h2>
<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
	<thead>
		<tr>
			<th>Examinations</th>
			<th>Registered</th>
			<th>Actual</th>
			<th>Variance</th>
			<th>%</th>
		</tr>
	</thead>
	<tbody><?php echo $papsmear?></tbody>
</table>
<br/><br/>	
<?php
		unset($res);
		unset($rec);
		unset($recM);
		unset($recF);
		$subtotal =0;
		$male=0;
		$female=0;
		$res = $db->resultArray("
		COUNT(*) AS num,
		pap_smear
		",
		"data_teletech_patient",
		"where gender='Female' group by pap_smear");
		
		foreach($res as $key => $val){
			$rec['pap_smear'][$val['pap_smear']] += $val['num'];
		}
		
	?>
	
	<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th>Pap Smear</th>
				<th>Count</th>
				<th>% Total</th>
			</tr>
		</thead>
		<tbody>
			<?php 
				foreach($res as $key => $val){
					echo "<tr>
						<td>{$val['pap_smear']}</td>
						<td>{$val['num']}</td>
						<td>".number_format($val['num']/$grandtotal*100,2)."%</td>
					</tr>";
					$dataPapsmear[] = array("y" => $val['num'], "legendText" => $val['pap_smear'], "label" => $val['pap_smear']);
				}
			?>
		</tbody>
	</table>
	<div id="pap_smear" style="min-height:400px;">&nbsp;</div>
	<script type="text/javascript">
	$(function () {
		
		var chart = new CanvasJS.Chart("pap_smear", {
		title: {
			text: "Pap Smear"
		},
		animationEnabled: true,
		legend: {
			verticalAlign: "center",
			horizontalAlign: "left",
			fontSize: 20,
			fontFamily: "Helvetica"
		},
		theme: "light2",
		data: [
		{
			type: "pie",
			indexLabelFontFamily: "Garamond",
			indexLabelFontSize: 20,
			indexLabel: "{label} {y}%",
			startAngle: -20,
			showInLegend: true,
			toolTipContent: "{legendText} {y}%",
			dataPoints: <?php echo json_encode($dataPapsmear, JSON_NUMERIC_CHECK); ?>
		}
		]
	});
	chart.render();
	});
	</script>			
	
<!---------------------------------------------->	
<div style="width:45%;float:left;min-height:100px;background-color:#fff;padding:10px;">
	<h2>VII. TOP TEN FINDINGS</h2>
	<p>Top ten findings are top most common diseases that were diagnosed during the annual check up based on all the examinations made. (Refer Examinations above) </p>
</div>
<div style="width:45%;float:right;min-height:100px;background-color:#fff;padding:10px;">
	<h2>Overview</h2>
	<p>Data  shows that   Weight problem and Oral Hygiene are the prevalent findings  of employees. </p>
</div>		
<div style="clear:both;height:10px;"></div>
<h2>BREAKDOWN OF TOP TEN FINDINGS</h2>	

	
	
<!---------------------------------------------->		
	
</body>
</html>