<!DOCTYPE html>
<html lang="en">
<head>
  <title><?=$_SESSION['settings']['system_name']?></title>
  <meta name="description" content=""/>
  <meta name="keywords" content=""/>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<link href="css/style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="css/tblstyle.css" type="text/css" />
	<link rel="stylesheet" href="css/modal_manual.css" type="text/css" />
	<!--script type="text/javascript" src="js/js/jquery-1.8.0.min.js"></script-->
	<script type="text/javascript" src="jqgrid/js/jquery-1.7.2.min.js"></script>
	<link type="text/css" href="js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="js/js/jquery-ui-1.8.23.custom.min.js"></script>
	
	<!--link type="text/css" href="js/jquery-ui-1.12.1.custom/jquery-ui.min.css" rel="stylesheet" />
	<script type="text/javascript" src="js/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script-->
	
	
	
	<link rel="stylesheet" href="jqgrid/css/ui.jqgrid.css" type="text/css" />
	<script src="jqgrid/js/i18n/grid.locale-en.js" type="text/javascript"></script>
	<script src="jqgrid/js/jquery.jqGrid.min.js" type="text/javascript"></script>
	<script src="jqgrid/js/jquery.jqGrid.src.js" type="text/javascript"></script>
	
	<script type="text/javascript" src="js/myjs.js"></script>
	<script type="text/javascript" src="js/autoSuggest.js"></script>
	<script type="text/javascript" src="js/jqGrid.js"></script>
	
	<script src="jquery.table_navigation_main.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="styles.css" />
	<style type="text/css">
		table {border-collapse: collapse;}
		th, td {margin: 0; padding: 0.25em 0.5em;}
		/* This "tr.selected" style is the only rule you need for yourself. It highlights the selected table row. */
		tr.selected {background-color: #ffcb2e; color: white;}
		tr.selectedmain {background-color: green; color: white;}
		tr.selecteduom {background-color: green; color: white;}
		tr.selectedjournal {background-color: #ccc; color: white;}
		/* Not necessary but makes the links in selected rows white to... */
		tr.selected a {color: white;}
		tr.selecteduom a {color: white;}
	</style>
	
	<script src="jquery.table_navigation.js" type="text/javascript"></script>
	<script src="jquery.table_navigation_uom.js" type="text/javascript"></script>
	<script src="jquery.table_navigation_journal.js" type="text/javascript"></script>
	
	<link href="./js/chosen/chosen.css" rel="stylesheet">
	<script src="./js/chosen/chosen.jquery.js" type="text/javascript"></script>
	<script src="./js/chosen/docsupport/prism.js" type="text/javascript" charset="utf-8"></script>
	
	<script src="./js/tinymce/js/tinymce/tinymce.min.js"></script>
	<script src="./js/maskedinput.js"></script>
	
</head>
<body>
<div style="z-index:1002"><?//=$_SESSION['error']?></div>
<?php if($_REQUEST['page']!="sales"){ ?>
<?php 
switch($_SESSION['settings']['system_name']){
	case"RTK":
		$logo = '<img src="./images/rtklogoNew.png" style="float:left;height:45px;width:140px;margin-right:10px;padding-top:5px;"/>';
		$bgcolor="#fff";
		$fontcolor="#262780";
	break;
	case"CSACCI":
		$logo =  '<img src="./images/csacci_logo.jpg" style="float:left;height:45px;width:55px;margin-right:10px;padding-top:5px;"/>';
	break;
	case"TKC":
		$logo =  '<img src="./images/tkc.png" style="float:left;height:45px;width:55px;margin-right:10px;padding-top:5px;"/>';
	break;
	case"Lizgan System":
		$logo =  '<img src="./images/lizganlogo.jpg" style="float:left;height:50px;width:130px;margin-right:10px;padding-top:5px;"/>';
	break;
}
?>
<div class="main-header" style="background-color:<?=$bgcolor?$bgcolor:"#fff"?> !important;color:<?=$fontcolor?$fontcolor:"#000"?>;">
	<div class="main-header-left">
		<?=$logo?>
		<div style="font-size:20px;color:<?=$fontcolor?$fontcolor:"#000"?>;margin-top:5px;float:left;"><?=$db->stockin_header."</br><span style='font-size:12px;'>".($_SESSION['connect']?strtoupper($_SESSION['connect']):"Main Branch")."</span>";?></div>
	</div>
	<div class="icon-container" onclick="myFunction(this)">
	  <div class="bar1" style="background-color:<?=$fontcolor?$fontcolor:"#000"?>"></div>
	  <div class="bar2" style="background-color:<?=$fontcolor?$fontcolor:"#000"?>"></div>
	  <div class="bar3" style="background-color:<?=$fontcolor?$fontcolor:"#000"?>"></div>
	</div>
	<div style="float:left;">
		<div style="width:150px;float:left;">Branch</div>
		<select id="branchname" style="width:100%;">
			<option value="main">Cebu</option>
			<?php
			$branch = $db->resultArray("*","settings_connections","where con_name !='main'");
			foreach($branch as $key => $val){
				echo "<option ".($_SESSION['connect']==$val['con_name']?"selected":"")." value='{$val['con_name']}'>".ucfirst($val['con_name'])."</option>";
			}
			?>
		</select>
	</div>
	<div class="main-header-right">
		<div style="font-size:12px;text-align:center;color:<?=$fontcolor?$fontcolor:"#000"?>;"><?="Connected:".$db->constatus;?></div>
		<div style="font-size:12px;text-align:center;"><?="IP:".$_SERVER['REMOTE_ADDR'];?></div>
		<div style="font-size:12px;text-align:center;"><?="TERMINAL:".$_SESSION['counter_num'];?></div>
	</div>
</div>
<?php } ?>

<div class="main-out" style="margin:0px 0 10px 0;">
	<div class="main <?=$_REQUEST['page']=="sales"||$_REQUEST['page']=="order"||$_REQUEST['page']=="estore"?"":"full"?>">
		<div class="page <?=$_REQUEST['page']!="sales"?"full":""?>">
			<?php
				require_once "$content";
			?>
		</div>
	</div>
	<div id="prodlist" style="font-size:12px;"></div>
	<div style="clear:both;height:5px;"></div>
	<div id="xlogin"></div>
	<div id="startreading"></div>
	<div id="dialogbox2"></div>
	<div id="dialogbox3"></div>
	<div id="popuploading"></div>
	<div id="dialogTenderpayment"></div>
	<div id="msg"></div>
	<div style="clear:both;"></div>
</div>
<script>
$("#branchname").change(function(){
	$.ajax({
		url: './content/pos_ajax.php?execute=changebranch&branchname='+$(this).val(),
		type:"POST",
		success:function(data){
			if(data=="success"){
				window.location=document.URL;
			}
		}
	});
});
</script>
</body>
</html>