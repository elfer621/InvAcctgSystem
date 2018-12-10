<?php
$product_name="";
$part_number="";
$brand="";
$where="";
if($_POST){
	$product_name=!empty($_REQUEST['product_name'])?"and (product_name like '%{$_REQUEST['product_name']}%' or `class` like '%{$_REQUEST['product_name']}%')":"";
	$part_number=!empty($_REQUEST['part_number'])?"and parts_number like '%{$_REQUEST['part_number']}%'":"";
	$brand=!empty($_REQUEST['brand'])?"and brand like '%{$_REQUEST['brand']}%'":"";
}
$sql="select * from tbl_product_name where product_name!='' $product_name $part_number $brand";
$qry = mysql_query($sql);
?>
<div style="clear:both;height:10px;"></div>
<div class="container-fluid">
 <div class="row">
	<fieldset style="width:95%;padding:25px;box-shadow:2px 2px 2px 2px gray;margin:0 auto;">
		<legend style="background-color:#fff;width:85px;">Search:</legend>
		<form method="post">
			<div class="col-xl-3 col-md-3">
				<input value="<?=!empty($_REQUEST['product_name'])?$_REQUEST['product_name']:""?>" class="form-control" type="text" name="product_name" style="width:100%;" placeholder="Product Name"/>
			</div>
			<div class="col-xl-3 col-md-3">
				<input value="<?=!empty($_REQUEST['part_number'])?$_REQUEST['part_number']:""?>" class="form-control" type="text" name="part_number" style="width:100%;" placeholder="Part Number"/>
			</div>
			<div class="col-xl-3 col-md-3">
				<input value="<?=!empty($_REQUEST['brand'])?$_REQUEST['brand']:""?>" class="form-control" type="text" name="brand" style="width:100%;" placeholder="Brand"/>
			</div>
			<div class="col-xl-3 col-md-3">
				<button type="submit" class="btn btn-primary" style="width:100%;">Execute</button>
			</div>
		</form>
	</fieldset>
 	<div style="width:100%;margin:10px auto;">
		<div class="col-xl-12">
			<?php while($row=mysql_fetch_assoc($qry)){ ?>
				<div class="col-xl-3 col-md-3 boxes">
					<img src="./images/no-image.jpg" class="img-responsive" style="width:150px;height:150px;"/>
					<div class="lbl"><?=$row['parts_number']?></div>
					<h6><?= $row['product_name']?></h6>
					<div class="lbl"><?=$row['brand']?></div>
					<div class="lbl"><?=$row['class']?></div>
				</div>
			<?php } ?>
		</div>
	</div>
 </div>
</div>
<style>
[class*="col-"] {
    float:left;
	text-align:center;
	
 }	
 .main-cont{
	padding-left:150px;
 }
 .boxes{
	min-height:325px !important;
	background-color:#fff;
	box-shadow: 1px 1px 1px gray;
	margin:5px;
	max-width:230px !important;
 }
</style>
<link rel="stylesheet" href="./js/bootstrap/css/bootstrap.min.css" >
<script src="./js/bootstrap/js/bootstrap.min.js" ></script>