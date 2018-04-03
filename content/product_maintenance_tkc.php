<?php
//$_SESSION['connect']="main";
//$db->openDb("main");
if($auto_barcode){
	$code = $db->genSKU(8);
}
if($_POST['save_itemprod']){
	// echo "<pre>";
	// print_r($_POST);
	// echo "</pre>";
	$skuid = ($_REQUEST['sku_id']==""?$db->genSKU(8):$_REQUEST['sku_id']);
	//$subj=explode("|",$_REQUEST['subject_name']);
	$subj[0]=$_REQUEST['subject_name'];
	$subj[1]=$_REQUEST['subject_name'];
	
	$imgData =addslashes(file_get_contents($_FILES['fileImg']['tmp_name']));
	$imageProperties = getimageSize($_FILES['fileImg']['tmp_name']);
	
	mysql_query("BEGIN");
	$insert_prod="insert into tbl_product_name (sku_id,product_name,base_unit,supplier_id,category_id,manufacturer_id,
			school_level,subject_name,subjtype,tax_type,hidden,
			imageData,brand,inventory_code,location,class,parts_number) values 
			('".$skuid."','".mysql_real_escape_string($_REQUEST['product_name'])."','".$_REQUEST['unit']."','".$_REQUEST['supplier_name']."','".$_REQUEST['category']."','".$_REQUEST['manufacturer_name']."','".
			$_REQUEST['sch_level']."','".$subj[0]."','".$subj[1]."','{$_REQUEST['tax_type']}','{$_REQUEST['hide']}',
			'{$imgData}','{$_REQUEST['brand']}','{$_REQUEST['inventory_code']}','{$_REQUEST['location']}','{$_REQUEST['class']}','{$_REQUEST['parts_number']}') 
			on duplicate key update hidden=values(hidden),product_name=values(product_name),base_unit=values(base_unit),supplier_id=values(supplier_id),
			category_id=values(category_id),manufacturer_id=values(manufacturer_id),school_level=values(school_level),
			subject_name=values(subject_name),subjtype=values(subjtype),tax_type=values(tax_type),
			imageData=values(imageData),brand=values(brand),inventory_code=values(inventory_code),location=values(location),class=values(class),parts_number=values(parts_number)";
	$qry_prod = mysql_query($insert_prod);
	if($qry_prod){
		$qry_bcode=mysql_query("insert into tbl_barcodes (sku_id,barcode,price,price_gov,cost,unit) values 
			('".$skuid."','".$_REQUEST['xbarcode']."','".$_REQUEST['price']."','{$_REQUEST['price_gov']}','".$_REQUEST['cost']."','".$_REQUEST['unit']."') 
			on duplicate key update barcode=values(barcode),price=values(price),price_gov=values(price_gov),cost=values(cost),unit=values(unit)");
		if(!$qry_bcode){
			$_SESSION['error'] = mysql_error();
		}else{
			
			if($_SESSION['connect']=='warehouse'){
				echo "ProductName:".$con->updateMainTbl("tbl_product_name")."\n";
				echo "Barcode:".$con->updateMainTbl("tbl_barcodes")."\n";
			}
		}
	}else{
		$_SESSION['error'] =  mysql_error();
	}
	if($_REQUEST['uom_prebuilt']){
		$uom = explode('|',$_REQUEST['uom_prebuilt']);
		$divmuls = explode(',',$uom[0]);
		$units = explode(',',$uom[1]);
		foreach($units as $key => $val){
			$qry_bcode=mysql_query("insert into tbl_barcodes set 
				sku_id='".$skuid."',
				barcode='".$skuid."_".$val."',
				price='".($_REQUEST['price']*$divmuls[$key])."',
				cost='".($_REQUEST['cost']*$divmuls[$key])."',
				divmul='".$divmuls[$key]."',
				unit='".$val."'");
			if(!$qry_bcode){
				$_SESSION['error'] =  mysql_error();
			}
		}
	}
	if($qry_prod && $qry_bcode){
		mysql_query("COMMIT");
	}else{
		mysql_query("ROLLBACK");
		exit;
	}
}
$info = $db->getWHERE("a.*,b.*","tbl_barcodes a left join tbl_product_name b on a.sku_id=b.sku_id","where a.barcode='{$_REQUEST['bcodeinfo']}'");
// echo "<pre>";
// print_r($info);
// echo "</pre>";
?>
<style>
#camera {
  width: 100%;
  height: 350px;
}
</style>
<!--script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script-->
<script src="./webcam/jpeg_camera/jpeg_camera_with_dependencies.min.js" type="text/javascript"></script>
<div style="height:500px;overflow:auto;background-color:white;">
	<div id="tabs" style="min-height:450px;">
		<ul>
			<li><a href="#tabs-1">Info</a></li>
			<li><a href="#tabs-2">UOM &amp; Pricing</a></li>
			<li><a href="#tabs-3" onclick="loadtrans()">Transaction</a></li>
		</ul>
			<div id="tabs-1">
				<form name="itemprod" method="post" onsubmit="return validate();" enctype="multipart/form-data">
					<div style="float:left;">
						<div style="float:left;margin-right:10px;width:100px;">Brand</div>
						<input value="<?=$info?$info['brand']:""?>" type="text" name="brand" id="brand" style="width:560px;" />
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Description</div>
						<input value="<?=$info?$info['product_name']:""?>" type="text" name="product_name" id="product_name" style="width:560px;" />
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Inventory Code</div>
						<input value="<?=$info?$info['inventory_code']:""?>" type="text" name="inventory_code" id="inventory_code" style="width:560px;" />
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Location</div>
						<input value="<?=$info?$info['location']:""?>" type="text" name="location" id="location" style="width:560px;" />
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Class</div>
						<input value="<?=$info?$info['class']:""?>" type="text" name="class" id="class" style="width:560px;" />
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:10px;width:100px;">Parts Number</div>
						<input value="<?=$info?$info['parts_number']:""?>" type="text" name="parts_number" id="parts_number" style="width:560px;" />
						<div style="clear:both;height:5px;"></div>
						<div style="float:left;margin-right:20px;">
							<div style="float:left;margin-right:10px;width:100px;">SKU ID</div>
							<input readonly type="text" name="sku_id" id="sku_id" value="<?=$info?$info['sku_id']:$code?>"  />
							<div style="clear:both;height:5px;"></div>
							<div id="subinfo">
								<div style="float:left;margin-right:10px;width:100px;">Barcode</div>
								<input type="text" name="xbarcode" id="xbarcode" value="<?=$info?$info['barcode']:$code?>" />
								<div style="clear:both;height:5px;"></div>
								<div style="clear:both;height:5px;"></div>
								<div style="float:left;margin-right:10px;width:100px;">Private Selling</div>
								<input type="text" name="price" id="price" value="<?=$info?$info['price']:""?>" />
								<div style="clear:both;height:5px;"></div>
								<div style="float:left;margin-right:10px;width:100px;">Gov't Selling</div>
								<input type="text" name="price_gov" id="price_gov" value="<?=$info?$info['price_gov']:""?>" />
								<div style="clear:both;height:5px;"></div>
								<div style="float:left;margin-right:10px;width:100px;">Cost</div>
								<input type="text" name="cost" id="cost" value="<?=$info?$info['cost']:""?>" />
								<div style="clear:both;height:5px;"></div>
								<div style="float:left;margin-right:10px;width:100px;">Base UOM</div>
								<input type="text" name="unit" id="unit" value="<?=$info?$info['unit']:"pcs"?>" />
								<div style="clear:both;height:5px;"></div>
							</div>
							<div style="float:left;margin-right:10px;width:100px;">ReOrder Level</div>
							<input type="text" name="reorder_level" id="reorder_level" value="30" />
							<div style="clear:both;height:5px;"></div>
							<input type="hidden" id="buom"/>
						</div>
						<div style="float:left;">
							<div style="float:left;margin-right:10px;width:100px;">Supplier</div>
							<select name="supplier_name" id="supplier_name" style="float:left;width:230px;">
								<option value="">Select Supplier</option>
								<?php $db->getSupplier(); ?>
							</select>
							<div style="clear:both;height:5px;"></div>
							<div style="float:left;margin-right:10px;width:100px;">Manufacturer</div>
							<select name="manufacturer_name" id="manufacturer_name" style="float:left;width:230px;">
								<option value="">Select Manufacturer</option>
								<?php $db->getManufacturer(); ?>
							</select>
							<!--div style="clear:both;height:5px;"></div>
							<div style="float:left;margin-right:10px;width:100px;">Pre-Built UOM</div>
							<select name="uom_prebuilt" id="uom_prebuilt" style="float:left;width:230px;">
								<option value="">Select Pre-Built UOM</option>
								<option value="1,0.02|sks,kls">50kls per Sack</option>
								<option value="1,0.020408163|sks,kls">49kls per Sack</option>
							</select-->
							<div style="clear:both;height:5px;"></div>
							<div style="float:left;margin-right:10px;width:100px;">Category</div>
							<select name="category" id="category" style="float:left;width:230px;">
								<option value="">Select Category</option>
								<?php $db->getCategory(); ?>
							</select>
							<div style="clear:both;height:20px;"></div>
							<!--div style="visibility: hidden;">
								<div style="float:left;margin-right:10px;width:100px;">School Level</div>
								<select name="sch_level" id="sch_level" style="float:left;width:230px;">
									<option value="">Select School Level</option>
									<option value="College">College</option>
									<option value="SenHigh">SenHigh</option>
									<option value="HighSchool">HighSchool</option>
									<option value="Elementary">Elementary</option>
								</select>
								<div style="clear:both;height:5px;"></div>
								<div style="float:left;margin-right:10px;width:100px;">SubjectName</div>
								<select name="subject_name" id="subject_name" style="float:left;width:230px;">
									<option value="">Select Subject Name</option>
								</select>
							</div-->
							<div style="clear:both;height:5px;"></div>
							<!--div style="float:left;margin-right:10px;width:100px;">TeacherName</div>
							<select name="teacher_name" id="teacher_name" style="float:left;width:230px;">
								<option value="">Select Subject Name</option>
							</select>
							<div style="clear:both;height:5px;"></div-->
							<div style="float:left;margin-right:10px;width:100px;">Tax Type</div>
							<select name="tax_type" id="tax_type" style="float:left;width:230px;">
								<option value="1">VAT-Exempt</option>
								<option value="2">Non-Vat</option>
								<option value="3">Vat-Sales</option>
							</select>
							<div style="clear:both;height:5px;"></div>
							<div style="float:left;margin-right:10px;width:100px;">Hide</div>
							<select name="hide" id="hide" style="float:left;width:230px;">
								<option value="">False</option>
								<option value="True">True</option>
							</select>
						</div>
						<div style="clear:both;height:5px;"></div>
						<?php if($mode=="main"||$_SESSION['connect']=='warehouse'){?>
							<input type="submit" value="Save" name="save_itemprod" style="margin-top:10px;width:150px;height:30px;"/>
						<?php } ?>
					</div>
					<div style="float:left;padding:10px;margin-right:20px;">
						<?php 
						if($info['imageData']){
							echo '<img class="img-responsive" style="border:1px solid gray;width:200px;" src="data:image/jpeg;base64,'.base64_encode( $info['imageData'] ).'"/>'; 
						}else{
							echo '<img class="img-responsive" style="border:1px solid gray;width:200px;" src="./images/no-photo.jpg"/>'; 
						}
						?>
						<br/>
						<input type="file" name="fileImg" id="fileImg"/>
						<div id="camera_info"></div>
						<button id="take_snapshots" class="btn btn-success btn-sm">Take Snapshots</button>
						<div id="camera"></div><br>
					</div>
					
				</form>
			</div>
			<div id="tabs-2">
				<table id="tbluom" cellspacing="0" cellpadding="3" border="1">
					<tr>
						<td>Barcode</td>
						<td>Price</td>
						<td>Cost</td>
						<td colspan="2">per BaseUOM</td>
						<td colspan="2">UOM</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
				</table>
			</div>
			<div id="tabs-3" style="height:320px;"></div>
	</div>
</div>
<div style="clear:both;"></div>
<div style="background-color:white;padding:10px;">
	<fieldset>
		<legend>Scan Barcode / Press Enter</legend>
		<input onkeypress="return runScript(event,this)" type="text" name="barcode" id="barcode" style="float:left;height:25px;width:98%"/>
	</fieldset>
	<div style="clear:both;height:10px;"></div>
</div>
<style>
#tbluom input[type=text]{
	width:80px;
	text-align:right;
}
#tabs-1 input[type=text]{
	float:left;
	width:200px;
}
.ui-tabs{
	border-bottom:none;
}
</style>
<script>
var options = {
      shutter_ogg_url: "jpeg_camera/shutter.ogg",
      shutter_mp3_url: "jpeg_camera/shutter.mp3",
      swf_url: "jpeg_camera/jpeg_camera.swf",
    };
    var camera = new JpegCamera("#camera", options);
  
$('#take_snapshots').click(function(){
    var snapshot = camera.capture();
    snapshot.show();
    
    snapshot.upload({api_url: "action.php"}).done(function(response) {
	$('#imagelist').prepend("<tr><td><img src='"+response+"' width='100px' height='100px'></td><td>"+response+"</td></tr>");
	}).fail(function(response) {
	  alert("Upload failed with status " + response);
	});
});

function done(){
    $('#snapshots').html("uploaded");
}

$(document).ready(function() {
	//getSubject('all');
	//getTeachername('all');
	var sku = '<?php echo $_REQUEST['sku']?>';
	var bcodeinfo = '<?php echo $_REQUEST['bcodeinfo']?>';
	if(sku !='' || bcodeinfo!=''){
		getBarcodeInfo(bcodeinfo,sku);
	}
});

$(function(){
	$('#tabs').tabs();
});

function loadtrans(){
	var sku_id = $("#sku_id").val();
	var buom = $("#buom").val();
	$.ajax({
		url: './content/pos_ajax.php?execute=prodtrans',
		type:"POST",
		data: {sku_id:sku_id,buom:buom},
		success:function(data){
			$("#tabs-3").html(data);
		}
	});
}
$("#category").change(function(){
	//console.log($(this).val());
	if($(this).val()!=1){
		$("#tax_type").val(3);
	}else{
		$("#tax_type").val(1);
	}
});

$("#sch_level").change(function(){
	//getSubject($(this).val());
});
function getTeachername(schlevel){
	$("#subject_name").html("");
	$.getJSON( './content/pos_ajax.php?execute=getTeachername&schlevel='+schlevel, function( data ) {
		$("#subject_name").append("<option value=''>Select SubjectName</option>");
		$.each( data.tbl_rec, function( key, val ) {
			$("#teacher_name").append(
		"<option value='"+val.InstructorNo+"'>"+"["+val.InstructorNo+"] "+val.name+"</option>"
			);
		});
	});
}
function getSubject(schlevel){
	$("#subject_name").html("");
	$.getJSON( './content/pos_ajax.php?execute=getSubject&schlevel='+schlevel, function( data ) {
		$("#subject_name").append("<option value=''>Select SubjectName</option>");
		$.each( data.tbl_rec, function( key, val ) {
			$("#subject_name").append(
				"<option value='"+val.code+"'>"+val.subject_name+"</option>"
			);
		});
	});
}
function validate(){
	var frm = document.itemprod;
	if(frm.xbarcode.value==""){
		alert("Barcode is required...");
		return false;
	}
	if(frm.product_name.value==""){
		alert("Description is required...");
		return false;
	}
	if(frm.price.value==""){
		alert("Price is required...");
		return false;
	}
	if(frm.cost.value==""){
		alert("Cost is required...");
		return false;
	}
	if(frm.unit.value==""){
		alert("Base UOM is required...");
		return false;
	}
	if(frm.category.value==""){
		alert("Category is required...");
		return false;
	}
	return true;
}
</script>