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
		
	}
}else{

	if($_POST){
		if($_POST['data']['blood_pressure']['systolic']>=120 and $_POST['data']['blood_pressure']['systolic']<=139){
			$bp=",bp='PreHpN'";
		}elseif($_POST['data']['blood_pressure']['systolic']>=140 and $_POST['data']['blood_pressure']['systolic']<=159){
			$bp=",bp='HpN 1'";
		}elseif($_POST['data']['blood_pressure']['systolic']>=160){
			$bp=",bp='HpN 2'";
		}else{
			$bp=",bp='Normal'";
		}
		
		$bmi = $_POST['info']['weight']/($_POST['info']['height']*$_POST['info']['height']);
		if($bmi<18.5){
			$bmiq=",bmi='Underweight'";
		}elseif($bmi>=18.5 and $bmi<=24.9){
			$bmiq=",bmi='Normal'";
		}elseif($bmi>=25 and $bmi<=29.9){
			$bmiq=",bmi='Over Weight'";
		}elseif($bmi>=30 and $bmi<=34.9){
			$bmiq=",bmi='Obese'";
		}elseif($bmi>=35 and $bmi<=39.9){
			$bmiq=",bmi='Severely Obese'";
		}elseif($bmi>=40){
			$bmiq=",bmi='Morbid Obese'";
		}
		
		$sql="update data_teletech_patient set 
		height='{$_POST['info']['height']}',
		weight='{$_POST['info']['weight']}',
		pe_data='".serialize($_POST['data'])."' $bp $bmiq where id='{$_REQUEST['idno']}'";
		$qry=mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}else{
			echo "<h2>Successfully Save...</h2>";
			echo "<script>$(document).ready(function(){window.opener.location.href = window.opener.location.href;});</script>";
		}
	}
if($_REQUEST['idno']){	
	$info= $db->getWHERE("*","data_teletech_patient","where id='{$_REQUEST['idno']}'");
	$rec = unserialize($info['pe_data']);
	$_age = getAge($info['date_of_birth']);
	// echo "<pre>";
	// print_r($rec);
	// echo "</pre>";
}
echo array_search("Peptic Ulcer",$rec['previous_illness']);
echo array_search("Blood Disorder",$rec['previous_illness']);
?>
<style>
#tbl1 tr td{
	border-bottom:1px solid #000;
}

</style>
<div class="content" style="min-height:300px;width:100%!important;">
	<h2><?=$lab['report_title']?></h2>
	<form name="frminfo" id="frminfo" method="post">
	<div style="clear:both;height:10px;"></div>
		<div style="width:20%;float:left;">
			<fieldset>
				<legend>Patient Information</legend>
				<div style="width:150px;float:left;">Employee No</div>
				<input value="<?=$info['employee_no']?>" name="info[employee_no]" type="text" />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">First Name</div>
				<input value="<?=$info['first_name']?>" name="info[first_name]" type="text" />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Last Name</div>
				<input value="<?=$info['last_name']?>" name="info[last_name]" type="text" />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Birth Date</div>
				<input value="<?=$info['date_of_birth']?>" name="info[date_of_birth]" type="text" />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Age</div>
				<input value="<?=$info['age']?$info['age']:$_age?>" name="info[age]" type="text" />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Sex</div>
				<select name="info[gender]" >
					<option value="">Select</option>
					<option <?=$info['gender']=="Male"?"Selected":""?> value="Male">Male</option>
					<option <?=$info['gender']=="Female"?"Selected":""?> value="Female">Female</option>
				</select>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">HMO Policy No</div>
				<input value="<?=$info['hmo_policy_no']?>" name="info[hmo_policy_no]" type="text" />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Height</div>
				<input value="<?=$info['height']?>" name="info[height]" type="text" />
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Weight</div>
				<input value="<?=$info['weight']?>" name="info[weight]" type="text" />
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<fieldset style="width:100%;">
				<legend>Menu</legend>
				<input id="bt7" class="buthov" type="submit" value="Save" style="float:right;height:40px;width:150px;float:left;"/>
			</fieldset>
		</div>
		<div style="width:75%;float:right;">
			<div style="display: flex;">
				<fieldset style="align-items: stretch;width:100%;float:left;">
					<table class="navigateableMain" id="tbl1" cellspacing="0" cellpadding="0" width="100%">
						<tbody>
							<tr>
								<td colspan="2">Medical History: Has examinee suffered from or been told to any of the following conditions</td>
							</tr>
							<tr>
								<td style="width:200px;">A. PREVIOUS ILLNESS</td>
								<td>
									<input <?=in_array("Allergies",$rec['previous_illness'],true)?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Allergies"/> Allergies
									<input <?=in_array("Blood Disorder",$rec['previous_illness'])?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Blood Disorder"/> Blood Disorder
									<input <?=in_array("Bronchial Asthma",$rec['previous_illness'])?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Bronchial Asthma"/> Bronchial Asthma
									<input <?=in_array("Diabetes Mellitus",$rec['previous_illness'])?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Diabetes Mellitus"/> Diabetes Mellitus
									<input <?=in_array("Hepatitis",$rec['previous_illness'])?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Hepatitis"/> Hepatitis
									<input <?=in_array("Hypertension",$rec['previous_illness'])?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Hypertension"/> Hypertension
									<input <?=in_array("Peptic Ulcer",$rec['previous_illness'])?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Peptic Ulcer"/> Peptic Ulcer
									<input <?=in_array("Tuberculosis",$rec['previous_illness'])?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Tuberculosis"/> Tuberculosis
									<input <?=in_array("Others",$rec['previous_illness'])?"checked":""?> type="checkbox" name="data[previous_illness][]" value="Others"/> Others
									<input type="text" name="data[previous_illness][others]" style="width:80px;" value="<?=$rec['previous_illness']['others']?>"/>
								</td>
							</tr>
							<tr>
								<td style="width:200px;">B. FAMILY HISTORY</td>
								<td>
									<input <?=in_array("Allergies",$rec['family_history'])?"checked":""?> type="checkbox" name="data[family_history][]" value="Allergies"/> Allergies
									<input <?=in_array("Blood Disorder",$rec['family_history'])?"checked":""?> type="checkbox" name="data[family_history][]" value="Blood Disorder"/> Blood Disorder
									<input <?=in_array("Bronchial Asthma",$rec['family_history'])?"checked":""?> type="checkbox" name="data[family_history][]" value="Bronchial Asthma"/> Bronchial Asthma
									<input <?=in_array("Diabetes Mellitus",$rec['family_history'])?"checked":""?> type="checkbox" name="data[family_history][]" value="Diabetes Mellitus"/> Diabetes Mellitus
									<input <?=in_array("Hepatitis",$rec['family_history'])?"checked":""?> type="checkbox" name="data[family_history][]" value="Hepatitis"/> Hepatitis
									<input <?=in_array("Hypertension",$rec['family_history'])?"checked":""?> type="checkbox" name="data[family_history][]" value="Hypertension"/> Hypertension
									<input <?=in_array("Peptic Ulcer",$rec['family_history'])?"checked":""?> type="checkbox" name="data[family_history][]" value="Peptic Ulcer"/> Peptic Ulcer
									<input <?=in_array("Tuberculosis",$rec['family_history'])?"checked":""?> type="checkbox" name="data[family_history][]" value="Tuberculosis"/> Tuberculosis
									<input <?=in_array("Others",$rec['family_history'])?"checked":""?>  type="checkbox" name="data[family_history][]" value="Others"/> Others
									<input value="<?=$rec['family_history']['others']?>" type="text" name="data[family_history][others]" style="width:80px;"/>
								</td>
							</tr>
							<tr>
								<td>C. HOSPITALIZATION / OPERATIONS:</td>
								<td><input value="<?=$rec['hospitalization']?>" type="text" name="data[hospitalization]"/></td>
							</tr>
							<tr>
								<td>D. PERSONAL HISTORY</td>
								<td>
									<fieldset style="width:40%;float:left;">
										<legend>Medication</legend>
										<input value="<?=$rec['personal_history']['medication']?>" type="text" name="data[personal_history][medication]"/>
									</fieldset>
									<fieldset style="width:40%;float:left;">
										<legend>Cardiovascular</legend>
										<input value="<?=$rec['personal_history']['cardiovascular']?>" type="text" name="data[personal_history][cardiovascular]"/>
									</fieldset>
									<fieldset style="width:40%;float:left;">
										<legend>Respiratory</legend>
										<input value="<?=$rec['personal_history']['respiratory']?>" type="text" name="data[personal_history][respiratory]"/>
									</fieldset>
									<fieldset style="width:40%;float:left;">
										<legend>Gastro-Intestinal</legend>
										<input value="<?=$rec['personal_history']['gastro-intestinal']?>" type="text" name="data[personal_history][gastro-intestinal]"/>
									</fieldset>
									<fieldset style="width:40%;float:left;">
										<legend>Genito-Urinary</legend>
										<input value="<?=$rec['personal_history']['genito-urinary']?>" type="text" name="data[personal_history][genito-urinary]"/>
									</fieldset>
									<fieldset style="width:40%;float:left;">
										<legend>Menstrual-Obstetrical</legend>
										<input value="<?=$rec['personal_history']['menstrual-obstetrical']?>" type="text" name="data[personal_history][menstrual-obstetrical]"/>
									</fieldset>
									<fieldset style="width:40%;float:left;">
										<legend>LMP</legend>
										<input value="<?=$rec['personal_history']['lmp']?>" type="text" name="data[personal_history][lmp]"/>
									</fieldset>
								</td>
							</tr>
							<tr>
								<td>Vital Sign</td>
								<td>
									Height:<input value="<?=$info['height']?>" type="text" name="info[height]"/>
									Weight:<input value="<?=$info['weight']?>" type="text" name="info[weight]"/>
								</td>
							</tr>
							<tr>
								<td>Blood Pressure</td>
								<td>
									<input value="<?=$rec['blood_pressure']['systolic']?>" type="text" name="data[blood_pressure][systolic]"/>
									<input value="<?=$rec['blood_pressure']['diastolic']?>" type="text" name="data[blood_pressure][diastolic]"/> mmHg
								</td>
							</tr>
							<tr>
								<td>Pulse Rate:</td>
								<td><input value="<?=$rec['pulse_rate']?>" type="text" name="data[pulse_rate]"/>bpm</td>
							</tr>
							<tr>
								<td>Respiratory Rate:</td>
								<td><input value="<?=$rec['respiratory_rate']?>" type="text" name="data[respiratory_rate]"/>cpm</td>
							</tr>
							<tr>
								<td>Temperature:</td>
								<td><input value="<?=$rec['temperature']?>" type="text" name="data[temperature]"/><sup>o</sup>c</td>
							</tr>
							<tr>
								<td>Smoker:</td>
								<td>
									<input <?=$rec['smoker']=="Yes"?"checked":""?> type="radio" name="data[smoker]" value="Yes"/> Yes
									<input <?=$rec['smoker']=="No"?"checked":""?> type="radio" name="data[smoker]" value="No"/> No
								</td>
							</tr>
							<tr>
								<td>Alcoholic Drinker:</td>
								<td>
									<input <?=$rec['alcoholic']=="Yes"?"checked":""?> type="radio" name="data[alcoholic]" value="Yes"/> Yes
									<input <?=$rec['alcoholic']=="No"?"checked":""?> type="radio" name="data[alcoholic]" value="No"/> No
								</td>
							</tr>
							<tr>
								<td style="width:200px;">VACCINATION</td>
								<td>
									<input <?=in_array("Flu",$rec['vaccination'])?"checked":""?> type="checkbox" name="data[vaccination][]" value="Flu"/> Flu
									<input value="<?=$rec['vaccination']['fludate']?>" type="text" name="data[vaccination][fludate]" style="width:80px;" />
									<input <?=in_array("HPV",$rec['vaccination'])?"checked":""?> type="checkbox" name="data[vaccination][]" value="HPV"/> HPV
									<input <?=in_array("HEPA",$rec['vaccination'])?"checked":""?> type="checkbox" name="data[vaccination][]" value="HEPA"/> HEPA
									<input <?=in_array("PCV13",$rec['vaccination'])?"checked":""?> type="checkbox" name="data[vaccination][]" value="PCV13"/> PCV13
									<input <?=in_array("PPSV23",$rec['vaccination'])?"checked":""?> type="checkbox" name="data[vaccination][]" value="PPSV23"/> PPSV23
									<input <?=in_array("Dengue",$rec['vaccination'])?"checked":""?> type="checkbox" name="data[vaccination][]" value="Dengue"/> Dengue
									<input <?=in_array("Others",$rec['vaccination'])?"checked":""?> type="checkbox" name="data[vaccination][]" value="Others"/> Others
								</td>
							</tr>
							<tr>
								<td>Visual Acuity:</td>
								<td>
									<input <?=$rec['visual_acuity']=="Yes"?"checked":""?> type="radio" name="data[visual_acuity]" value="Yes"/> Yes
									<input <?=$rec['visual_acuity']=="No"?"checked":""?> type="radio" name="data[visual_acuity]" value="No"/> No
								</td>
							</tr>
							<tr>
								<td>Visual Grade</td>
								<td>
									Right: <input value="<?=$rec['visual_grade']['right']?>" type="text" name="data[visual_grade][right]"/>
									Left: <input value="<?=$rec['visual_grade']['left']?>" type="text" name="data[visual_grade][left]"/>
								</td>
							</tr>
							<tr>
								<td>ISHIHARA TEST</td>
								<td>
									<input <?=$rec['ishihara']=="With Normal Color Vision"?"checked":""?> type="radio" name="data[ishihara]" value="With Normal Color Vision"/> With Normal Color Vision
									<input <?=$rec['ishihara']=="With Color Vision Deficiency"?"checked":""?> type="radio" name="data[ishihara]" value="With Color Vision Deficiency"/> With Color Vision Deficiency
								</td>
							</tr>
						</tbody>
					</table>
					<table class="navigateableMain" id="items_tbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<td></td>
								<td>Normal</td>
								<td>Findings</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>General Appearance</td>
								<td><input <?=in_array("Normal",$rec['asstd']['general_appearance'])?"checked":""?> type="checkbox" name="data[asstd][general_appearance][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['general_appearance'][0]=="Normal"?"":$rec['asstd']['general_appearance'][0]?>" type="text" name="data[asstd][general_appearance][]" /></td>
							</tr>
							<tr>
								<td>Skin</td>
								<td><input <?=in_array("Normal",$rec['asstd']['skin'])?"checked":""?> type="checkbox" name="data[asstd][skin][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['skin'][0]=="Normal"?"":$rec['asstd']['skin'][0]?>" type="text" name="data[asstd][skin][]" /></td>
							</tr>
							<tr>
								<td>Head and Scalp</td>
								<td><input <?=in_array("Normal",$rec['asstd']['headandscalp'])?"checked":""?> type="checkbox" name="data[asstd][headandscalp][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['headandscalp'][0]=="Normal"?"":$rec['asstd']['headandscalp'][0]?>" type="text" name="data[asstd][headandscalp][]" /></td>
							</tr>
							<tr>
								<td>Eyes, Pupils</td>
								<td><input <?=in_array("Normal",$rec['asstd']['eyespupils'])?"checked":""?> type="checkbox" name="data[asstd][eyespupils][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['eyespupils'][0]=="Normal"?"":$rec['asstd']['eyespupils'][0]?>" type="text" name="data[asstd][eyespupils][]" /></td>
							</tr>
							<tr>
								<td>Nose, Sinuses</td>
								<td><input <?=in_array("Normal",$rec['asstd']['nosesinuses'])?"checked":""?> type="checkbox" name="data[asstd][nosesinuses][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['nosesinuses'][0]=="Normal"?"":$rec['asstd']['nosesinuses'][0]?>" type="text" name="data[asstd][nosesinuses][]" /></td>
							</tr>
							<tr>
								<td>Mouth, Throat</td>
								<td><input <?=in_array("Normal",$rec['asstd']['mouththroat'])?"checked":""?> type="checkbox" name="data[asstd][mouththroat][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['mouththroat'][0]=="Normal"?"":$rec['asstd']['mouththroat'][0]?>" type="text" name="data[asstd][mouththroat][]" /></td>
							</tr>
							<tr>
								<td>Neck, Thyroid</td>
								<td><input <?=in_array("Normal",$rec['asstd']['neckthyroid'])?"checked":""?> type="checkbox" name="data[asstd][neckthyroid][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['neckthyroid'][0]=="Normal"?"":$rec['asstd']['neckthyroid'][0]?>" type="text" name="data[asstd][neckthyroid][]" /></td>
							</tr>
							<tr>
								<td>Chest, Breast and Axilla</td>
								<td><input <?=in_array("Normal",$rec['asstd']['chestbreastandaxilla'])?"checked":""?> type="checkbox" name="data[asstd][chestbreastandaxilla][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['chestbreastandaxilla'][0]=="Normal"?"":$rec['asstd']['chestbreastandaxilla'][0]?>" type="text" name="data[asstd][chestbreastandaxilla][]" /></td>
							</tr>
							<tr>
								<td>Heart-Cardiovascular</td>
								<td><input <?=in_array("Normal",$rec['asstd']['heartcardiovascular'])?"checked":""?> type="checkbox" name="data[asstd][heartcardiovascular][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['heartcardiovascular'][0]=="Normal"?"":$rec['asstd']['heartcardiovascular'][0]?>" type="text" name="data[asstd][heartcardiovascular][]" /></td>
							</tr>
							<tr>
								<td>Lung-Respiratory</td>
								<td><input <?=in_array("Normal",$rec['asstd']['lungrespiratory'])?"checked":""?> type="checkbox" name="data[asstd][lungrespiratory][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['lungrespiratory'][0]=="Normal"?"":$rec['asstd']['lungrespiratory'][0]?>" type="text" name="data[asstd][lungrespiratory][]" /></td>
							</tr>
							<tr>
								<td>Abdomen</td>
								<td><input <?=in_array("Normal",$rec['asstd']['abdomen'])?"checked":""?> type="checkbox" name="data[asstd][abdomen][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['abdomen'][0]=="Normal"?"":$rec['asstd']['abdomen'][0]?>" type="text" name="data[asstd][abdomen][]" /></td>
							</tr>
							<tr>
								<td>Backs, Flanks</td>
								<td><input <?=in_array("Normal",$rec['asstd']['backsflanks'])?"checked":""?> type="checkbox" name="data[asstd][backsflanks][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['backsflanks'][0]=="Normal"?"":$rec['asstd']['backsflanks'][0]?>" type="text" name="data[asstd][backsflanks][]" /></td>
							</tr>
							<tr>
								<td>Anus, Rectum</td>
								<td><input <?=in_array("Normal",$rec['asstd']['anusrectum'])?"checked":""?> type="checkbox" name="data[asstd][anusrectum][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['anusrectum'][0]=="Normal"?"":$rec['asstd']['anusrectum'][0]?>" type="text" name="data[asstd][anusrectum][]" /></td>
							</tr>
							<tr>
								<td>Genito-Urinary System</td>
								<td><input <?=in_array("Normal",$rec['asstd']['genitourinarysystem'])?"checked":""?> type="checkbox" name="data[asstd][genitourinarysystem][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['genitourinarysystem'][0]=="Normal"?"":$rec['asstd']['genitourinarysystem'][0]?>" type="text" name="data[asstd][genitourinarysystem][]" /></td>
							</tr>
							<tr>
								<td>Inguinal, Genitals</td>
								<td><input <?=in_array("Normal",$rec['asstd']['inguinalgenitals'])?"checked":""?> type="checkbox" name="data[asstd][inguinalgenitals][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['inguinalgenitals'][0]=="Normal"?"":$rec['asstd']['inguinalgenitals'][0]?>" type="text" name="data[asstd][inguinalgenitals][]" /></td>
							</tr>
							<tr>
								<td>Musculo-Skeletal</td>
								<td><input <?=in_array("Normal",$rec['asstd']['musculoskeletal'])?"checked":""?> type="checkbox" name="data[asstd][musculoskeletal][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['musculoskeletal'][0]=="Normal"?"":$rec['asstd']['musculoskeletal'][0]?>" type="text" name="data[asstd][musculoskeletal][]" /></td>
							</tr>
							<tr>
								<td>Extremities</td>
								<td><input <?=in_array("Normal",$rec['asstd']['extremities'])?"checked":""?> type="checkbox" name="data[asstd][extremities][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['extremities'][0]=="Normal"?"":$rec['asstd']['extremities'][0]?>" type="text" name="data[asstd][extremities][]" /></td>
							</tr>
							<tr>
								<td>Reflexes</td>
								<td><input <?=in_array("Normal",$rec['asstd']['reflexes'])?"checked":""?> type="checkbox" name="data[asstd][reflexes][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['reflexes'][0]=="Normal"?"":$rec['asstd']['reflexes'][0]?>" type="text" name="data[asstd][reflexes][]" /></td>
							</tr>
							<tr>
								<td>Neurological-Nervous</td>
								<td><input <?=in_array("Normal",$rec['asstd']['neurologicalnervous'])?"checked":""?> type="checkbox" name="data[asstd][neurologicalnervous][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['neurologicalnervous'][0]=="Normal"?"":$rec['asstd']['neurologicalnervous'][0]?>" type="text" name="data[asstd][neurologicalnervous][]" /></td>
							</tr>
							<tr>
								<td>Ears,Eardrum</td>
								<td><input <?=in_array("Normal",$rec['asstd']['ears-eardrum'])?"checked":""?> type="checkbox" name="data[asstd][ears-eardrum][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['ears-eardrum'][0]=="Normal"?"":$rec['asstd']['ears-eardrum'][0]?>" type="text" name="data[asstd][ears-eardrum][]" /></td>
							</tr>
							<tr>
								<td>Remarks/Recommendation</td>
								<td><input <?=in_array("Normal",$rec['asstd']['recommendation'])?"checked":""?> type="checkbox" name="data[asstd][recommendation][]" value="Normal"/></td>
								<td><input value="<?=$rec['asstd']['recommendation'][0]=="Normal"?"":$rec['asstd']['recommendation'][0]?>" type="text" name="data[asstd][recommendation][]" /></td>
							</tr>
						</tbody>
					</table>
				</fieldset>
				
			</div>
		</div>
	</form>
</div>
<script>
$(document).ready(function() {
	<?php if(!$rec){ ?>
	$("form input:checkbox").attr( "checked" , false );
	$("form input[name*='asstd']").attr( "checked" , true );
	<?php } ?>
});
$("#items_tbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		del();
	}
});
function del(){
	$('input[type="checkbox"]:checked').closest("tr").remove();
}
</script>
<?php } ?>