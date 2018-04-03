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
		case'DelProc':
			$sql="delete from lab_procedure_status where 
				receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}' and skuid='{$_REQUEST['skuid']}' and category_id='{$_REQUEST['category_id']}'";
			$qry=mysql_query($sql);
			if($qry){
				echo "Delete Procedure...";
				$sql2="delete from lab_results where 
				receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}' and skuid='{$_REQUEST['skuid']}' and category_id='{$_REQUEST['category_id']}'";
				$qry2=mysql_query($sql2);
				if($qry2){
					echo "success";
				}
			}else{
				echo mysql_error();
			}
		break;
	}
}else{
$procedure = $db->getWHERE("*","lab_procedure_status","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}' and category_id='{$_REQUEST['catid']}' and skuid='{$_REQUEST['skuid']}'");
//$lab = $db->getWHERE("*","lab_exam_format","where category_id='{$_REQUEST['catid']}' and skuid='{$_REQUEST['skuid']}'");
$lab = $db->getWHERE("*","lab_exam_format","where lab_exam_id='{$procedure['lab_exam_id']}'");
if($_REQUEST['datainfo']=="data_teletech_patient"){
	$info= $db->getWHERE("*","{$_REQUEST['datainfo']}","where id='{$_REQUEST['idno']}'");
}else{
	$info= $db->getWHERE("*","{$_REQUEST['datainfo']}","where idno='{$_REQUEST['idno']}'");
}
	if($_POST){
		// echo "<pre>";
		// print_r($_POST);
		// echo implode("|",$_POST['patient']);
		// echo "</pre>";
		if($_REQUEST['catid']==6){ //xray
			$data=array(
				'receipt'=>$_REQUEST['receipt'],
				'counter'=>$_REQUEST['counter'],
				'reading'=>$_REQUEST['reading'],
				'skuid'=>$_REQUEST['skuid'],
				'category_id'=>$_REQUEST['catid'],
				'patient_info'=>implode("|",$_POST['patient']),
				'examination'=>$_REQUEST['examination'],
				'findings'=>$_REQUEST['findings'],
				'impression'=>$_REQUEST['impression'],
				'doctor_signature'=>$_REQUEST['doctor_signature']
			);
			$sql = $db->genSqlInsert($data,'lab_xray_results');
			$qry=mysql_query($sql);
			if(!$qry){
				echo mysql_error();
			}else{
				echo "<h2>Successfully Save...</h2>";
			}
			$data=array(
				'receipt'=>$_REQUEST['receipt'],
				'counter'=>$_REQUEST['counter'],
				'reading'=>$_REQUEST['reading'],
				'skuid'=>$_REQUEST['skuid'],
				'category_id'=>$_REQUEST['catid'],
				'patient_info'=>implode("|",$_POST['patient']),
				'report_title'=>'X-Ray',
				'category_label'=>$lab['category_label'],
				'fields_label'=>'',
				'default_value'=>$lab['default_value'],
				'unit_label'=>'',
				'reference_range'=>'',
				'lab_results'=>''
			);
			$sql = $db->genSqlInsert($data,'lab_results');
			$qry=mysql_query($sql);
			if(!$qry){
				echo mysql_error();
			}else{
				echo "<h2>Successfully Save...</h2>";
			}
		}else{
			$data=array(
				'receipt'=>$_REQUEST['receipt'],
				'counter'=>$_REQUEST['counter'],
				'reading'=>$_REQUEST['reading'],
				'skuid'=>$_REQUEST['skuid'],
				'category_id'=>$_REQUEST['catid'],
				'patient_info'=>implode("|",$_POST['patient']),
				'report_title'=>$lab['report_title'],
				'category_label'=>$lab['category_label'],
				'fields_label'=>implode("|",$_POST['fields']),
				'default_value'=>$lab['default_value'],
				'unit_label'=>implode("|",$_POST['units']),
				'reference_range'=>implode("|",$_POST['range']),
				'lab_results'=>implode("|",$_POST['results'])
			);
			$sql = $db->genSqlInsert($data,'lab_results');
			$qry=mysql_query($sql);
			if(!$qry){
				echo mysql_error();
			}else{
				echo "<h2>Successfully Save...</h2>";
			}
			
		}
		
	}

$results = $db->getWHERE("*","lab_results","where receipt='{$_REQUEST['receipt']}' and counter='{$_REQUEST['counter']}' and reading='{$_REQUEST['reading']}' and category_id='{$_REQUEST['catid']}' and skuid='{$_REQUEST['skuid']}'");
$_age = floor((time() - strtotime($info['birth_date'])) / 31556926);
?>

<div class="content" style="min-height:300px;width:100%!important;">
	<h2><?=$lab['report_title']?></h2>
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
				<div style="width:150px;float:left;">Date & Time</div>
				<input value="<?=date("Y-m-d H:i:s")?>" name="patient[datetime]" type="text" style="width:300px;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="width:100%;">
				<legend>Menu</legend>
				<input id="bt7" class="buthov" type="submit" value="Save" style="float:right;height:40px;width:100px;float:left;"/>
				<input id="bt8" onclick="DelProc(<?=$_REQUEST['receipt']?>,<?=$_REQUEST['counter']?>,<?=$_REQUEST['reading']?>,<?=$_REQUEST['skuid']?>,<?=$_REQUEST['catid']?>)" class="buthov" type="button" value="Del" style="float:right;height:40px;width:100px;float:right;"/>
			</fieldset>
		</div>
		<div style="width:70%;float:right;">
			<div style="display: flex;">
				<fieldset style="align-items: stretch;width:100%;float:left;">
					<?php if($_REQUEST['catid']==6){ //X-Ray ?>
						<table class="navigateableMain" id="items_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th>Parameter</th>
									<th>Options/Default Values</th>
								</tr>
								<tbody>
									<tr>
										<td>Examination</td>
										<td><input type="text" name="examination" value="Chest X-Ray"/></td>
									</tr>
									<tr>
										<td>Result Type</td>
										<td>
											<select name="result_type" id="result_type" onchange="xrayDefault(this.value)">
												<option value="Normal">Normal</option>
												<option value="ALV">ALV</option>
												<option value="CM">CM</option>
												<option value="INFLAMATORY">INFLAMATORY</option>
												<option value="MCM">MCM</option>
												<option value="DEXTRO">DEXTRO</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>Findings:</td>
										<td>
											<textarea style="height:150px;width:500px;" name="findings" id="findings">The lung fields are clear. The hila and pulmonary vascular markings are within normal limits. The heart is not enlarged. The trachea is midline. The hemidiaphragms and costophrenic angles are intact. The osseous and soft tissue chest wall structures are unremarkable.</textarea>
										</td>
									</tr>
									<tr>
										<td>Impression:</td>
										<td>
											<textarea style="height:150px;width:500px;" name="impression" id="impression">NO SIGNIFICANT CHEST FINDINGS.</textarea>
										</td>
									</tr>
									<tr>
										<td>Doctor Signatory</td>
										<td>
											<select name="doctor_signature" id="doctor_signature">
												<option value="DIOGENES R. LABAJO, MD, DPBR">DIOGENES R. LABAJO, MD, DPBR</option>
												<option value="ALEX JUSTIMBASTE, MD, DPBR">ALEX JUSTIMBASTE, MD, DPBR</option>
											</select>
										</td>
									</tr>
								</tbody>
							</thead>
						</table>
					<?php }else{ ?>
						<table class="navigateableMain" id="items_tbl" cellspacing="0" cellpadding="0" width="100%">
							<thead>
								<tr>
									<th><input type="button" value="Del" onclick="del()"/></th>
									<th style="border:none;">Parameter</th>
									<th style="border:none;width:150px;">Results</th>
									<th style="border:none;">Units</th>
									<th style="border:none;">Reference Range</th>
								</tr>
							</thead>
							<tbody>
							<?php
							if($_REQUEST['catid']==4){ //Consultation
								if($procedure['lab_exam_id']==22){ //BMI
									echo "<tr>
										<td></td>
										<td><input tabindex='-1' name='fields[]' style='border:none;' value='Height'/></td>
										<td><input value='{$info['height']}' type='text' name='results[]' style='width:100%;'/></td>
										<td></td>
										<td></td>
									</tr>";
									echo "<tr>
										<td></td>
										<td><input tabindex='-1' name='fields[]' style='border:none;' value='Weight'/></td>
										<td><input value='{$info['weight']}' type='text' name='results[]' style='width:100%;'/></td>
										<td></td>
										<td></td>
									</tr>";
									echo "<tr>
										<td></td>
										<td><input tabindex='-1' name='fields[]' style='border:none;' value='BMI'/></td>
										<td><input value='".number_format($info['weight']/($info['height']*$info['height']),2)."' type='text' name='results[]' style='width:100%;'/></td>
										<td></td>
										<td></td>
									</tr>";
									echo "<tr>
										<td></td>
										<td><input tabindex='-1' name='fields[]' style='border:none;' value='Classification'/></td>
										<td><input value='{$info['bmi']}' type='text' name='results[]' style='width:100%;'/></td>
										<td></td>
										<td></td>
									</tr>";
								}else{
									echo "<tr>
										<td colspan='5' style='text-align:center;'><input type='button' value='PE' onclick='openPE()' style='width:350px;height:30px;'/></td>
									</tr>";
								}
							}else{
								if($lab){
									$category = explode("|",$lab['category_label']);
									$fields_label = $results?explode("|",$results['fields_label']):explode("|",$lab['fields_label']);
									$default_value = explode("|",$lab['default_value']);
									$unit_label = $results?explode("|",$results['unit_label']):explode("|",$lab['unit_label']);
									$reference_range = $results?explode("|",$results['reference_range']):explode("|",$lab['reference_range']);
									$results_val = explode("|",$results['lab_results']);
									for($x=0;$x<count($fields_label);$x++){
										if($category[$x]){
											echo "<tr><td colspan='4'><input type='checkbox' tabindex='-1'><b>{$category[$x]}</b></td></tr>";
										}
										echo "<tr>
											<td><input type='checkbox' tabindex='-1'></td>
											<td style='padding-left:25px;'><input tabindex='-1' name='fields[]' style='border:none;' value='{$fields_label[$x]}'/></td>
											<td>";
											if($default_value[$x]){
												$select = explode(",",$default_value[$x]);
												echo "<select name='results[]' style='width:100%;'>";
												foreach($select as $k=>$v){
													echo "<option ".($results_val[$x]==$v?"selected":"")." value='$v'>$v</option>";
												}
												echo "</select>";
											}else{
												echo "<input value='".$results_val[$x]."' type='text' name='results[]' style='width:100%;'/>";
											}
											echo "</td>
											<td><input tabindex='-1' name='units[]' style='border:none;' value='{$unit_label[$x]}'/></td>
											<td><input tabindex='-1' name='range[]' style='border:none;' value='{$reference_range[$x]}'/></td>
										</tr>";
									}
									
								}
							}
							?>
							</tbody>
						</table>
					<?php } ?>
				</fieldset>
			</div>
		</div>
	</form>
</div>
<script>
$(document).ready(function() {
	$("form input:checkbox").attr( "checked" , true );
});
function xrayDefault(val){
	switch(val){
		case'Normal':
			var findings = "The lung fields are clear. The hila and pulmonary vascular markings are within normal limits. The heart is not enlarged. The trachea is midline. The hemidiaphragms and costophrenic angles are intact. The osseous and soft tissue chest wall structures are unremarkable.";
			var impression = "NO SIGNIFICANT CHEST FINDINGS.";
		break;
		case'ALV':
			var findings = "Suspicious density is seen in the left apical region. The hila and pulmonary vascular markings are within normal limits. The heart is not enlarged. The trachea is midline. The hemidiaphragms and costophrenic angles are intact. The osseous and soft tissue chest wall structures are unremarkable.";
			var impression = "SUSPICIOUS DENSITY, LEFT APICAL REGION. SUGGEST APICOLORDOTIC VIEW FOR FURTHER EVALUATION.";
		break;
		case'CM':
			var findings = "The lung fields are clear. The hila and pulmonary vascular markings are within normal limits. The heart is enlarged. The trachea is midline. The hemidiaphragms and costophrenic angles are intact. The osseous and soft tissue chest wall structures are unremarkable.";
			var impression = "CARDIOMEGALY. 2D ECHO CORRELATION IS SUGGESTED.";
		break;
		case'INFLAMATORY':
			var findings = "Haziness is noted in the left lower lung field. The hila and pulmonary vascular markings are within normal limits. The heart is not enlarged. The trachea is midline. Both hemidiaphragm and costophrenic angles are intact. The osseous and soft tissue chest wall structures are unremarkable.";
			var impression = "1) INFLAMMATORY PROCESS, LEFT LOWER LUNG FIELD IS CONSIDERED. CLINICAL CORRELATION AND FF-UP STUDY ARE SUGGESTED.";
		break;
		case'MCM':
			var findings = "The lung fields are clear. The hila and pulmonary vascular markings are within normal limits. The heart is slightly enlarged. The trachea is midline. The hemidiaphragms and costophrenic angles are intact. The osseous and soft tissue chest wall structures are unremarkable.";
			var impression = "MILD CARDIOMEGALY. 2D ECHO CORRELATION IS SUGGESTED.";
		break;
		case'DEXTRO':
			var findings = "The lung fields are clear. The hila and pulmonary vascular markings are within normal limits. The heart is not enlarged. The trachea is midline. The hemidiaphragms and costophrenic angles are intact. There is a mild rightward curvature in the thoracic spine. The osseous and soft tissue chest wall structures are unremarkable.";
			var impression = "MILD DEXTROSCOLIOSIS OF THE THORACIC SPINE OTHERWISE NO SIGNIFICANT CHEST FINDINGS.";
		break;
	}
	$("#findings").text(findings);
	$("#impression").text(impression);
}
$("#items_tbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		del();
	}
});
function del(){
	$('input[type="checkbox"]:checked').closest("tr").remove();
}

function openXray(){
	if (window.showModalDialog) {
		window.showModalDialog('index.php?page=xray&idno='+getParam('idno'),"X-Ray","dialogWidth:1050px;dialogHeight:650px");
	} else {
		window.open('index.php?page=xray&idno='+getParam('idno'),"X-Ray",'height=650,width=1050,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
	}
}
function openPE(){
	if (window.showModalDialog) {
		window.showModalDialog('index.php?page=physical_exam&idno='+getParam('idno'),"PE","dialogWidth:1050px;dialogHeight:650px");
	} else {
		window.open('index.php?page=physical_exam&idno='+getParam('idno'),"PE",'height=650,width=1050,toolbar=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,modal=yes,location=yes');
	}
}
function DelProc(receipt,counter,reading,skuid,category_id){
	$.ajax({
		url: './content/lab/dynamic_lab.php?execute=DelProc',
		data:{receipt:receipt,counter:counter,reading:reading,skuid:skuid,category_id:category_id},
		type:"POST",
		success:function(data){
			//window.location=removeURLParameter(document.URL, 'refid');
			if(data=="success"){
				window.location = '?page=queue';
			}else{
				alert(data);
			}
		}
	});
}
</script>
<?php } ?>