<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />


<!--[if lte IE 8]><script language="javascript" type="text/javascript" src="./js/jqplot/excanvas.js"></script><![endif]-->
<!-- jqPlot components start here -->
<link class="include" rel="stylesheet" type="text/css" href="../js/jqplot/jquery.jqplot.min.css" />
<link rel="stylesheet" type="text/css" href="../js/jqplot/examples/examples.min.css" />
<link type="text/css" rel="stylesheet" href="../js/jqplot/examples/syntaxhighlighter/styles/shCoreDefault.min.css" />
<link type="text/css" rel="stylesheet" href="../js/jqplot/examples/syntaxhighlighter/styles/shThemejqPlot.min.css" />

<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="./js/jqplot/excanvas.js"></script><![endif]-->
<!-- Don't touch this! -->
<script class="include" type="text/javascript" src="../js/jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="../js/jqplot/examples/syntaxhighlighter/scripts/shCore.min.js"></script>
<script type="text/javascript" src="../js/jqplot/examples/syntaxhighlighter/scripts/shBrushJScript.min.js"></script>
<script type="text/javascript" src="../js/jqplot/examples/syntaxhighlighter/scripts/shBrushXml.min.js"></script>
<!-- End Don't touch this! -->

 <!-- to render rotated axis ticks, include both the canvasText and canvasAxisTick renderers -->
<script language="javascript" type="text/javascript" src="../js/jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/jqplot/plugins/jqplot.dateAxisRenderer.min.js"></script>
<script language="javascript" type="text/javascript" src="../js/jqplot/plugins/jqplot.cursor.min.js"></script>
<!-- End additional plugins -->
<script type="text/javascript" src="../js/jqplot/examples/example.min.js"></script>
<!-- jqPlot components end here -->


</head>
<?php
session_start();
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
$begdate = $_REQUEST['beg_date']?$_REQUEST['beg_date']:date('Y-m-01');
$enddate = $_REQUEST['end_date']?$_REQUEST['end_date']:date('Y-m-d');

?>
<body style="margin:0 auto 0;width:900px;font-size:11px;">
	<h2>Graphical Presentation</h2>
	<form name="frm_cust" method="post">
		<div style="float:left;margin-right:10px;">Records Type: </div>
		<select id="data" name="data" style="float:left;margin-right:20px;width:150px;">
			<option <?=$_REQUEST['data']=="Sales"?"selected":""?> value="Sales">Sales</option>
		</select>
		<div style="float:left;margin-right:30px;">Beg Date</div>
		<input style="float:left;margin-right:50px;" type="text" id="beg_date" name="beg_date" value="<?=$begdate?>"/>
		<div style="float:left;margin-right:30px;">End Date</div>
		<input style="float:left;" type="text" id="end_date" name="end_date" value="<?=$enddate?>"/>
		<input type="submit" value="Search" name="search_date" style="margin-left:20px;"/>
	</form>
	<div style="clear:both;height:10px;"></div>
	<div class="example-plot" id="chart" style="width: 900px; height: 500px;float:left;margin-right:30px;"></div>
</body>
<script>
var graph_rec = new Array();

$(document).ready(function(){
	$('#beg_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$('#end_date').datepicker({
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	// Enable plugins like highlighter and cursor by default.
	// Otherwise, must specify show: true option for those plugins.
	$.jqplot.config.enablePlugins = true;
	$(function(){
		var type = 'salesMonthly';
		var begdate=$("#beg_date").val();
		var enddate=$("#end_date").val();
		$.ajax({
			url: "./ajax_graph.php?type=graph&data="+type+"&startdate="+begdate+"&enddate="+enddate,
			type:"POST",
			async:true,
			dataType: "json",
			success:function(data){
				var plot1 = $.jqplot('chart', [data], {
					title:type,
					axes:{
						xaxis:{
							renderer:$.jqplot.DateAxisRenderer, 
							rendererOptions:{
								tickRenderer:$.jqplot.CanvasAxisTickRenderer
							},
							tickOptions:{ 
								fontSize:'10pt', 
								fontFamily:'Tahoma', 
								angle:-40						}
						},
						yaxis:{
							rendererOptions:{
								tickRenderer:$.jqplot.CanvasAxisTickRenderer},
								tickOptions:{
									fontSize:'10pt', 
									fontFamily:'Tahoma', 
									angle:0,
									formatter: function(format, value){
										switch(type){
											case "Sales":case "salesMonthly":
												return value.toFixed(2);
											break;
											default:
												return sec_to_time(value);
											break;
										}
									}
								}
						}
					},
					series:[{ lineWidth:4, markerOptions:{ style:'square' } }],
					cursor:{
						zoom:true,
						looseZoom: true
					}
				});
			}
		});
	});
});
function sec_to_time(n) {
	var dtm = new Date();
    dtm.setTime(n);
	
	var hours = Math.floor(n / 3600);
	var minutes = Math.floor(n % 3600 / 60);
	var seconds = n % 60;
	return $.jqplot.sprintf("%d:%02d:%02d", hours, minutes, seconds);
}
function MillisecondsToDuration(n) {
    var dtm = new Date();
    dtm.setTime(n);

    var hours = Math.floor(n / 3600000);
    var minutes = dtm.getMinutes();
    var seconds = dtm.getSeconds();

    return $.jqplot.sprintf('%02d:%02d:%02d', hours, minutes, seconds);        
}
</script>
</html>