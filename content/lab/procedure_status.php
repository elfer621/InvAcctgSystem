<?php
if($_REQUEST['execute']){
	session_start();
	require_once"../../settings.php";
	require_once"../../class/dbConnection.php";
	require_once"../../class/dbUpdate.php";
	$db=new dbConnect();
	$db->openDb();
	$con=new dbUpdate();
	switch($_REQUEST['execute']){
		
	}
}else{
$receipt= $db->getWHERE("*","tbl_sales_items","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' 
	and reading='{$_REQUEST['reading']}' and skuid='{$_REQUEST['skuid']}' and category_id='{$_REQUEST['catid']}'");
if($_REQUEST['datainfo']=="data_teletech_patient"){
	$info= $db->getWHERE("*","{$_REQUEST['datainfo']}","where id='{$_REQUEST['idno']}'");
}else{
	$info= $db->getWHERE("*","{$_REQUEST['datainfo']}","where idno='{$_REQUEST['idno']}'");
}
	if($_POST){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		$data=array(
			'receipt'=>$_REQUEST['receipt'],
			'counter'=>$_REQUEST['counter'],
			'reading'=>$_REQUEST['reading'],
			'skuid'=>$_REQUEST['skuid'],
			'category_id'=>$_REQUEST['catid'],
			'datetime'=>$_REQUEST['procedure']['datetime'],
			'medical_tech'=>$_REQUEST['procedure']['medical_tech'],
			'status'=>$_REQUEST['procedure']['status'],
			'lab_exam_id'=>$_REQUEST['procedure']['lab_exam_id'],
			'notes'=>$_REQUEST['notes']
		);
		$sql = $db->genSqlInsert($data,'lab_procedure_status');
		$qry=mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}else{
			echo "<h3>Successfully Save...</h3>";
			echo "<script>$(document).ready(function(){window.opener.location.href = window.opener.location.href+'&txtsearch=".$_REQUEST['txtsearch']."';});</script>";
		}
	}

$procedure = $db->getWHERE("*","lab_procedure_status","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}' and category_id='{$_REQUEST['catid']}' and skuid='{$_REQUEST['skuid']}'");
$labforms = $db->resultArray("*","lab_exam_format","");
$_age = floor((time() - strtotime($info['birth_date'])) / 31556926);
?>

<div class="content" style="min-height:300px;width:100%!important;">
	<h2><?=$receipt['item_desc']?></h2>
	<form name="frminfo" id="frminfo" method="post">
	<div style="clear:both;height:10px;"></div>
		<div style="width:25%;float:left;">
			<fieldset>
				<legend>Patient Information</legend>
				<div style="width:150px;float:left;">First Name</div>
				<input value="<?=$info['first_name']?>" name="patient[first_name]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Last Name</div>
				<input value="<?=$info['last_name']?>" name="patient[last_name]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Requesting Physician</div>
				<input value="<?=$info['requesting_physician']?>" name="patient[requesting_physician]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Age</div>
				<input value="<?=$info['age']?$info['age']:$_age?>" name="patient[age]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Sex</div>
				<input value="<?=$info['gender']?>" name="patient[gender]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="width:100%;">
				<legend>Menu</legend>
				<input id="bt7" class="buthov" type="submit" value="Save" style="float:right;height:40px;width:150px;float:left;"/>
			</fieldset>
		</div>
		<div style="width:65%;float:right;">
			<div style="display: flex;">
				<fieldset style="align-items: stretch;width:100%;float:left;">
					<div style="width:150px;float:left;">Date Time</div>
					<input value="<?=$procedure['datetime']?$procedure['datetime']:date("Y-m-d H:i:s");?>" name="procedure[datetime]" type="text" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Medical Technologist</div>
					<input value="<?=$procedure['medical_tech']?$procedure['medical_tech']:$_SESSION['complete_name']?>" name="procedure[medical_tech]" type="text" style="width:300px;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Status</div>
					<select name="procedure[status]" style="width:300px;">
						<option value=""></option>
						<option <?= $procedure['status']=="Pending"?"selected":""?> value="Pending">Pending</option>
						<option <?= $procedure['status']=="Repeat"?"selected":""?> value="Repeat">Repeat</option>
						<option <?= $procedure['status']=="Done"?"selected":""?> value="Done">Done</option>
					</select>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Lab Form Format</div>
					<select name="procedure[lab_exam_id]" style="width:300px;">
						<option value=""></option>
						<?php
							foreach($labforms as $k=>$v){
								echo "<option ".($procedure[lab_exam_id]==$v['lab_exam_id']?"selected":"")." value='{$v['lab_exam_id']}'>{$v['report_title']}</option>";
							}
						?>
					</select>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Notes</div>
					<div style="clear:both;height:5px;"></div>
					<textarea name="notes" style="width:450px;height:150px;"><?=$procedure['notes']?></textarea>
				</fieldset>
			</div>
		</div>
	</form>
</div>
<script>

$(document).ready(function() {
	
});

</script>
<?php } ?>