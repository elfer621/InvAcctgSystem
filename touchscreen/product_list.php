<?php
/*error_reporting(0);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$con = $db->openDb();
if(!$con){
	$db->openDb_local();
}
$readingNum = $db->getReadingnum($_SESSION['counter_num']);*/
$cat = $db->resultArray("*","tbl_category","order by category_name asc");
?>
<style>
td{
padding:0 !important;
}
</style>
<div class="top">
	<div class="content" style="min-height:150px;">
		<div style="float:left;width:300px;height:500px;overflow:auto;" id="content">
			<fieldset style="padding:10px;min-height:500px;">
				<legend>&nbsp; CATEGORY &nbsp;</legend>
				<table class="tbl">
				<?php 
					$i=0;
					foreach($cat as $key => $val){
						$btn = '<button id="bt19" class="buthov" type="button" onclick="showProd('.$val['category_id'].')" style="height:50px;width:100%;float:left;font-size:11px;">'.$val['category_name'].'</button>';
						$tbl.=$i%2==0?"<tr><td>$btn</td>":"<td>$btn</td></tr>";
						$i++;
					}
					echo $tbl;
				?>
				</table>
			</fieldset>
		</div>
		<div style="float:right;width:680px;">
			<div id="prodarea" style="height:450px;overflow:auto;width:60%;float:left;">
			</div>
			<div style="height:450px;width:40%;float:left;height:450px;font-size:10px;">
				<div style="overflow:auto;height:400px;">
					<table class="navigateableMain" id="mytbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<th style="border:none;">Bcode</th>
								<th style="border:none;">Desc</th>
								<th style="border:none;">Price</th>
								<th style="border:none;">Qty</th>
								<th style="border:none;">Amount</th>
							</tr>
						</thead>
						<tbody>
							<?php if(isset($_SESSION['sales'])){ $change="";$xtotal=0;?>
								<?php $count=1; foreach($db->subval_sort($_SESSION['sales'],'count',arsort) as $val){ ?>
									<tr>
										<td><a href="javascript:backToBarcode();" class="activationMain"><?php echo $val['bcode']; ?></a></td>
										<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
										<td style="text-align:right;"><?php echo number_format($val['price'],2) ?></td>
										<td style="text-align:right;"><?php echo $val['qty'] ." ". $val['unit'] ?></td>
										<td  style="text-align:right;"><?php echo number_format($val['total'],2) ?></td>
										<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
										<td  style="text-align:right;display:none;"><?php echo $val['cost'] ?></td>
									</tr>
								<?php $xtotal+=$val['total'];$count++;} ?>
							<?php } ?>
						</tbody>
						
					</table>
				</div>
				<div style="clear:both;height:5px;"></div>
					<table class="tbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<th colspan="3">Total</th>
								<th colspan="2" style="font-size:15px;text-align:right;color:red;"><?=number_format($xtotal,2)?></th>
							</tr>
						</thead>
					</table>
			</div>
			<div style="clear:both;height:5px;"></div>
			<div style="width:100%;">
				<fieldset style="float:left;">
					<legend>Menu:</legend>
					<input id="bt6" class="buthov" type="button" value="Close" onclick="xclose();" style="height:40px;width:100px;float:left;font-size:11px;"/>
					<input id="bt20" class="buthov" type="button" value="Up"  style="height:40px;width:100px;float:left;font-size:11px;"/>
					<input id="bt21" class="buthov" type="button" value="Down"  style="height:40px;width:100px;float:left;font-size:11px;"/>
				</fieldset>
			</div>
		</div>
		<div style="clear:both;height:10px;"></div>
	</div>
</div>
<script>
var step = 25;
var scrolling = false;

// Wire up events for the 'scrollUp' link:
$("#bt20").bind("click", function(event) {
    event.preventDefault();
    // Animates the scrollTop property by the specified
    // step.
    $("#content").animate({
        scrollTop: "-=" + step + "px"
    });
});/*.bind("mouseover", function(event) {
    scrolling = true;
    scrollContent("up");
}).bind("mouseout", function(event) {
    // Cancel scrolling continuously:
    scrolling = false;
});*/


$("#bt21").bind("click", function(event) {
    event.preventDefault();
    $("#content").animate({
        scrollTop: "+=" + step + "px"
    });
});/*.bind("mouseover", function(event) {
    scrolling = true;
    scrollContent("down");
}).bind("mouseout", function(event) {
    scrolling = false;
});*/

function scrollContent(direction) {
    var amount = (direction === "up" ? "-=1px" : "+=1px");
    $("#content").animate({
        scrollTop: amount
    }, 1, function() {
        if (scrolling) {
            // If we want to keep scrolling, call the scrollContent function again:
            scrollContent(direction);
        }
    });
}

$(document).ready(function() {
	jQuery.tableNavigationMain();
	return false;
});
$("#mytbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		delProdsale($("tr.selected").find('td:eq(0)').text());
	}else if(chCode==113){ //pressing f2
		qtyclick($("tr.selected").find('td:eq(0)').text());
	}else if(chCode==119){ //pressing f8
		priceclick($("tr.selected").find('td:eq(0)').text());
	}else if(chCode==115){ //pressing f4
		uomlist($("tr.selected").find('td:eq(0)').text());
		jQuery.tableNavigationUom();
	}
});
function xclose(){
	window.close();
	window.opener.backToBarcode();
}
function showProd(catid){
	htmlobj=$.ajax({url:'./touchscreen/touchscreen_ajax.php?execute=prodlist&catid='+catid,async:false});
	$('#prodarea').html(htmlobj.responseText);
}
</script>