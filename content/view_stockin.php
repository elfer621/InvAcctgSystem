<head>
	<script type="text/javascript" src="../js/js/jquery-1.8.0.min.js"></script>
	<link type="text/css" href="../js/css/start/jquery-ui-1.8.23.custom.css" rel="stylesheet" />
	<script type="text/javascript" src="../js/js/jquery-ui-1.8.23.custom.min.js"></script>
	<script src="../jquery.table_navigation.js" type="text/javascript"></script>
	<script src="../js/myjs.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="../styles.css" />
	<style type="text/css">
		table {border-collapse: collapse;}
		th, td {margin: 0; padding: 0.25em 0.5em;}
		/* This "tr.selected" style is the only rule you need for yourself. It highlights the selected table row. */
		tr.selected {background-color: red; color: white;}
		/* Not necessary but makes the links in selected rows white to... */
		tr.selected a {color: white;}
	</style>
</head>
<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/pagination.class.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$p=new pagination();
$con=new dbUpdate();
//$lokasion = $_REQUEST['location']?$_REQUEST['location']:"tbl";

$_SESSION['memory']["{$_REQUEST['page']}"]['begdate'] = $_REQUEST['begdate']?$_REQUEST['begdate']:$_SESSION['memory']["{$_REQUEST['page']}"]['begdate'];
$_SESSION['memory']["{$_REQUEST['page']}"]['enddate'] = $_REQUEST['enddate']?$_REQUEST['enddate']:$_SESSION['memory']["{$_REQUEST['page']}"]['enddate'];

$begdate = $_REQUEST['begdate']?$_REQUEST['begdate']:$_SESSION['memory']["{$_REQUEST['page']}"]['begdate'];
$enddate = $_REQUEST['enddate']?$_REQUEST['enddate']:$_SESSION['memory']["{$_REQUEST['page']}"]['enddate'];
$date = "where (`date` between '$begdate' and '$enddate')";
$search = $_REQUEST['searchtxt']?"and `remarks` like '%".$_REQUEST['searchtxt']."%' or sinum like '%".$_REQUEST['searchtxt']."%'":"";
$supplier = $_REQUEST['supplier']?"and supplier_id='{$_REQUEST['supplier']}'":"";
if($_REQUEST['stocktransfer']){
	$db->openDb("main");
	$sql = "select * from (select * from tbl_stocktransfer_header $date $search $supplier) tbl where status='Transfer Stock' and `to`='{$_SESSION['connect']}' order by id desc";
}elseif($_REQUEST['po']){
	$db->openDb("main");
	$sql = "select * from tbl_po_header $date $search $supplier order by id desc";
}else{
	$db->openDb();
	$sql = "select * from tbl_{$_REQUEST['page']}_header $date $search $supplier order by id desc";
	
	if($_REQUEST['page']=="stockout"){
		$status = $con->transferStockStatus($_SESSION['connect'],$datewhere);
		//print_r($status);
	}
	
}
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
unset($_SESSION['persup']);
unset($_SESSION["{$_REQUEST['page']}_header"]);
unset($_SESSION["{$_REQUEST['page']}"]);
unset($_SESSION['poDISCOUNT1']);unset($_SESSION['poDISCOUNT2']);
$sup = $db->resultArray("*","tbl_supplier","order by supplier_name asc");
?>
<body style="padding:5px;height:100%;font-size:10px;">
	<fieldset>
		<legend>Filter</legend>
		<form method="post" id="frmfilter">
			<div style="float:left;margin-right:10px;">Beg Date</div>
			<input type="text" name="begdate" id="begdate" style="float:left;width:100px;margin-right:10px;" value="<?=$begdate?>"/>
			<div style="float:left;margin-right:10px;">End Date</div>
			<input type="text" name="enddate" id="enddate" style="float:left;width:100px;margin-right:20px;" value="<?=$enddate?>"/>
			<div style="float:left;margin-right:10px;">Supplier</div>
			<select name="supplier" style="width:150px;float:left;">
				<option value=''>Select</option>
				<?php foreach($sup as $key => $val){ ?>
					<option value="<?=$val['id']?>"><?=$val['supplier_name']?></option>
				<?php } ?>
			</select>
			<div style="float:left;margin-right:10px;">Search</div>
			<input type="text" name="searchtxt" style="float:left;width:150px;margin-right:10px;"/>
			<input type="button" value="Execute" onclick="executeFilter()" style="float:left;width:80px;height:30px;"/>
		</form>
	</fieldset>
	<fieldset>
		<legend><?=strtoupper($_REQUEST['page'])." RECORDS"?></legend>
		<div style="height:375px;overflow:auto;padding:5px;">
			<table class="navigateable" style="width:100%;">
				<thead>
					<tr>
						<th>RefID</th>
						<th>Status</th>
						<th>Date</th>
						<th>Supplier/Branch</th>
						<th>SI#</th>
						<th>Remarks</th>
						<th>Total</th>
						<?php if(!$_REQUEST['stocktransfer']){ ?>
						<th style="width:100px;">Menu</th>
						<?php } ?>
						<?php if($_REQUEST['page']=="stockout"){ ?>
						<th>Reference</th>
						<?php } ?>
					</tr>
				</thead>
				<tbody>
					<? 	while($row = mysql_fetch_assoc($qry)){ ?>
					<tr>
						<? if($row['status']=='Received Stock'){?>
							<td><a href="javascript:alert('Stock Received Already...');" class="activation"><?php echo $row['id'] ?></a></td>
						<? }elseif($_REQUEST['po']){?>
							<td><a href="javascript:viewPO('<?php echo $row['id'] ?>')" class="activation"><?php echo $row['id'] ?></a></td>
						<?}else{?>
							<?php if($status[$row['id']]['status']=="Received Stock"){ ?>
								<td><a href="javascript:alert('Stock Received Already...Disabled Editing!');" class="activation"><?php echo $row['id'] ?></a></td>
							<?php }else{ ?>
								<td><a href="javascript:transferStockin('<?= $row['id'] ?>','<?=$row['from']?>')" class="activation"><?php echo $row['id'] ?></a></td>
							<?php } ?>
						<?}?>
						<td><?= $row['status']?></td>
						<td><?= $row['date']?></td>
						<td>
							<?php
							if($_REQUEST['stocktransfer']){
								echo strtoupper($row['from']);
							}elseif($row['status']=='Received from Branch'||$row['status']=='Transfer Stock'||$row['status']=='Received from Customer'){
								$branches = $db->getWHERE("name","tbl_branch","where id='{$row['supplier_id']}'");
								echo $branches['name'];
							}elseif($row['status']=='Sold Stock'){
								$supinfo = $db->getWHERE("*","tbl_customers","where cust_id='".$row['supplier_id']."'","main");
								echo $supinfo['customer_name'];
							}else{
								$supinfo = $db->getWHERE("*","tbl_supplier","where id='".$row['supplier_id']."'","main");
								echo $supinfo['supplier_name'];
							}
							?>
						</td>
						<td><?= $row['sinum']?></td>
						<td><?= $row['remarks']?></td>
						<td><?= number_format($row['total'],2) ?></td>
						<?php
							
							//print_r($_SESSION['reports']); //$_SESSION['reports']['PO']['report_link'];
						
						?>
						
						
						<?php if(!$_REQUEST['stocktransfer']){ //menu area ?>
							<td style="text-align:center;">
								<? if($_REQUEST['page']=="po"||$_REQUEST['po']){ ?>
									<!--img onclick="viewReport('../reports/po.php?refid=')" src="../images/print.png" style="width:20px;height:20px;float:left;" />
									<img onclick="viewReport('<?=$_SESSION['reports']['PO']['report_link']."?refid=".$row['id'] ?>')" src="../images/print.png" style="width:20px;height:20px;float:left;" /-->
									<img onclick="viewReport('../reports/po<?=$_SESSION['repExtension']?>.php?refid=<?php echo $row['id'] ?>')" src="../images/print.png" style="width:20px;height:20px;float:left;" />
								<? }elseif($_REQUEST['page']=="stockout" && $row['status']=='Transfer Stock'){ ?>
									<img onclick="viewReport('../reports/transferstock.php?refid=<?php echo $row['id'] ?>')" src="../images/print.png" style="width:20px;height:20px;float:left;" />
									<img onclick="viewReport('../reports/trust_receipt.php?refid=<?= $row['id'] ?>&page=<?=$_REQUEST['page']?>')" src="../images/cashdetails.png" title="Trust Receipt" style="width:20px;height:20px;float:left;" />
									<?php if($status[$row['id']]['status'] != "Received Stock"){ ?>
									<img src="../images/del.png" style="width:20px;height:20px;float:left;" onclick="delStockout('<?=$row['id'] ?>')"/>
									<?php } ?>
								<? }elseif($row['status']=='Sold Stock'){ ?>
									<img onclick="viewReport('../reports/soldstock.php?refid=<?= $row['id'] ?>&page=<?=$_REQUEST['page']?>')" src="../images/cashdetails.png" title="Trust Receipt" style="width:20px;height:20px;float:left;" />
								<? }else{ ?>
									<? if($view==2){ ?>
										<img onclick="printPreview_withForm('<?php echo $row['id'] ?>')" src="../images/print.png" style="width:20px;height:20px;float:left;" />
									<? }else{ ?>
										<img onclick="printPreview('<?php echo $row['id'] ?>')" src="../images/print.png" style="width:20px;height:20px;float:left;" />
										<img src="../images/del.png" style="width:20px;height:20px;float:left;" onclick="delStockin('<?=$row['id'] ?>')"/>
									<? } ?>
								<? } ?>
								<!--img onclick="viewReport('../reports/vouchering.php?refid=<?=$row['glref'] ?>&center=<?=$_SESSION['connect']?>&type=GJ')" src="../images/search.png" style="width:20px;height:20px;float:left;" /-->
								<img onclick="viewReport('../reports/vouchering.php?refid=<?=$row['id'] ?>&center=<?=$_SESSION['connect']?>&type=GJ')" src="../images/search.png" style="width:20px;height:20px;float:left;" />
							</td>
						<?php } ?>
						<?php if($_REQUEST['page']=="stockout"){ 
							if($status){
								echo "<td>".($status[$row['id']]['status']=="Transfer Stock"?"SavedOnline":$status[$row['id']]['status'])."</td>";
							}else{
								echo "<td>NoConnection</td>";
							}
							
						} ?>
					</tr>
					<? } ?>
				</tbody>
			</table>
		</div>
	</fieldset>
</body>
<script>
	$('input[name*="date"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	$(document).ready(function(){
		jQuery.tableNavigation();
		window.opener.location.reload();
	});
	function executeFilter(){
		$("#frmfilter").submit();
	}
	function transferStockin(val,xfrom){
		//window.opener.setValue(val);
		var urls = getUrl();
		$.ajax({
			url: 'pos_ajax_stockin.php?execute=viewStockin&refid='+val+'&location=tbl&stocktransfer=<?=$_REQUEST['stocktransfer']?>&xfrom='+xfrom,
			data:urls,
			type:"POST",
			success:function(data){
				window.close();
				window.opener.location.reload();
				//window.opener.refresh();
			}
		});
	}
	function transferPO(val){
		//window.opener.setValue(val);
		var urls = getUrl();
		$.ajax({
			url: 'pos_ajax_stockin.php?execute=transferPO&refid='+val,
			data:urls,
			type:"POST",
			success:function(data){
				window.close();
				window.opener.location.reload();
				//window.opener.refresh();
				//alert(data);
			}
		});
	}
	function viewPO(val){
		var ponum = val;
		var urls = getUrl();
		$.ajax({
			url: 'pos_ajax.php?execute=loadpo&ponum='+ponum,
			type:"POST",
			data:urls,
			success:function(data){
				if(data=="success"){
					window.close();
					window.opener.location.reload();
					//alert(data);
				}else{
					alert(data);
				}
			}
		});
	}
	function delStockout(val){
		var r = confirm("Are you sure you want to delete this?");
		if (r == true){
			$.ajax({
				url: 'pos_ajax_stockin.php?execute=delStockout&refid='+val,
				type:"POST",
				success:function(data){
					if(data=="success"){
						window.location.reload();
					}else{
						alert(data);
					}
				}
			});
		}
	}
	function delStockin(val){
		var r = confirm("Are you sure you want to delete this?");
		if (r == true){
			$.ajax({
				url: 'pos_ajax_stockin.php?execute=delStockin&refid='+val,
				type:"POST",
				success:function(data){
					if(data=="success"){
						window.location.reload();
					}else{
						alert(data);
					}
				}
			});
		}
	}
	// function viewReport(page){
		// if (window.showModalDialog) {
			// window.showModalDialog(page,"PO","dialogWidth:650px;dialogHeight:650px");
		// } else {
			// window.open(page,"PO",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		// }
	// }
	function printPreview(refid){
		//window.close();
		if (window.showModalDialog) {
			window.showModalDialog('../reports/stockin.php?refid='+refid+"&"+$.param(getUrl()),"RR","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open('../reports/stockin.php?refid='+refid+"&"+$.param(getUrl()),"RR",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
	function printPreview_withForm(refid){
		//window.close();
		if (window.showModalDialog) {
			window.showModalDialog('../reports/stockin_withform.php?refid='+refid,"RR","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open('../reports/stockin_withform.php?refid='+refid,"RR",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
</script>

