<?php
// if(isset($_REQUEST['connect'])){
	// $db->dbname="lizgan_{$_REQUEST['connect']}";
// }
switch($_REQUEST['page']){
	case'prod_maintenance_whole':
	case'prod_maintenance':
		switch($_SESSION['settings']['system_name']){
			case'TKC':
			$dashboard="./content/product_maintenance_tkc.php";
			break;
			case'RTK':
			$dashboard="./content/product_maintenance_rtk.php";
			break;
			default:
			$dashboard="./content/product_maintenance.php";
			break;
		}
	break;
	case'saleshist':
		$dashboard="./content/saleslistserver_reading.php";
	break;
	case'manager_user':
		$dashboard="./content/manage_user.php";
	break;
	case'reports':
		if($_SESSION['connect']=="warehouse"){
			$dashboard="./content/report_list_warehouse.php";
		}else{
			$dashboard="./content/report_list.php";
		}
	break;
	case'stocktransfer':
		$dashboard="./content/transfer_stock.php";
	break;
	case'import_files':
		$dashboard="./content/import_files.php";
	break;
	case'view_branches':
		$dashboard="./content/view_branches.php";
	break;
	case'journalentry':
		$dashboard="./content/journal_entry_summary.php";
	break;
	case'chart_of_account':
		$dashboard="./content/chart_of_account.php";
	break;
	case'dbmaintenance':
		$dashboard="./content/dbmaintenance.php";
	break;
	case'persupplier':
		$dashboard="./content/supplier_view.php";
	break;
	case'allor':
		$dashboard="./content/allOR.php";
	break;
	
	case'soa_create':
		$dashboard="./content/soa_create.php";
	break;
	case'package_create':
		$dashboard="./content/package_create.php";
	break;
	case'gl':
	case'vouchering':
		switch($_SESSION['settings']['system_name']){
			case'TKC':
				$dashboard="./content/vouchering_tkc.php";
			break;
			default:
				$dashboard="./content/vouchering.php";
			break;
		}
	break;
	case'stockin':
		$dashboard="./content/stockin.php";
	break;
	case'stockout':
		$dashboard="./content/stockin.php";
	break;
	case'po':
		$dashboard="./content/stockin.php";
	break;
	case'sales':
		$dashboard ="pos_content.php";
	break;
	case'prod_maintenance_whole':
		$dashboard="./content/product_maintenance.php";
	break;
	case'touchscreen_prodlist':
		$dashboard ="./touchscreen/product_list.php";
	break;
	case'jqgrid':
		$dashboard ="./content/jqgrid_content.php";
	break;
	case'jqgrid_dynamic':
		$dashboard ="./content/jqgrid_dynamic.php";
	break;
	case'floor_mapping':
		$dashboard ="./content/floor_mapping.php";
	break;
	case'tenant_list':
		$dashboard ="./content/tenant_list.php";
	break;
	case'soa_multiple':
		$dashboard ="./content/soa_multiple.php";
	break;
	case'floorunit_reservation':
		$dashboard ="./content/floorunit_reservation.php";
	break;
	case'payment_received':
		$dashboard ="./content/payment_received.php";
	break;
	case'hotelreservation':
		$dashboard='./hotelreservation/index.php';
	break;
	case'roomreservation':
		$dashboard='./roomreservation/index.php';
	break;
	case'doctorappointment':
		$dashboard='./DoctorAppointment/index.php';
	break;
	case'payroll':
		$dashboard ="./content/rber/payroll.php";
	break;
	case'unitpage':
		$dashboard='./content/condo/unitpage.php';
	break;
	case'dynamictbl':
		$dashboard='./content/dynamictbl_add_edit.php';
	break;
	case'pdcinput':
		$dashboard='./content/ccrc/pdc.php';
	break;
	case'otherchargesdaily':
		$dashboard='./content/ccrc/othercharges.php';
	break;
	case'contract':
		$dashboard='./content/ccrc/contract.php';
	break;
	case'quotation':
		$dashboard ="./content/rber/quotation.php";
	break;
	case'dynamic_invoicing':
		switch($_SESSION['settings']['system_name']){
			case"TKC":
				$dashboard ="./content/tkc/dynamic_invoicing.php";
			break;
			case"RTK":
				$dashboard ="./content/rtk/dynamic_invoicing.php";
			break;
			default:
				$dashboard ="./content/rber/dynamic_invoicing.php";
			break;
		}
		
	break;
	
	case'dynamic_lab':
		$dashboard ="./content/lab/dynamic_lab.php";
	break;
	case'queue':
		$dashboard ="./content/lab/queue.php";
	break;
	case'procedure_status':
		$dashboard ="./content/lab/procedure_status.php";
	break;
	case'demographic_input':
		$dashboard ="./content/lab/teletech_input.php";
	break;
	case'physical_exam':
		$dashboard ="./content/lab/physical_exam.php";
	break;
	default:
		// if($db->constatus=="lizgan_main"){
			// $dashboard="./content/view_vouchering_main.php";
		// }else{
			// $dashboard="./content/view_vouchering.php";
		// }
		//$dashboard='./content/condo/dashboard.php';
	break;
}
?>
<div class="top <?=$_REQUEST['page']!="sales"?"full":""?>">
	<div class="content <?=$_REQUEST['page']!="sales"?"full":""?>" style="min-height:450px;">
		<div class="side-bar" style="margin-right:20px;float:left;<?=$_REQUEST['page']=="prod_maintenance_whole"||$_REQUEST['page']=="physical_exam"||$_REQUEST['page']=="procedure_status"||$_REQUEST['page']=="dynamic_lab"?"display:none;":'';?>">
			<div style="clear:both;height:10px;"></div>
			<fieldset style="padding:10px;">
				<legend>&nbsp; DASHBOARD &nbsp;</legend>
					<?php 
						if(isset($_SESSION['connect'])){
							switch($_SESSION['settings']['system_name']){
								case"CSACCI":
									include_once"dashboard_branch_lab.php";
								break;
								default:
									include_once"dashboard_branch.php";
								break;
							}
						}else{
							switch($_SESSION['settings']['system_name']){
								case"CSACCI":
									include_once"dashboard_lab.php";
								break;
								case"Lizgan System":
									include_once"dashboard_main_lizgan.php";
								break;
								case"Rber System":
									include_once"dashboard_main_rber.php";
								break;
								case"CCRC System":
									include_once"dashboard_main_ccrc.php";
								break;
								case"TKC":
									include_once"dashboard_main_tkc.php";
									$_SESSION['repExtension']="_tkc";
								break;
								case"RTK":
									include_once"dashboard_main_rtk.php";
									$_SESSION['repExtension']="_rtk";
								break;
								case"Hotel":
									include_once"dashboard_main_hotel.php";
									$_SESSION['repExtension']="_hotel";
								break;
								default:
									include_once"dashboard_main.php";
								break;
							}
						}
					?>
			</fieldset> 
			<br/>
			<fieldset style="padding:10px;">
				<?php if($mode=="main"){ ?>
					<input id="bt7" class="buthov" type="button" value="Back to Main" onclick="window.location='?connect=main';" style="float:right;height:40px;width:100%;float:left;"/>
					<br/>
					<?php if($_SESSION['restrictionid']==1){ ?>
						<input id="bt12" class="buthov" type="button" value="Manage/Create User" onclick="window.location='?page=manager_user';" style="height:40px;width:100%;float:left;"/>
						<br/>
					<?php } ?>
				<?php } ?>
				<input id="bt10" class="buthov" type="button" value="Password Change" onclick="changePass()" style="height:40px;width:100%;float:left;"/>
				<br/>
				<input id="bt6" class="buthov" type="button" value="Sign Out" onclick="signOut()" style="float:right;height:40px;width:100%;float:left;"/>
				<br/>
			</fieldset>
		</div>
		<div id="ajax_content" style="float:left;width:<?=$_REQUEST['page']=="prod_maintenance_whole"||$_REQUEST['page']=="physical_exam"||$_REQUEST['page']=="procedure_status"||$_REQUEST['page']=="dynamic_lab"?"100%":'83%';?>;margin:0 auto;">
			<div>
			<?php 
				if($_REQUEST['iframe']){
					echo '<iframe src="'.$dashboard.'" style="width:97%;height:580px;"></iframe>';
				}else{
					include_once"$dashboard"; 
				}
			
			
			?>
			
			</div>
			
		</div>
	</div>
	<div style="clear:both;height:10px;"></div>
</div>
<div id="dialogbox"></div>
<script>
function myFunction(x) {
    x.classList.toggle("change");
	var sidebar = $(".side-bar").attr('class');
	console.log(sidebar);
	if(sidebar=="side-bar"){
		$(".side-bar").addClass("active");
	}else{
		$(".side-bar").removeClass("active");
	}
}
function show_custlist(){
	//clickDialog('dialogbox',500,400,'customerlist','Customer List');
	$.ajax({
		url: './content/pos_ajax.php?execute=customerlist',
		type:"POST",
		success:function(data){
			var rep_btn = "<fieldset style='margin-top:30px;'><legend>Reports</legend>"+
			'<input id="bt1" class="buthov" type="button" value="Payment" style="float:left;height:40px;width:180px;" onclick="custTrans_Rep()"/>'+
			'<input id="bt2" class="buthov" type="button" value="Receipt Undelivered" style="float:left;height:40px;width:180px;" onclick="custTrans_Rep(\'undelivered_receipt\')"/>'+
			"</fieldset>";
			$("#ajax_content").html(data+rep_btn);
			$("#custokbt").css('display','none');
		}
	});
}
function custTrans_Rep(type){
	if (window.showModalDialog) {
		window.showModalDialog('./reports/cust_trans.php?rep_type='+type,"Customer Trans","dialogWidth:700px;dialogHeight:500px");
	} else {
		window.open('./reports/cust_trans.php?rep_type='+type,"Customer Trans",'height=500,width=700,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
	}
}
function viewCommReport(){
	var win=window.open('reports/sales_comm.php','_blank');
	win.focus();
}
</script>