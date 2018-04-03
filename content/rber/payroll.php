<?php
$location = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
if($_REQUEST['refid']){
	$header = $db->getWHERE("*","tbl_payroll_header","where id='{$_REQUEST['refid']}'");
	$entries = $db->resultArray("*","tbl_payroll_entry","where refid='{$_REQUEST['refid']}' order by name asc");
}
?>
<h2>Payroll</h2>
<form name="frmPayrollHeader" id="frmPayrollHeader" method="post">
	<fieldset style="width:350px;float:left;">
		<legend>Payroll Period</legend>
		<div style="float:left;margin-right:10px;width:80px;">RefID:</div>
		<input readonly style="float:left;margin-right:10px;width:80px;" type="text" name="refid" id="refid" value="<?=$_REQUEST['refid']?$_REQUEST['refid']:$db->getNextID('id','tbl_payroll_header')?>"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;width:80px;">BegDate:</div>
		<input type="text" name="begdate" id="begdate" value="<?=$header['begdate']?>" style="float:left;margin-right:10px;width:80px;"/>
		<div style="float:left;margin-right:10px;">EndDate:</div>
		<input type="text" name="enddate" id="enddate" value="<?=$header['enddate']?>" style="float:left;width:80px;"/>
		<div style="clear:both;height:5px;"></div>
		<div style="float:left;margin-right:10px;">Remarks:</div>
		<div style="clear:both;height:5px;"></div>
		<textarea style="width:100%;min-height:40px;" name="remarks"><?=$header['remarks']?></textarea>
	</fieldset>
	<input type="button" value="SAVE" onclick="savePayroll();" style="width:120px;height:30px;float:right;margin:3px;background:blue;color:white;"/>
	<input type="button" value="Emp List" onclick="empList();" style="width:120px;height:30px;float:right;margin:3px;"/>
	<input type="button" value="Payroll List" onclick="payrollList();" style="width:120px;height:30px;float:right;margin:3px;"/>
	<a href="./reports/payroll_summary.php?refid=<?=$_REQUEST['refid']?>" target="_blank"><input type="button" value="Summary" style="width:120px;height:30px;float:right;margin:3px;"/></a>
	<input type="button" value="Payslip" onclick="viewPayslip(<?=$_REQUEST['refid']?>)" style="width:120px;height:30px;float:right;margin:3px;"/>
	<a href="./reports/payroll_deduction_summary.php?payrollid=<?=$_REQUEST['refid']?>" target="_blank"><input type="button" value="Deduction List" style="width:120px;height:30px;float:right;margin:3px;"/></a>
	<input type="button" value="Per Emp Deduction" onclick="perEmpList()" style="width:120px;height:30px;float:right;margin:3px;"/>
	<input type="button" value="Advs Entry (BegBal)" onclick="advancesEntry();" style="width:120px;height:30px;float:right;margin:3px;"/>
	<div style="clear:both;height:10px;"></div>
	<table class="navigateableMain" id="tblPayroll" cellspacing="0" cellpadding="0" width="100%">
		<thead>
			<tr>
				<th style="width:5px;"><input type="checkbox" id="checkAll" ></th>
				<th style="width:12px;"></th>
				<th style="width:40px;">Menu</th>
				<th style="width:20px;">Emp ID</th>
				<th>Employee Name</th>
				<th style="width:50px;">Daily Rate</th>
				<th style="width:70px;">Reg Pay</th>
				<th  style="width:50px;">OT Hours</th>
				<th  style="width:60px;">OT Amt</th>
				<th style="width:50px;">Added Amt</th>
				<th style="width:70px;">Gross Total</th>
				<th  style="width:50px;">SSS</th>
				<th  style="width:50px;">PhilHealth</th>
				<th  style="width:50px;">Pagibig</th>
				<th  style="width:40px;">Others</th>
				<th style="width:50px;">Less Total</th>
				<th  style="width:50px;">Adj</th>
				<th  style="width:60px;">Net Total</th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($entries as $key => $val){
			echo '<tr id="emp'.$val['empid'].'">
							<td><input class="empcheckbox" type="checkbox"  ></td>
							<td><input type="text" class="entryid" name="payrollList['.$val['empid'].'][id]" value="'.$val['id'].'" style="width:100%;font-size:8px;" readonly/></td>
							<td>
								<input type="button" value="DTR" onclick="dtr('.$val['empid'].')" style="width:45px;float:left;"/>
								<input type="button" value="Others" onclick="others('.$val['empid'].')" style="width:45px;float:left;"/>
							</td>
							<td><input type="text" class="plempid" name="payrollList['.$val['empid'].'][empid]" value="'.$val['empid'].'" style="width:100%;"/></td>
							<td><input type="text" class="plname" name="payrollList['.$val['empid'].'][name]" value="'.$val['name'].'" title="'.$val['name'].'" style="width:100%;"/></td>
							<td><input type="text" class="pldailyrate" name="payrollList['.$val['empid'].'][dailyrate]" value="'.number_format($val['dailyrate'],2).'" style="width:100%;text-align:right;" readonly/></td>
							<td><input type="text" class="plregtime" name="payrollList['.$val['empid'].'][regtime]" value="'.number_format($val['regtime'],2).'" style="width:100%;text-align:right;" readonly/></td>
							<td><input type="text" class="plothours" name="payrollList['.$val['empid'].'][othours]" value="'.number_format($val['othours'],2).'" style="width:100%;text-align:right;" readonly/></td>
							<td><input type="text" class="plotamt" name="payrollList['.$val['empid'].'][otamt]" value="'.number_format($val['otamt'],2).'" style="width:100%;text-align:right;" readonly/></td>
							<td><input type="text" class="pladded_amt" name="payrollList['.$val['empid'].'][added_amt]" value="'.number_format($val['added_amt'],2).'" style="width:100%;text-align:right;color:blue;" readonly/></td>
							<td><input type="text" class="plgross_total" name="payrollList['.$val['empid'].'][gross_total]" value="'.number_format($val['gross_total'],2).'" style="width:100%;text-align:right;color:blue;" readonly/></td>
							<td><input type="text" class="plsss less" name="payrollList['.$val['empid'].'][sss]" value="'.number_format($val['sss'],2).'" style="width:100%;text-align:right;"/></td>
							<td><input type="text" class="plphilhealth less" name="payrollList['.$val['empid'].'][philhealth]" value="'.number_format($val['philhealth'],2).'" style="width:100%;text-align:right;"/></td>
							<td><input type="text" class="plpagibig less" name="payrollList['.$val['empid'].'][pagibig]" value="'.number_format($val['pagibig'],2).'" style="width:100%;text-align:right;"/></td>
							<td><input type="text" class="plother less" name="payrollList['.$val['empid'].'][others]" value="'.number_format($val['others'],2).'" style="width:100%;text-align:right;" readonly/></td>
							<td><input type="text" class="pllesstotal" name="payrollList['.$val['empid'].'][lesstotal]" value="'.number_format($val['lesstotal'],2).'" style="width:100%;text-align:right;color:red;" readonly/></td>
							<td><input type="text" class="pladjustment" name="payrollList['.$val['empid'].'][adjustment]" value="'.number_format($val['adjustment'],2).'" style="width:100%;text-align:right;color:blue;" readonly/></td>
							<td><input type="text" class="plnettotal" name="payrollList['.$val['empid'].'][nettotal]" value="'.number_format($val['nettotal'],2).'" style="width:100%;text-align:right;font-weight:bold;" readonly/></td>
						</tr>';
		}
		?>
		</tbody>
	</table>
</form>
<script>
var otreg_percent = strtodouble('<?=$_SESSION['settings']['otreg']?>');
var wod_percent = strtodouble('<?=$_SESSION['settings']['wod']?>');
var special_hol_percent = strtodouble('<?=$_SESSION['settings']['special_hol']?>');
var legal_hol_percent = strtodouble('<?=$_SESSION['settings']['legal_hol']?>');

$(document).ready(function() {
	jQuery.tableNavigationMain();
});
$('#begdate,#enddate').datepicker({
	inline: true,
	dateFormat:"yy-mm-dd"
});
$("#tblPayroll").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		$('input.empcheckbox[type="checkbox"]:checked').closest("tr").remove();
	}
});
$("#checkAll").click(function(){
    $('input.empcheckbox[type="checkbox"]').not(this).prop('checked', this.checked);
});
function viewPayslip(refid){
	var ids = getChecked();
	if(ids!=''){
	window.open('./reports/payroll_payslip.php?refid='+refid+'&empids='+ids,'_blank');
	}else{
		alert("Pls select Employees to view payslip...");
	}
}
function getChecked(){
	var selected=[];
	$('input.empcheckbox[type="checkbox"]:checked').each(function() {
		selected.push($(this).closest('tr').find(".plempid").val());
	});
	return selected.join(",");
}
function perEmpList(){
	 var emp = $("tr.selected").find(".plempid").val();
	 window.open('./reports/payroll_deduction_summary_emp.php?empid='+emp,'_blank');
}
$(document).on('change', '.plsss, .plphilhealth, .plpagibig, .plother', function () {
	$(this).closest("tr").find(".pllesstotal").val(sumLess($(this).closest("tr").find(".less")));
	netTotal($(this).closest("tr"));
});
function advancesEntry(){
	clickDialog('dialogbox2',700,600,'advancesEntry','Advances Entry',null,'rber/payroll_ajax.php');
}
function netTotal(row){
	var gross = strtodouble(row.find(".plgross_total").val());
	var adj = strtodouble(row.find(".pladjustment").val());
	var less = strtodouble(row.find(".pllesstotal").val());
	row.find(".plnettotal").val(strtocurrency((gross+adj)-less));
}
function savePayroll(){
	// var frm = $("form[name='frmPayrollHeader']");
	// frm.submit();
	var datastring = $("#frmPayrollHeader").serialize();
	$.ajax({
		url: './content/rber/payroll_ajax.php?execute=savePayroll',
		data:datastring,
		type:"POST",
		success:function(data){
			alert(data);
			window.location=document.URL;
		}
	});
}
function payrollList(){
	clickDialog('dialogbox2',500,500,'payrollList','Payroll List',null,'rber/payroll_ajax.php');
}
function empList(){
	clickDialog('dialogbox2',500,500,'emplist','DTR',null,'rber/payroll_ajax.php');
}
function others(empid){
	var payrollref = '&payrollref='+$("#refid").val();
	clickDialog('dialogbox2',700,700,'others_exps&empid='+empid+payrollref,'Others Expenses',null,'rber/payroll_ajax.php');
}
$(".pladjustment").click(function(){
	var empid = $(this).closest("tr").find(".plempid").val();
	adjustment(empid);
});
function adjustment(empid){
	var payrollref = '&payrollref='+$("#refid").val();
	clickDialog('dialogbox2',500,500,'adjustment&empid='+empid+payrollref,'Adjustment',null,'rber/payroll_ajax.php');
}
function dtr(empid){
	var urls = getUrl();
	var dates = '&begdate='+$("#begdate").val()+'&enddate='+$("#enddate").val();
	var rate = '&rate='+$("#emp"+empid).find(".pldailyrate").val();
	var payrollref = '&payrollref='+$("#refid").val();
	var entryid = '&entryid='+$("#emp"+empid).find(".entryid").val();
	clickDialog('dialogbox2',1200,500,'dtr&empid='+empid+dates+rate+payrollref+entryid,'DTR',urls,'rber/payroll_ajax.php');
}
function sumLess(row){
	var sum = 0;
	row.each(function() {
		var value = strtodouble($(this).val());
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	return sum;
}
function sumName(name){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(name).each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = strtodouble($(this).val());
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	return strtocurrency(sum);
	//return new Number(sum).formatMoney(2);
}
</script>