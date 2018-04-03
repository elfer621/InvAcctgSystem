<?php
function getAge($dt){
	$newdt = str_replace("/", "-", $dt);
	$newdt = explode("-",$newdt);
	if($newdt[2]>31){
		$newdt = date("Y-m-d", mktime(0, 0, 0, $newdt[0], $newdt[1], $newdt[2]));
	}else{
		$newdt = date("Y-m-d", mktime(0, 0, 0, $newdt[2], $newdt[1], $newdt[0]));
	}
	return floor((time() - strtotime($newdt)) / 31556926);
}
if($_REQUEST['execute']){
	session_start();
	require_once"../../settings.php";
	require_once"../../class/dbConnection.php";
	require_once"../../class/dbUpdate.php";
	$db=new dbConnect();
	$db->openDb();
	$con=new dbUpdate();
	switch($_REQUEST['execute']){
		case'toSession':
			foreach($_REQUEST as $key => $val){
				$_SESSION['records_filter'][$key] = $val;
			}
			if($_SESSION['records_filter']){
				echo "success";
			}
		break;
		case'list':
			$list = $db->resultArray("a.*","data_teletech_patient a ",
				" where company_name='{$_SESSION['records_filter']['company_name']}' and data_reference='{$_SESSION['records_filter']['data_reference']}' and (a.employee_no like '%{$_REQUEST['search']}%' or a.first_name like '%{$_REQUEST['search']}%' 
				or a.last_name like '%{$_REQUEST['search']}%' or a.hmo_policy_no like '%{$_REQUEST['search']}%')");
		?>
			<input type="text" name="search" id="search" style="float:left;width:75%;" />
			<input type="button" style="float;right;width:10%;margin-left:5px;" value="Search" onclick="search();"/>
			<div style="clear:both;height:5px;"></div>
			<table class="navigateableMain" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th style="border:none;">Employee No.</th>
						<th style="border:none;">First Name</th>
						<th style="border:none;">Last Name</th>
						<th style="border:none;">Age</th>
						<th style="border:none;">Gender</th>
						<th style="border:none;">HMO Policy No</th>
						<th style="border:none;">Compliance</th>
					</tr>
				</thead>
				<tbody style="font-size:10px;">
					<?php foreach($list as $key => $val){ ?>
						<tr>
							<td><a href="?page=demographic_input&idno=<?=$val['id']?>"><?=$val['employee_no']?></a></td>
							<td style="text-align:left;"><a href="?page=demographic_input&idno=<?=$val['id']?>"><?=$val['first_name']?></a></td>
							<td style="text-align:center;"><?=$val['last_name']?></td>
							<td style="text-align:right;"><?=$val['age']?></td>
							<td style="text-align:right;"><?=$val['gender']?></td>
							<td style="text-align:center;"><?=$val['hmo_policy_no']?></td>
							<td style="text-align:center;"><?=$val['compliance']?></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<script>
			$('#search').keypress(function(e) {
				if(e.which == 13) {
					search();
				}
			});
			</script>
		<?
		break;
	}
}else{


// echo "<pre>";
// print_r($_SESSION['records_filter']);
// echo "</pre>";
	if($_POST){
		$sql = $db->genSqlInsert($_POST['data'],'data_teletech_patient');
		$qry=mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}else{
			echo "<h2 style='color:green;'>Successfully Save...</h2>";
		}
	}
if($_REQUEST['idno']){	
	$info= $db->getWHERE("*","data_teletech_patient","where company_name='{$_SESSION['records_filter']['company_name']}' and data_reference='{$_SESSION['records_filter']['data_reference']}' and id='{$_REQUEST['idno']}'");
	$defval = $db->resultArray("*","data_defval","where tbl_name='data_teletech_patient'");
	foreach($defval as $k => $v){
		$valdef[$v['col_name']] = explode("|",$v['defval']);
	}
	$_age = getAge($info['date_of_birth']);

	$col = array('compliance','bmi','bp','cbc','fbs','blood_chem','fecalysis','urinalysis','cxr','ecg','pap_smear',
		'pmh','allergies','physical_exam','dental','significant_findings','recommendation','classification');
}
$complist = $db->resultArray("distinct company_name","data_teletech_patient","where year=year(now())");
$dataref = $db->resultArray("distinct data_reference","data_teletech_patient","where year=year(now())");
?>

<div class="content" style="min-height:300px;width:100%!important;">
	<h2>CSACCI Mobile</h2>
	<form name="frminfo" id="frminfo" method="post" onsubmit="return validateForm()">
	<div style="clear:both;height:10px;"></div>
		<div style="width:35%;float:left;">
			<fieldset>
				<legend>Records Filter</legend>
				<div style="width:150px;float:left;">Company Name</div>
				<select id="company_name" style="float:left;width:300px;">
					<option value="">Select</option>
					<?php foreach($complist as $k => $v){ ?>
					<option <?= $_SESSION['records_filter']['company_name']==$v['company_name']?"selected":""?> value="<?=$v['company_name']?>"><?=$v['company_name']?></option>
					<?php } ?>
				</select>
				<div style="width:150px;float:left;">Data Reference</div>
				<select id="data_reference" style="float:left;width:300px;">
					<option value="">Select</option>
					<?php foreach($dataref as $k => $v){ ?>
					<option <?= $_SESSION['records_filter']['data_reference']==$v['data_reference']?"selected":""?> value="<?=$v['data_reference']?>"><?=$v['data_reference']?></option>
					<?php } ?>
				</select>
			</fieldset>
			<div style="clear:both;height:10px;"></div>
			<fieldset>
				<legend>Patient Information</legend>
				<div style="width:150px;float:left;">Company Name</div>
				<input value="<?=$_SESSION['records_filter']['company_name']?>" name="data[company_name]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Data Reference</div>
				<input value="<?=$_SESSION['records_filter']['data_reference']?>" name="data[data_reference]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<input value="<?=$info['id']?>" name="data[id]" type="hidden" style="width:300px;"/>
				<div style="width:150px;float:left;">Date Registered</div>
				<input value="<?=$info['date_registered']?$info['date_registered']:date('Y-m-d')?>" name="data[date_registered]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Tracking No.</div>
				<input value="<?=$info['tracking_num']?>" name="data[tracking_num]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Employee No</div>
				<input value="<?=$info['employee_no']?>" name="data[employee_no]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">First Name</div>
				<input value="<?=$info['first_name']?>" name="data[first_name]" id="first_name" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Last Name</div>
				<input value="<?=$info['last_name']?>" name="data[last_name]" id="last_name" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Birth Date</div>
				<input value="<?=$info['date_of_birth']?>" name="data[date_of_birth]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Age</div>
				<input value="<?=$info['age']?$info['age']:$_age?>" name="data[age]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Sex</div>
				<select name="data[gender]" style="width:300px;">
					<option value="">Select</option>
					<option <?=$info['gender']=="Male"?"Selected":""?> value="Male">Male</option>
					<option <?=$info['gender']=="Female"?"Selected":""?> value="Female">Female</option>
				</select>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">HMO Policy No</div>
				<input value="<?=$info['hmo_policy_no']?>" name="data[hmo_policy_no]" type="text" style="width:300px;"/>
				<!--div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Height</div>
				<input value="<?=$info['height']?>" name="data[height]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Weight</div>
				<input value="<?=$info['weight']?>" name="data[weight]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div-->
			</fieldset>
			
			
		</div>
		<div style="width:60%;float:right;">
			<fieldset style="width:90%;">
				<legend>Menu</legend>
				<input id="bt8" onclick="$('#frminfo').submit()" class="buthov" type="button" value="Save" style="float:right;height:40px;width:100px;float:left;"/>
				<input id="bt7" class="buthov" type="button" value="List" onclick="viewlist()" style="float:right;height:40px;width:100px;float:left;"/>
				<?php if($info['receipt'] == null){ ?>
				<input id="bt6" class="buthov" type="button" value="POS" onclick="openPOS()" style="float:right;height:40px;width:100px;float:left;"/>
				<?php } ?>
				<input id="bt5" class="buthov" type="button" value="PE" onclick="openPE()" style="float:right;height:40px;width:100px;float:left;"/>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<div style="display: flex;">
				
				<fieldset style="align-items: stretch;width:100%;float:left;">
					
					<table class="navigateableMain" id="items_tbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<th style="border:none;">Parameter</th>
								<th style="border:none;width:250px;">Results</th>
							</tr>
						</thead>
						<tbody>
						<?php
						foreach($col as $key => $val){
							echo "<tr>";
							echo "<td>".strtoupper($val).($val=="bmi"?"   <input type='text' readonly style='float:right;text-align:center;' value='".number_format($info['weight']/($info['height']*$info['height']),2)."'/>":"")."</td>";
							if($valdef[$val]){
								echo "<td>";
								
								echo "<select name='data[{$val}]' style='width:100%;'>";
								echo "<option value=''>Select</option>";
								foreach($valdef[$val] as $a => $b){
									echo "<option ".($info[$val]==$b?"Selected":"")." value='{$b}'>{$b}</option>";
								}
								echo "</select>";
								echo "</td>";
							}else{
								echo "<td><input type='text' name='data[{$val}]' value='{$info[$val]}' style='width:100%;'/></td>";
							}
							echo "</tr>";
						}
						?>
						</tbody>
					</table>
				</fieldset>
			</div>
		</div>
	</form>
</div>
<script>
var posWindow;
$(document).ready(function() {
	$('input[name="data[date_of_birth]"]').mask('9999-99-99',{placeholder:"yyyy-mm-dd"});
	$("form input:checkbox").attr( "checked" , true );
	$('input[name="data[date_registered]"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		yearRange: '1900:+0',
		dateFormat:"yy-mm-dd"
	});
});
function validateForm() {
    if ($("#first_name").val() == "") {
        alert("FirstName must be filled out");
        return false;
    }
	if ($("#last_name").val() == "") {
        alert("LastName must be filled out");
        return false;
    }
	return true;
}
function getAge(dateString) {
    var today = new Date();
    var birthDate = new Date(dateString);
    var age = today.getFullYear() - birthDate.getFullYear();
    var m = today.getMonth() - birthDate.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    return age;
}

$('input[name="data[date_of_birth]"]').keypress(function(e) {
    if(e.which == 13) {
        var age = getAge($(this).val());
		$('input[name="data[age]"]').val(age);
    }
});
$("#items_tbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		del();
	}
});
$("#company_name").change(function(){
	var val = $(this).val();
	$.ajax({
		url: './content/lab/teletech_input.php?execute=toSession&company_name='+val,
		type:"POST",
		success:function(data){
			//window.location=removeURLParameter(document.URL, 'refid');
			$("input[name='data[company_name]']").val(val);
		}
	});
});
$("#data_reference").change(function(){
	var val = $(this).val();
	$.ajax({
		url: './content/lab/teletech_input.php?execute=toSession&data_reference='+val,
		type:"POST",
		success:function(data){
			//window.location=removeURLParameter(document.URL, 'refid');
			$("input[name='data[data_reference]']").val(val);
		}
	});
});
function del(){
	$('input[type="checkbox"]:checked').closest("tr").remove();
}
function openPOS(){
	if (window.showModalDialog) {
		posWindow = window.showModalDialog('index.php?page=sales&specialcase=1&idno='+getParam('idno'),"POS","dialogWidth:1050px;dialogHeight:650px");
	} else {
		posWindow = window.open('index.php?page=sales&specialcase=1&idno='+getParam('idno'),"POS",'height=650,width=1050,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
	}
}
function openPE(){
	if (window.showModalDialog) {
		window.showModalDialog('index.php?page=physical_exam&idno='+getParam('idno'),"PE","dialogWidth:1050px;dialogHeight:650px");
	} else {
		window.open('index.php?page=physical_exam&idno='+getParam('idno'),"PE",'height=650,width=1050,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
	}
}
function viewlist(){
	var custid = $("#business_name").val();
	clickDialogUrl("dialogbox3",900,500,'./content/lab/teletech_input.php?&execute=list',"Listing");
}
function search(){
	var search = $("#search").val();
	clickDialogUrl("dialogbox3",900,500,'./content/lab/teletech_input.php?&execute=list&search='+search,"Listing");
	$("#search").focus();
}
</script>
<?php } ?>