<?php
$sql = "select id,date,type,status,payee,remarks,total,center from tbl_vouchering where (approvedby is null or status='ForApproval') and `status`<>'Rejected'
			union 
			select id,date,type,status,payee,remarks,total,center from tbl_vouchering_uclm where (approvedby is null or status='ForApproval') and `status`<>'Rejected'
			union 
			select id,date,type,status,payee,remarks,total,center from tbl_vouchering_ucmain where (approvedby is null or status='ForApproval') and `status`<>'Rejected'
			union 
			select id,date,type,status,payee,remarks,total,center from tbl_vouchering_ucmambaling where (approvedby is null or status='ForApproval') and `status`<>'Rejected'
			union 
			select id,date,type,status,payee,remarks,total,center from tbl_vouchering_warehouse where (approvedby is null or status='ForApproval') and `status`<>'Rejected'
			";
$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<div style="height:100%;overflow:auto;background-color:white;">
	<div id="tabs" style="height:98%;">
		<ul>
			<li><a href="#tabs-1" onclick="loadTrans('ForApproval')">For Approval</a></li>
			<li><a href="#tabs-2" onclick="loadTrans('Approved')">Approved</a></li>
			<li><a href="#tabs-3" onclick="loadTrans('Rejected')">Rejected</a></li>
		</ul>
			<div id="tabs-1" style="min-height:455px;">
				<table class="navigateable" id="mytbl" style="width:100%;">
					<thead>
						<tr>
							<th>RefID</th>
							<th>Status</th>
							<th>Type</th>
							<th>Date</th>
							<th>Remarks</th>
							<th>Total</th>
							<th>Menu</th>
						</tr>
					</thead>
					<tbody>
						<? 	while($row = mysql_fetch_assoc($qry)){ ?>
						<tr>
							<td><a href="javascript:transferStockin('<?php echo $row['id'] ?>','<?php echo $row['type'] ?>','<?=$row['center']?>')" class="activation"><?php echo $row['id'] ?></a></td>
							<td><?= $row['status']?></td>
							<td><?= $row['type']?></td>
							<td><?= $row['date']?></td>
							<td><?= $row['remarks']?></td>
							<td><?= number_format($row['total'],2) ?></td>
							<td>
								<img src="./images/print.png" style="width:20px;height:20px;float:left;" onclick="viewReport('./reports/vouchering.php?refid=<?php echo $row['id'] ?>&center=<?=$row['center']?>')"/>
							</td>
						</tr>
						<? } ?>
					</tbody>
				</table>
			</div>
			<div id="tabs-2" style="min-height:455px;">
				
			</div>
			<div id="tabs-3" style="min-height:455px;">
			
			</div>
	</div>
</div>
<!--fieldset>
	<legend>VOUCHER RECORDS</legend>
	<div style="height:375px;overflow:auto;padding:5px;">
		
	</div>
</fieldset-->
<script>
	$(function(){
		$('#tabs').tabs();
	});
	$(document).ready(function(){
		jQuery.tableNavigation();
		//window.opener.location.reload();
	});
	function transferStockin(val,xtype,center){
		var urls = getUrl();
		$.ajax({
			url: './content/vouchering_ajax.php?execute=viewVoucher&refid='+val+'&center='+center+'&type='+xtype,
			data:urls,
			type:"POST",
			success:function(data){
				if(data=="success"){
					window.location.href="?page=vouchering";
				}
			}
		});
	}
	function loadTrans(type){
		$.ajax({
			url: './content/vouchering_ajax.php?execute=loadTrans&type='+type,
			type:"POST",
			success:function(data){
				$("#tabs-1").html("");
				$("#tabs-2").html("");
				$("#tabs-3").html("");
				switch(type){
					case'ForApproval':
						$("#tabs-1").html(data);
					break;
					case'Approved':
						$("#tabs-2").html(data);
					break;
					case'Rejected':
						$("#tabs-3").html(data);
					break;
				}
				jQuery.tableNavigation();
			}
		});
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

