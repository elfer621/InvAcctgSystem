<?php
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();

switch($_REQUEST['execute']){
	case'modal':
	$_SESSION['dataSync']['refid']=$_REQUEST['refid'];
	switch($_SESSION['dataSync']['refid']){
		case 1: //mobile1 to server
			$fromhost="192.168.1.101";
			$fromdb="csacci_db_mobile1";
			$tohost="192.168.1.100";
			$todb="csacci_db_mobile1";
			$testCon = $con->pdoTestConnection($fromhost,$fromdb);
		break;
		case 2: //mobile2 to server
			$fromhost="192.168.1.102";
			$fromdb="csacci_db_mobile2";
			$tohost="192.168.1.100";
			$todb="csacci_db_mobile2";
			$testCon = $con->pdoTestConnection($fromhost,$fromdb);
		break;
		case 3: //server to mobile1
			$fromhost="192.168.1.100";
			$fromdb="csacci_db_mobile1";
			$tohost="192.168.1.101";
			$todb="csacci_db_mobile1";
			$testCon = $con->pdoTestConnection($tohost,$todb);
		break;
		case 4: //server to mobile2
			$fromhost="192.168.1.100";
			$fromdb="csacci_db_mobile2";
			$tohost="192.168.1.102";
			$todb="csacci_db_mobile2";
			$testCon = $con->pdoTestConnection($tohost,$todb);
		break;
	}
	if($testCon){
		$complist = $con->pdoStyle($fromhost,$fromdb,"select distinct company_name from data_teletech_patient where year=year(now())");
		$dataref = $con->pdoStyle($fromhost,$fromdb,"select distinct data_reference from data_teletech_patient where year=year(now())");
		?>
		<div style="float:left;margin-right:30px;width:150px;">Company Name:</div>
		<select name="company_name" id="company_name" style="float:left;width:200px;">
			<option value="">Select</option>
			<?php foreach($complist as $k => $v){ ?>
			<option <?= $_SESSION['records_filter']['company_name']==$v['company_name']?"selected":""?> value="<?=$v['company_name']?>"><?=$v['company_name']?></option>
			<?php } ?>
		</select>
		<div style="clear:both;height:5px;"></div>
		<div id="cashier_area">
			<div style="float:left;margin-right:30px;width:150px;">Data Reference:</div>
			<select name="data_reference" id="data_reference" style="float:left;width:200px;">
				<option value="">Select</option>
				<?php foreach($dataref as $k => $v){ ?>
				<option <?= $_SESSION['records_filter']['data_reference']==$v['data_reference']?"selected":""?> value="<?=$v['data_reference']?>"><?=$v['data_reference']?></option>
				<?php } ?>
			</select>
		</div>
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="Execute" onclick="Exec()" style="height:30px;width:150px;"/>
	<?php } ?>
	<script>
	function Exec(ref){
		var data_reference = "&data_reference="+$("#data_reference").val();
		var company_name = "&company_name="+$("#company_name").val();
		if (window.showModalDialog) {
			window.showModalDialog('content/dataSync.php?execute=patientTrans&data_reference'+company_name+data_reference,"PE","dialogWidth:1050px;dialogHeight:650px");
		} else {
			window.open('content/dataSync.php?execute=patientTrans'+company_name+data_reference,"PE",'height=650,width=1050,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
		}
	}
	$("select[name='data[company_name]']").change(function(){
		var val = $(this).val();
		$.ajax({
			url: './content/lab/teletech_input.php?execute=toSession&company_name='+val,
			type:"POST",
			success:function(data){
				alert("Set as default to Company Name...");
			}
		});
	});
	$("select[name='data[data_reference]']").change(function(){
		var val = $(this).val();
		$.ajax({
			url: './content/lab/teletech_input.php?execute=toSession&data_reference='+val,
			type:"POST",
			success:function(data){
				alert("Set as default to Data Reference...");
			}
		});
	});	
	</script>
	<?
	break;
	case'patientTrans':
		switch($_SESSION['dataSync']['refid']){
			case 1: //mobile1 to server
				$fromhost="192.168.1.101";
				$fromdb="csacci_db_mobile1";
				$tohost="192.168.1.100";
				$todb="csacci_db_mobile1";
				$testCon = $con->pdoTestConnection($fromhost,$fromdb);
			break;
			case 2: //mobile2 to server
				$fromhost="192.168.1.102";
				$fromdb="csacci_db_mobile2";
				$tohost="192.168.1.100";
				$todb="csacci_db_mobile2";
				$testCon = $con->pdoTestConnection($fromhost,$fromdb);
			break;
			case 3: //server to mobile1
				$fromhost="192.168.1.100";
				$fromdb="csacci_db_mobile1";
				$tohost="192.168.1.101";
				$todb="csacci_db_mobile1";
				$testCon = $con->pdoTestConnection($tohost,$todb);
			break;
			case 4: //server to mobile2
				$fromhost="192.168.1.100";
				$fromdb="csacci_db_mobile2";
				$tohost="192.168.1.102";
				$todb="csacci_db_mobile2";
				$testCon = $con->pdoTestConnection($tohost,$todb);
			break;
		}
		if($testCon){
			if($_REQUEST['data_reference']){
				$_SESSION['records_filter']['data_reference']=$_REQUEST['data_reference'];
				$_SESSION['records_filter']['company_name']=$_REQUEST['company_name'];
				$sql1 = "select * from data_teletech_patient where data_reference='{$_SESSION['records_filter']['data_reference']}' and company_name='{$_SESSION['records_filter']['company_name']}'";
				$qry1 = $con->pdoStyle($fromhost,$fromdb,$sql1);
				$count=count($qry1);
				$incre=1;
				foreach($qry1 as $key => $val){
					$exec = "REPLACE INTO data_teletech_patient (`".implode("`,`",array_keys($val))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $val))."');\n";
					$q = $con->pdoExec($tohost,$todb,$exec);
					if($q){
						echo "1-($incre/$count) Success<br/>";
					}else{
						echo "1-($incre/$count) Error [$exec]<br/>";
					}
					$incre++;
				}

				$sql2 = "select * from lab_procedure_status where receipt in (
				select receipt from data_teletech_patient where data_reference='{$_SESSION['records_filter']['data_reference']}' and company_name='{$_SESSION['records_filter']['company_name']}')";
				$qry2 = $con->pdoStyle($fromhost,$fromdb,$sql2);
				$count=count($qry2);
				$incre=1;
				foreach($qry2 as $key => $val){
					$exec = "REPLACE INTO lab_procedure_status (`".implode("`,`",array_keys($val))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $val))."')";
					$q = $con->pdoExec($tohost,$todb,$exec);
					if($q){
						echo "2-($incre/$count) Success<br/>";
					}else{
						echo "2-($incre/$count) Error<br/>";
					}
					$incre++;
				}
				
				
				$sqlres = "select * from lab_results where receipt in (
				select receipt from data_teletech_patient where data_reference='{$_SESSION['records_filter']['data_reference']}' and company_name='{$_SESSION['records_filter']['company_name']}')";
				$qryres = $con->pdoStyle($fromhost,$fromdb,$sqlres);
				$count=count($qryres);
				$incre=1;
				foreach($qryres as $key => $val){
					$exec = "REPLACE INTO lab_results (`".implode("`,`",array_keys($val))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $val))."')";
					$q = $con->pdoExec($tohost,$todb,$exec);
					if($q){
						echo "2-($incre/$count) Success<br/>";
					}else{
						echo "2-($incre/$count) Error<br/>";
					}
					$incre++;
				}
				
				

				$sql3 = "select * from tbl_sales_items where receipt in (
				select receipt from data_teletech_patient where data_reference='{$_SESSION['records_filter']['data_reference']}' and company_name='{$_SESSION['records_filter']['company_name']}')";
				$qry3 = $con->pdoStyle($fromhost,$fromdb,$sql3);
				$count=count($qry3);
				$incre=1;
				foreach($qry3 as $key => $val){
					$exec = "REPLACE INTO tbl_sales_items (`".implode("`,`",array_keys($val))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $val))."')";
					$q = $con->pdoExec($tohost,$todb,$exec);
					if($q){
						echo "3-($incre/$count) Success<br/>";
					}else{
						echo "3-($incre/$count) Error<br/>";
					}
					$incre++;
				}

				$sql4 = "select * from tbl_sales_receipt_1 where receipt_id in (
				select receipt from data_teletech_patient where data_reference='{$_SESSION['records_filter']['data_reference']}' and company_name='{$_SESSION['records_filter']['company_name']}')";
				$qry4 = $con->pdoStyle($fromhost,$fromdb,$sql4);
				$count=count($qry4);
				$incre=1;
				foreach($qry4 as $key => $val){
					$exec = "REPLACE INTO tbl_sales_receipt_1 (`".implode("`,`",array_keys($val))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $val))."')";
					$q = $con->pdoExec($tohost,$todb,$exec);
					if($q){
						echo "4-($incre/$count) Success<br/>";
					}else{
						echo "4-($incre/$count) Error<br/>";
					}
					$incre++;
				}
				
				$sql5 = "select * from tbl_product_name";
				$qry5 = $con->pdoStyle($fromhost,$fromdb,$sql5);
				$count=count($qry5);
				$incre=1;
				foreach($qry5 as $key => $val){
					$exec = "REPLACE INTO tbl_product_name (`".implode("`,`",array_keys($val))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $val))."')";
					$q = $con->pdoExec($tohost,$todb,$exec);
					if($q){
						echo "5-($incre/$count) Success<br/>";
					}else{
						echo "5-($incre/$count) Error<br/>";
					}
					$incre++;
				}
				
				$sql6 = "select * from tbl_barcodes";
				$qry6 = $con->pdoStyle($fromhost,$fromdb,$sql6);
				$count=count($qry6);
				$incre=1;
				foreach($qry6 as $key => $val){
					$exec = "REPLACE INTO tbl_barcodes (`".implode("`,`",array_keys($val))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $val))."')";
					$q = $con->pdoExec($tohost,$todb,$exec);
					if($q){
						echo "6-($incre/$count) Success<br/>";
					}else{
						echo "6-($incre/$count) Error<br/>";
					}
					$incre++;
				}
				
				$sql7 = "select * from tbl_packages";
				$qry7 = $con->pdoStyle($fromhost,$fromdb,$sql7);
				$count=count($qry7);
				$incre=1;
				foreach($qry7 as $key => $val){
					$exec = "REPLACE INTO tbl_packages (`".implode("`,`",array_keys($val))."`) values ('".implode("', '", array_map('mysql_real_escape_string', $val))."')";
					$q = $con->pdoExec($tohost,$todb,$exec);
					if($q){
						echo "7-($incre/$count) Success<br/>";
					}else{
						echo "7-($incre/$count) Error<br/>";
					}
					$incre++;
				}
			}else{
				echo "No records selected...";
			}
		}
	break;
}



?>