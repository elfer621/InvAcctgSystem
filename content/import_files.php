<?php
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);
require_once './class/PHPExcel/Classes/PHPExcel/IOFactory.php';
//print_r($_POST);
if(isset($_POST["upload"])){
	//print_r($_POST["upload"]);
	if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
		$file=$_FILES['userfile']['tmp_name'];
		$objPHPExcel = PHPExcel_IOFactory::load($file);
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		$text=$sheetData;
		$count=1;
		for($i=1;$i<=count($text);$i++){ //count($text)
			if($i>($count*5000)){
				$count++;
			}
			if($i==1){
				if($text[1]['B']!=""){
					$col = $text[1];
					$sql_header = "insert into {$_REQUEST['data_type']} (`data_reference`,`company_name`,`".implode("`,`",array_map('mysql_real_escape_string', $col))."`) values ";
				}
			}elseif($i==2){
				if($text[1]['B']==""){
					$col = $text[2];
					$sql_header = "insert into {$_REQUEST['data_type']} (`data_reference`,`company_name`,`".implode("`,`",array_map('mysql_real_escape_string', $col))."`) values ";
				}else{
					$sql_content[$count][] = "('{$_REQUEST['data_reference']}','{$_REQUEST['company_name']}','".implode("','",array_map('mysql_real_escape_string', $text[$i]))."')";
				}
			}else{
				$sql_content[$count][] = "('{$_REQUEST['data_reference']}','{$_REQUEST['company_name']}','".implode("','",array_map('mysql_real_escape_string', $text[$i]))."')";
			}
		}
		// $batch=1;
		// foreach($sql_content as $key => $val){
			// $sql = $sql_header.implode(",",$val).";";
			// echo "Batch $batch:".$con->uploadData($sql)."<br/>";
			// $batch++;
		// }
		
		
		$sql = $sql_header.implode(",",$sql_content[1]).";";
		$qry=mysql_query($sql);
		if(!$qry){
			echo mysql_error();
		}else{
			echo "<h2>Successfully Save...</h2>";
		}
	}else{
		echo "Error Uploading...";
	}
	//echo $sql;
	
	// echo "<pre>";
	// print_r($sql_content);
	// echo "</pre>";
	
	// echo "<textarea style='width:100%;height:400px;'>";
	// echo $sql;
	// echo "</textarea>";
	//echo $con->uploadData($sql);
}


?>
<style type="text/css">
	table.tbl,table.tbl2 {
		border-width: 0px;
		border-spacing: 0px;
		border-style: none;
		border-collapse: collapse;
		font-family:Verdana, Geneva, Arial, Helvetica, Sans-Serif;
		
	}
	table.tbl th,table.tbl2 th {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		height:20px;
		text-align:center;
	}
	table.tbl td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
	}
	table.tbl2 td {
		border-width: 1px;
		border-style: solid;
		border-color: gray;
		background-color: white;
		height:20px;
		text-align:center;
	}
	.lbl{
		float:left;margin-right:10px;width:120px;
	}
</style>
<fieldset>
<legend>UPLOAD DATA</legend>
<form name="myForm" method="post" enctype="multipart/form-data" onsubmit="return val()">
	<div class="form-group">
		<div style="float:left;margin-right:10px;">Data Type</div>
		<div style="float:left;">
			<select id="data_type" name="data_type" style="width:250px;" class="form-control">
				<?php if($_SESSION['settings']['system_name']=="CSACCI"){ ?>
				<option value="data_teletech_patient">Data Table</option>
				<?php }else{ ?>
				<option value="">SELECT FILES</option>
				<option value="data_college_ucmain">UC-Main (College)</option>
				<option value="data_highsch_ucmain">UC-Main (HighSch)</option>
				<option value="data_elem_ucmain">UC-Main (Elem)</option>
				
				<option value="data_college_ucbanilad">UC-Banilad (College)</option>
				<option value="data_highsch_ucbanilad">UC-Banilad (HighSch)</option>
				<option value="data_elem_ucbanilad">UC-Banilad (Elem)</option>
				
				<option value="data_college_uclm">UC-LM (College)</option>
				<option value="data_highsch_uclm">UC-LM (HighSch)</option>
				<option value="data_elem_uclm">UC-LM (Elem)</option>
				
				<option value="data_college_ucmambaling">UC-Mambaling (College)</option>
				<option value="data_highsch_ucmambaling">UC-Mambaling (HighSch)</option>
				<option value="data_elem_ucmambaling">UC-Mambaling (Elem)</option>
				<?php } ?>
			</select>
		</div>
		<div style="float:left;width:300px;margin-left:10px;">
			<div style="width:130px;float:left;">Data Reference</div>
			<input readonly type="text" name="data_reference" style="float:left;width:150px;" value="<?=date('Y-m')?>"/>
		</div>
		<div style="float:left;width:300px;margin-left:10px;">
			<div style="width:130px;float:left;">Company Name</div>
			<input type="text" name="company_name" style="float:left;width:150px;"/>
		</div>
	</div>
	<div style="clear:both;height:10px;"></div>
	<div style="float:left;">
		<input type="file" id="userfile" name="userfile" />
	</div>
	<div style="clear:both;height:10px;"></div>
	<input type="submit" id="upload" name="upload" value="Upload" style="float:left;height:30px;width:100px;"/>
	<input type="button" value="Sample" onclick="ExportToExcel('tbl');" style="float:left;height:30px;width:100px;"/>
</form>
<div style="clear:both;height:10px;"></div>
<?php
// $sql="SHOW columns FROM data_teletech_patient";
// $res = $con->resultArray($con->Nection()->query($sql));
// echo '<table class="tbl" cellspacing="0" cellpadding="0" width="100%"><tr>';
// foreach($res as $key => $row){
	// echo "<td>{$row['Field']}</td>";
// }
// echo '<tr></table>';
?>
<table class="tbl" id="tbl" cellspacing="0" cellpadding="0" width="100%">
	<tr>
		<td>employee_no</td>
		<td>first_name</td>
		<td>last_name</td>
		<td>middle_name</td>
		<td>gender</td>
		<td>date_of_birth</td>
		<td>age</td>
		<td>hmo_policy_no</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
</table>
</fieldset>
<script>
function val(){

     var frm = document.forms["myForm"];
     if(frm.data_reference.value == ""){
         alert("Data Reference is required");
         return false;
     }
	 else if(frm.company_name.value == ""){
         alert("Company Name is required");
         return false;
     }
     else{
         return true;
     }
}
</script>
