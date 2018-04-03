<?php

$date = $_REQUEST['begdate']?"a.`date` between '".$_REQUEST['begdate']."' and '".$_REQUEST['enddate']."'":"a.`date` between '".date('Y-m-01')."' and '".date('Y-m-d')."'";
$remarks = $_REQUEST['remarks']?"and a.account_desc like '%".$_REQUEST['remarks']."%'":"";
$report_type = $_REQUEST['report_type']?"and a.report_type='{$_REQUEST['report_type']}'":"";
$subsidiary = $_REQUEST['subsidiary']?"and a.subsidiary='{$_REQUEST['subsidiary']}'":"";
$account_type = $_REQUEST['account_type']?"and a.account_type='{$_REQUEST['account_type']}'":"";
$sql = "select a.*,b.* from tbl_journal_entry a left join tbl_vouchering b on a.refid=b.id where $date $remarks $report_type $subsidiary $account_type order by a.refid desc";

$qry = mysql_query($sql);
if(!$qry){
	echo mysql_error();
}
?>
<div style="height:650px;background-color:white;padding:10px;overflow:auto;">
	<fieldset>
		<legend>Filter</legend>
		<form method="post">
			<div style="float:left;margin-right:10px;">
				<div style="float:left;margin-right:5px;width:100px;">BegDate:</div>
				<input type="text" style="width:100px;float:left;margin-right:20px;" name="begdate" id="begdate" value="<?=$_REQUEST['begdate']?$_REQUEST['begdate']:date('Y-m-01')?>"/>
				<div style="float:left;margin-right:5px;width:100px;">EndDate:</div>
				<input type="text" style="width:100px;float:left;" name="enddate" id="enddate" value="<?=$_REQUEST['enddate']?$_REQUEST['enddate']:date('Y-m-d')?>"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:5px;width:100px;">Remarks:</div>
				<input type="text" style="width:332px;float:left;margin-right:20px;" name="remarks" id="remarks" />
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:5px;width:100px;">Account Type:</div>
				<select name="account_type" style="float:left;width:335px;">
					<option value=""></option>
					<option <?=$_REQUEST['account_type']=="EXP"?"selected":""?> value="EXP">EXP</option>
					<option <?=$_REQUEST['account_type']=="AST"?"selected":""?> value="AST">AST</option>
					<option <?=$_REQUEST['account_type']=="LIA"?"selected":""?> value="LIA">LIA</option>
					<option <?=$_REQUEST['account_type']=="EQU"?"selected":""?> value="EQU">EQU</option>
					<option <?=$_REQUEST['account_type']=="REV"?"selected":""?> value="REV">REV</option>
					<option <?=$_REQUEST['account_type']=="COS"?"selected":""?> value="COS">COS</option>
				</select>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;margin-right:5px;width:100px;">ReportType:</div>
				<select name="report_type" style="width:100px;float:left;margin-right:20px;">
					<option value=""></option>
					<option <?=$_REQUEST['report_type']=="PNL"?"selected":""?> value="PNL">PNL</option>
					<option <?=$_REQUEST['report_type']=="BS"?"selected":""?> value="BS">BS</option>
				</select>
				<div style="float:left;margin-right:5px;width:100px;">Subsidiary:</div>
				<select name="subsidiary" style="width:100px;float:left;margin-right:20px;">
					<option value=""></option>
					<option <?=$_REQUEST['subsidiary']=="Y"?"selected":""?> value="Y">YES</option>
					<option <?=$_REQUEST['subsidiary']=="N"?"selected":""?> value="N">NO</option>
				</select>
			</div>
			<div style="float:right;">
				<input type="submit" value="Execute" style="height:25px;width:100px;"/>
				<div style="clear:both;height:5px;"></div>
				<!--input type="button" value="Export" style="height:30px;width:100px;" onclick="tblToExcel('mytbl','JournalEntry_'+$('#begdate').val()+'_'+$('#begdate').val(),'JournalEntry')"/-->
				<input type="button" value="Export" style="height:25px;width:100px;" onclick="ExportToExcel('mytbl')"/>
				<div style="clear:both;height:5px;"></div>
				<input type="button" value="Print" style="height:25px;width:100px;" onclick="viewReport('./reports/journal_entries.php');" />
				<input type="hidden" id="xsql" name="xsql" value="<?=$sql?>"/>
			</div>
		</form>
	</fieldset>
	<div style="clear:both;height:5px;"></div>
	<?php include_once"journal_entry.php"; ?>
</div>
<script>
$('#begdate').datepicker({
	inline: true,
	changeMonth: true,
	changeYear: true,
	dateFormat:"yy-mm-dd"
});
$('#enddate').datepicker({
	inline: true,
	changeMonth: true,
	changeYear: true,
	dateFormat:"yy-mm-dd"
});
function viewRefid(val,xtype){
		var urls = getUrl();
		$.ajax({
			url: './content/vouchering_ajax.php?execute=viewVoucher&refid='+val,
			data:urls,
			type:"POST",
			success:function(data){
				if(data=="success"){
					if(xtype=='General Ledger'){
						window.location.href="?page=gl";
					}else{
						window.location.href="?page=vouchering";
					}
				}
			}
		});
	}
function viewReport(page){
		var sql = $("#xsql").val();
		if (window.showModalDialog) {
			window.showModalDialog(page+"?sql="+sql,"PO","dialogWidth:650px;dialogHeight:650px");
		} else {
			window.open(page+"?sql="+sql,"PO",'height=650,width=650,toolbar=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,modal=yes');
		}
	}
</script>