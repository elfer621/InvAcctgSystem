<?php
//$time = microtime(true);
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
// error_reporting(E_ALL);

session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
require_once"../class/dbUpdate.php";
$db=new dbConnect();
$db->openDb();
$con=new dbUpdate();

switch($_REQUEST['execute']){
	case'savecheckin':
		if($_POST){
			// print_r($_POST);
			// exit;
			$header="insert into tbl_floorunit_reservation_header (id,customer_name,customer_address,room_info_array,other_charges_array) values 
				('{$_REQUEST['refid']}','{$_REQUEST['customer_name']}','{$_REQUEST['customer_address']}','".serialize($_REQUEST['roomsInfo'])."','".serialize($_REQUEST['serviceInfo'])."') 
				on duplicate key update customer_name=values(customer_name),customer_address=values(customer_address),room_info_array=values(room_info_array),other_charges_array=values(other_charges_array)";
			$qry_header=mysql_query($header);
			if($qry_header){
				$refid = $_REQUEST['refid']?$_REQUEST['refid']:mysql_insert_id();
				foreach($_REQUEST['roomsInfo'] as $key=>$val){
					$sql="insert into tbl_floorunit_reservation (floor,unit,date,header_ref) values ";
					if($val['days']==1){
						$sql.="('{$val['floor']}','{$val['room']}','{$val['indate']}','$refid'),('{$val['floor']}','{$val['room']}','{$val['outdate']}','$refid')";
					}else{
						for($x=date('Y-m-d',strtotime($val['indate']));$x<=date('Y-m-d',strtotime($val['outdate']));$x=date('Y-m-d',strtotime($x."+1 days"))){
							$sql.="('{$val['floor']}','{$val['room']}','$x','$refid')";
						}
					}
					$qry=mysql_query(str_replace(")(","),(",$sql));
					
					$sqlnew = "insert into reservations (name,start,end,room_id,status,paid) values 
						('{$_REQUEST['customer_name']}','".($val['indate']." 12:00:00")."','".($val['outdate']." 12:00:00")."','{$val['room']}','Confirmed','50')";
					$qrynew = mysql_query($sqlnew);
					// if(!$qrynew){
						// echo "Error: ".mysql_error()."<br/>".$sql;
					// }
				}
				
				
			}
			if($qry_header){
				echo "success";
			}
		}
	break;
	case'checkin':
		$info = $db->getWHERE("*","tbl_floorunit_reservation_header","where id='{$_REQUEST['refid']}'");
		$payment = $db->resultArray("*","tbl_floorunit_reservation_payment","where refid='{$_REQUEST['refid']}' order by id desc");
	?>
	<form method="post" id="checkin" action="./content/floorunit_reservation_ajax.php?execute=savecheckin">
		<div style="width:150px;float:left;margin-right:10px;">Reference #:</div>
		<input type="text" readonly name="refid" id="refid" style="float:left;width:300px;" value="<?=$info['id']?>"/>
		<div style="clear:both;height:5px;"></div>
		<div style="width:150px;float:left;margin-right:10px;">Customer Name:</div>
		<input type="text" name="customer_name" style="float:left;width:300px;" value="<?=$info['customer_name']?>"/>
		<div style="clear:both;height:5px;"></div>
		<div style="width:150px;float:left;margin-right:10px;">Address:</div>
		<input type="text" name="customer_address" style="float:left;width:300px;" value="<?=$info['customer_address']?>"/>
		<div style="clear:both;height:5px;"></div>
		<fieldset style="float:left;width:50%;">
			<legend>Room Information</legend>
			<input type="button" onclick="addRoom()" value="+" style="float:left;height:20px;width:20px;margin-right:5px;"/>
			<input type="text" class="xdate" id="begdate"  style="float:left;width:100px;margin-right:5px;"/>
			<input type="text" class="xdate" id="enddate"  style="float:left;width:100px;margin-right:5px;"/>
			<select  id="roomType" style="float:left;margin-right:5px;">
				<option value="">Select Room</option>
				<option value="Standard">Standard</option>
				<option value="Deluxe">Deluxe</option>
				<option value="Family">Family</option>
			</select>
			<input type="text" id="roomnum" style="float:left;width:50px;margin-right:5px;"/>
			<!--input type="button" value="Price/Availability" onclick="checkRoom()" style="width:150px;height:30px;float:right;"/-->
			<div style="clear:both;height:5px;"></div>
			<table class="tbl" id="roomlist" border="1" cellspacing="0" cellpadding="0" width="100%">
				<thead>
				<tr>
					<th>&nbsp;</th>
					<th>Type</th>
					<th style="width:40px;">Room</th>
					<th>Date</th>
					<th style="width:40px;">In/Out</th>
					<th style="width:40px;">Days</th>
					<th>Amount</th>
				</tr>
				</thead>
				<tbody>
				<?php 
				foreach(unserialize($info['room_info_array']) as $key => $val){ ?>
					<tr>
						<td rowspan="2"><input type="radio" name="rooms"></td>
						<td rowspan="2"><input type="text" name="roomsInfo[<?=$key?>][type]" style="width:100%;border:none;" value="<?=$val['type']?>"/></td>
						<td rowspan="2"><input type="text" name="roomsInfo[<?=$key?>][room]" style="width:100%;border:none;" value="<?=$val['room']?>"/></td>
						<td><input type="text" class="roomdate" name="roomsInfo[<?=$key?>][indate]" style="width:100%;border:none;" value="<?=$val['indate']?>"/></td>
						<td><input type="text" style="width:100%;border:none;" value="IN"/></td>
						<td rowspan="2"><input type="text" name="roomsInfo[<?=$key?>][days]" style="width:100%;border:none;" value="<?=$val['days']?>"/></td>
						<td rowspan="2"><input readonly type="text" class="room" name="roomsInfo[<?=$key?>][amount]" style="border:none;width:100%;text-align:right;" value="<?=$val['amount']?>"/></td>
					</tr>
					<tr>
						<td><input type="text" class="roomdate" name="roomsInfo[<?=$key?>][outdate]" style="width:100%;border:none;" value="<?=$val['outdate']?>"/></td>
						<td><input type="text" style="width:100%;border:none;" value="OUT"/></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</fieldset>
		<div style="float:right;width:40%;margin-right:25px;">
			<fieldset style="float:left;width:100%;height:150px;overflow:auto;">
				<legend>Services and Other Charges</legend>
				<input type="button" onclick="addService()" value="+" style="float:left;height:20px;width:20px;margin-right:5px;"/>
				<select name="services" style="float:left;">
					<option value="">Select Services</option>
					<option value="BreakFast|500">Break Fast</option>
				</select>
				<div style="clear:both;height:5px;"></div>
				<table class="tbl" id="servicelist" border="1" cellspacing="0" cellpadding="0" width="100%">
					<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Name</th>
						<th>Rate</th>
						<th>Qty</th>
						<th>Subtotal</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach(unserialize($info['other_charges_array']) as $key => $val){ ?>
						<tr>
							<td><input type="radio" name="service"></td>
							<td><input type="text" name="serviceInfo[<?=$key?>][name]" style="width:100%;border:none;" value="<?=$val['name']?>"/></td>
							<td><input type="text" name="serviceInfo[<?=$key?>][rate]" style="width:100%;border:none;" value="<?=$val['rate']?>"/></td>
							<td><input type="text" name="serviceInfo[<?=$key?>][qty]" style="width:100%;border:none;" value="<?=$val['qty']?>"/></td>
							<td><input readonly type="text" class="other" name="serviceInfo[<?=$key?>][subtotal]" style="border:none;width:100%;"  value="<?=$val['subtotal']?>"/></td>
						</tr>
					<?php } ?>
					</tbody>
					
				</table>
			</fieldset>
			<fieldset style="float:left;width:100%;height:150px;overflow:auto;">
				<legend>Payment Information</legend>
				<input type="button" onclick="hotelPayment()" value="+" style="float:left;height:20px;width:20px;margin-right:5px;"/>
				<div style="clear:both;height:5px;"></div>
				<table class="tbl" id="servicelist" border="1" cellspacing="0" cellpadding="0" width="100%">
					<thead>
						<tr>
							<th>OR#</th>
							<th>DateTime</th>
							<th>Pay Type</th>
							<th>Total</th>
							<th>Details</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach($payment as $key=>$val){ ?>
						<tr>
							<td><?=$val['id']?></td>
							<td><?=$val['datetime']?></td>
							<td><?=$val['payment_method']?></td>
							<td><input class="payment" type="text" value="<?=$val['amount']?>" readonly style="width:100%;border:none;"/></td>
							<td><?=$val['comments']?></td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div style="clear:both;height:5px;"></div>
		<fieldset style="position:absolute;bottom:10px;width:95%;">
			<div style="float:left;width:30%;">
				<div style="float:left;width:45%;">Room Total</div>
				<input type="text" name="roomtotal" style="float:left;width:50%;text-align:right;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:45%;">Other Total</div>
				<input type="text" name="othertotal" style="float:left;width:50%;text-align:right;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:45%;">Net Total</div>
				<input type="text" name="nettotal" style="float:left;width:50%;text-align:right;"/>
				<div style="clear:both;height:5px;"></div>
			</div>
			<div style="float:left;width:35%;">
				<div style="float:left;width:45%;">Discount</div>
				<input type="text" name="discount" style="float:left;width:50%;text-align:right;" value="0"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:45%;">Tax</div>
				<input type="text" name="taxtype" style="float:left;width:50%;text-align:right;" value="VAT 12%"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:45%;">Tax Total</div>
				<input type="text" name="taxtotal" style="float:left;width:50%;text-align:right;"/>
				<div style="clear:both;height:5px;"></div>
			</div>
			<div style="float:left;width:35%;">
				<div style="float:left;width:45%;">Total</div>
				<input type="text" name="subtotal" style="float:left;width:50%;text-align:right;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:45%;">Paid</div>
				<input type="text" name="paid" style="float:left;width:50%;text-align:right;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:45%;">Balance</div>
				<input type="text" name="balance" style="float:left;width:50%;text-align:right;color:red;"/>
				<div style="clear:both;height:5px;"></div>
			</div>
			<input type="button" value="Save" onclick="saveCheckIn();" style="width:150px;height:30px;float:left;"/>
		</fieldset>
	</form>
	<script>
		$(document).ready(function() {
			$('#begdate').datepicker({
				inline: true,
				dateFormat:"yy-mm-dd"
			}).datepicker("setDate", "0");
			
			$('#enddate').datepicker({
				inline: true,
				dateFormat:"yy-mm-dd"
			}).datepicker("setDate", "1");
			
			var room = sumClass("room");
			var other = sumClass("other");
			var payment = sumClass("payment");
			var subtotal = (room+other) * 1.12;
			$("input[name='roomtotal']").val(new Number(room).formatMoney(2));
			$("input[name='othertotal']").val(new Number(other).formatMoney(2));
			$("input[name='nettotal']").val(new Number(room+other).formatMoney(2));
			$("input[name='taxtotal']").val(new Number((room+other) * .12).formatMoney(2));
			$("input[name='subtotal']").val(new Number(subtotal).formatMoney(2));
			$("input[name='paid']").val(new Number(payment).formatMoney(2));
			$("input[name='balance']").val(new Number(subtotal-payment).formatMoney(2));
		});
		function saveCheckIn(){
			var refid=$("#refid").val();
			var form = $("#checkin");
			$.ajax( {
			  type: "POST",
			  url: form.attr( 'action' ),
			  data: form.serialize(),
			  success: function( response ) {
				if(response=="success"){
					$("#reservationDialog").dialog('close');
					checkin(refid);
				}else{
					alert(response);
				}
			  }
			});
		}
		$("#roomlist").bind('keydown',function(e){
			var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
			if(chCode==46){ //pressing delete button
				//$("tr.selected").remove();
				$('input[type="checkbox"]:checked').closest("tr").remove();
			}
		});
		
		function sumClass(classname){
			var sum = 0;
			// iterate through each td based on class and add the values
			$("."+classname).each(function() {
				var value = $(this).val().replace(/,/g, "");
				// add only if the value is number
				console.log(classname+':'+value+'\n');
				if(!isNaN(value) && value.length != 0) {
					sum += parseFloat(value);
				}
			});
			return sum;
		}
		function addRoom(){
			var num = $("#roomlist tbody tr").length;
			var type =$("#roomType").val();
			var room =$("#roomnum").val();
			var xdate =$("#begdate").val();
			var ydate =$("#enddate").val();
			
			$.ajax({
				url: './content/floorunit_reservation_ajax.php?execute=checkRoom',
				data:{type:type,room:room,indate:xdate,outdate:ydate},
				async: false,
				dataType:"json",
				type:"POST",
				success:function(data){
					if(data['msg']=="success"){
						var txt='<tr>\
							<td rowspan="2"><input type="radio" name="rooms"></td>\
							<td rowspan="2"><input type="text" name="roomsInfo['+(num+1)+'][type]" style="border:none;width:100%;" value="'+type+'"/></td>\
							<td rowspan="2"><input type="text" name="roomsInfo['+(num+1)+'][room]" style="border:none;width:100%;" value="'+room+'"/></td>\
							<td><input type="text" class="roomdate" name="roomsInfo['+(num+1)+'][indate]" style="border:none;width:100%;" value="'+xdate+'"/></td>\
							<td><input type="text" style="border:none;width:100%;" value="IN"/></td>\
							<td rowspan="2"><input type="text" name="roomsInfo['+(num+1)+'][days]" style="border:none;width:100%;" value="'+data['days']+'"/></td>\
							<td rowspan="2"><input readonly type="text" class="room" name="roomsInfo['+(num+1)+'][amount]" style="border:none;width:100%;text-align:right;" value="'+data['amt']+'"/></td>\
							</tr>\
							<tr>\
							<td><input type="text" class="roomdate" name="roomsInfo['+(num+1)+'][outdate]" style="border:none;width:100%;" value="'+ydate+'"/></td>\
							<td><input type="text" style="border:none;width:100%;" value="OUT"/></td>\
							</tr>';
						$("#roomlist tbody").append(txt);
					}else{
						alert(data['msg']);
					}
				}
			});
			
		}
		function addService(){
			var num = $("#servicelist tbody tr").length;
			var name= $("select[name='services']").val().split('|');
			var txt='<tr>\
				<td><input type="radio" name="service"></td>\
				<td><input type="text" name="serviceInfo['+(num+1)+'][name]" style="border:none;width:100%;" value="'+name[0]+'"/></td>\
				<td><input type="text" name="serviceInfo['+(num+1)+'][rate]" style="border:none;width:100%;" value="'+name[1]+'"/></td>\
				<td><input type="text" name="serviceInfo['+(num+1)+'][qty]" style="border:none;width:100%;" value="1"/></td>\
				<td><input readonly type="text" class="other" name="serviceInfo['+(num+1)+'][subtotal]" style="border:none;width:100%;"  value="'+name[1]+'"/></td>\
				</tr>';
			$("#servicelist").append(txt);
		}
		function hotelPayment(){
			var refid = $("input[name='refid']").val();
			hotelDialog("paymentDialog",440,300,"RECEIVED PAYMENT","paymentDialog&refid="+refid);
		}
	</script>
	<?
	break;
	case'checkRoom':
		//$checkRoom = $db->getWHERE("*","tbl_floorunit_reservation","where floor='{$_REQUEST['floor']}' and unit='{$_REQUEST['room']}' and date='{$_REQUEST['date']}'");
		$checkRoom = $db->getWHERE("*","tbl_floorunit_reservation","where unit='{$_REQUEST['room']}' and (date between '{$_REQUEST['indate']}' and '{$_REQUEST['outdate']}')");
		if($checkRoom){
			echo '{"msg":"This room was already booked on this date..."}';
		}else{
			$diff = (strtotime($_REQUEST['outdate']) - strtotime($_REQUEST['indate']))/(60 * 60 * 24);
			$info = $db->getWHERE("*","tbl_floor_mapping","where unit='{$_REQUEST['room']}'");
			echo '{"amt":'. ($diff * $info['price']) .',"msg":"success","days":'.$diff.'}';
		}
	break;
	case'paymentDialog':
		$info = $db->getWHERE("*","tbl_floorunit_reservation_header","where id='{$_REQUEST['refid']}'");
		?>
		<fieldset>
			<legend>Guest Name</legend>
			<h2><?=$info['customer_name']?></h2>
		</fieldset>
		<div style="clear:both;height:5px;"></div>
		<fieldset>
			<legend>Post Payment</legend>
			<form name="postpayment" id="postpayment" method="post" action="./content/floorunit_reservation_ajax.php?execute=postpayment">
				<input type="hidden" id="refid" name="refid" value="<?=$_REQUEST['refid']?>"/>
				<div style="float:left;width:150px;">Payment Method</div>
				<select name="payment_method" style="float:left;width:150px;">
					<option value="Cash">Cash</option>
					<option value="CreditCard">CreditCard</option>
					<option value="DebitCard">DebitCard</option>
				</select>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:150px;">Post Amount</div>
				<input type="text" name="postamount" style="float:left;width:150px;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="float:left;width:150px;">Comments</div>
				<input type="text" name="comments" style="float:left;width:150px;"/>
			</form>
		</fieldset>
		<div style="clear:both;height:5px;"></div>
		<input type="button" value="POST PAYMENT" onclick="post_payment()" style="float:left;height:30px;width:200px;margin-right:10px;"/>
		<input type="button" value="CANCEL" style="float:left;height:30px;width:200px;"/>
		<script>
			function post_payment(){
				var refid=$("#refid").val();
				var form = $("#postpayment");
				$.ajax( {
				  type: "POST",
				  url: form.attr( 'action' ),
				  data: form.serialize(),
				  success: function( response ) {
					if(response=="success"){
						$("#paymentDialog").dialog('close');
						checkin(refid);
					}else{
						alert(response);
					}
				  }
				});
			}
		</script>
		<?
	break;
	case'postpayment':
		$sql="insert into tbl_floorunit_reservation_payment (refid,payment_method,amount,comments) values 
			('{$_REQUEST['refid']}','{$_REQUEST['payment_method']}','{$_REQUEST['postamount']}','{$_REQUEST['comments']}')";
		$qry=mysql_query($sql);
		if($qry){
			echo "success";
		}else{
			echo mysql_error();
		}
	break;
}