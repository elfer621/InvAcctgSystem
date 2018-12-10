<?php
unset($_SESSION['error']);
if($_REQUEST['execute']){
	session_start();
	require_once"../../settings.php";
	require_once"../../class/dbConnection.php";
	require_once"../../class/dbUpdate.php";
	$db=new dbConnect();
	$db->openDb();
	$con=new dbUpdate();
	switch($_REQUEST['execute']){
		
		case'deleteInvoice':
			$del1 = mysql_query("delete from tbl_sales_invoice_header where id='{$_REQUEST['refid']}'");
			$del2 = mysql_query("delete from tbl_sales_invoice_items where refid='{$_REQUEST['refid']}'");
			$del3 = mysql_query("delete from tbl_customers_trans where receipt='{$_REQUEST['refid']}'");
			if($del1&&$del2&&$del3){
				echo "success";
			}
		break;
		case'selectedProd':
			$prod = $db->resultArray("b.*,a.price,a.cost,a.barcode,a.divmul,a.unit","tbl_barcodes a left join tbl_product_name b on a.sku_id=b.sku_id","where barcode='{$_REQUEST['barcode']}'");
			echo '{"rec":'. json_encode($prod) .',"msg":"success"}';
		break;
		case'createNew':
			unset($_SESSION['dynamic_invoicing']['custid']);
		break;
		case'getStockOut':
			$list = $db->resultArray("*","tbl_stockout_items","where stockin_refid='{$_REQUEST['stockoutref']}'");
			echo '{"rec":'. json_encode($list) .',"msg":"success"}';
		break;
		case'toSession':
			$_SESSION['dynamic_invoicing']['custid']=$_REQUEST['custid'];
			if($_SESSION['dynamic_invoicing']){
				echo "success";
			}
		break;
		case'list':
			if($_REQUEST['search']||$_REQUEST['date']){
				if($_REQUEST['search']){
					$list = $db->resultArray("a.*,b.total,cust.customer_name","tbl_{$_REQUEST['tbltype']}_header a 
					left join (select refid,sum(amount) total from tbl_{$_REQUEST['tbltype']}_items group by refid) b on a.id=b.refid 
					left join tbl_customers cust on a.cust_id=cust.cust_id",
					" where a.id like '%{$_REQUEST['search']}%' or a.ponum like '%{$_REQUEST['search']}%' order by a.id desc");
				}else{
					$list = $db->resultArray("a.*,b.total,cust.customer_name","tbl_{$_REQUEST['tbltype']}_header a 
					left join (select refid,sum(amount) total from tbl_{$_REQUEST['tbltype']}_items group by refid) b on a.id=b.refid 
					left join tbl_customers cust on a.cust_id=cust.cust_id",
					" where a.id like '%{$_REQUEST['search']}%' or a.ponum like '%{$_REQUEST['search']}%' or a.date >= '{$_REQUEST['date']}' order by a.id desc");
				}
				
			}else{
				if($_REQUEST['custid']){
					$list = $db->resultArray("a.*,b.total,cust.customer_name","tbl_{$_REQUEST['tbltype']}_header a 
					left join (select refid,sum(amount) total from tbl_{$_REQUEST['tbltype']}_items group by refid) b on a.id=b.refid 
					left join tbl_customers cust on a.cust_id=cust.cust_id",
					" where a.cust_id='{$_REQUEST['custid']}' order by a.id desc");
				}else{
					
					$list = $db->resultArray("a.*,b.total,cust.customer_name","tbl_{$_REQUEST['tbltype']}_header a 
					left join (select refid,sum(amount) total from tbl_{$_REQUEST['tbltype']}_items group by refid) b on a.id=b.refid 
					left join tbl_customers cust on a.cust_id=cust.cust_id",
					" where a.id like '%{$_REQUEST['search']}%' or a.ponum like '%{$_REQUEST['search']}%' or a.date >= '{$_REQUEST['date']}' order by a.id desc");
				}
				
			}
			
		?>
			
			<input type="text" name="date" style="width:100px;float:left;margin-right:10px;" value="<?=date('Y-m-d')?>"/>
			<input type="text" name="search" id="search" style="float:left;width:75%;" />
			<input type="button" style="float;right;width:10%;margin-left:5px;" value="Search" onclick="search();"/>
			<div style="clear:both;height:5px;"></div>
			<table class="navigateableMain" cellspacing="0" cellpadding="0" width="100%">
				<thead>
					<tr>
						<th style="border:none;">Ref #</th>
						<th style="border:none;">Customer Name</th>
						<th style="border:none;">Date</th>
						<th style="border:none;">Amount</th>
						<th style="border:none;">Prepared By</th>
						<th style="border:none;">Menu</th>
					</tr>
				</thead>
				<tbody style="font-size:10px;">
					<?php foreach($list as $key => $val){ ?>
						<tr>
							<td><a href="?page=dynamic_invoicing&tbltype=<?=$_REQUEST['tbltype']?>&refid=<?=$val['id']?>"><?=$val['id']?></a></td>
							<td style="text-align:left;"><?=$val['customer_name']?></td>
							<td style="text-align:center;"><?=$val['date']?></td>
							<td style="text-align:right;"><?=number_format($val['total'],2)?></td>
							<td style="text-align:center;"><?=$val['preparedby']?></td>
							<td style="text-align:center;">
								<a href="./reports/dynamic_invoicing<?=$_SESSION['repExtension']?>.php?refid=<?=$val['id']?>&tbltype=<?=$_REQUEST['tbltype']?>" target="_blank">
									<img src="./images/print.png" style="width:25px;height:25px;" title="Print"/>
								</a>
								<a href="javascript:delInvoice('<?= $val['id']?>');void(0);">
									<img src="./images/del.png" style="width:25px;height:25px;" title="Delete"/>
								</a>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
			<script>
			$(document).ready(function() {
				$('input[name="date"]').datepicker({
					changeMonth: true,
					changeYear: true,
					inline: true,
					dateFormat:"yy-mm-dd"
				});
			});
			</script>
		<?
		break;
	}
}else{
	if($_POST){
		// echo "<pre>";
		// print_r($_POST);
		// echo "</pre>";
		
		
		mysql_query("BEGIN");
		$delsql = "delete from tbl_customers_trans where receipt='{$_REQUEST['refid']}'";
		$del = mysql_query($delsql);
		if(!$del){
			echo mysql_error();
		}
		$data=array(
				'id'=>$_REQUEST['refid'],
				'cust_id'=>$_REQUEST['business_name'],
				'date'=>$_REQUEST['info']['date'],
				'payment_terms'=>$_REQUEST['info']['payment_terms'],
				'ponum'=>$_REQUEST['info']['ponum'],
				'regnum'=>$_REQUEST['info']['regnum'],
				'region'=>$_REQUEST['info']['region'],
				'rep'=>$_REQUEST['info']['rep'],
				'taxtype'=>$_REQUEST['taxtype'],
				'agreement'=>$_REQUEST['agreement'],
				'preparedby'=>$_SESSION['complete_name'],
				'cust_address'=>$_REQUEST['cust_info']['address'],
				'cust_telnum'=>$_REQUEST['cust_info']['telnum'],
				'cust_faxnum'=>$_REQUEST['cust_info']['faxnum'],
				'cust_contactperson'=>$_REQUEST['cust_info']['contact_person'],
				'cust_tin'=>$_REQUEST['cust_info']['tin']
			);
		
		$sql = $db->genSqlInsert($data,"tbl_{$_REQUEST['tbltype']}_header");
		$qry1 = mysql_query($sql);
		
		if($qry1){
			$refid=$_REQUEST['refid']?$_REQUEST['refid']:mysql_insert_id();
			$del = mysql_query("delete from tbl_{$_REQUEST['tbltype']}_items where refid='$refid'");
			$flag=false;
			$sql2="insert into tbl_{$_REQUEST['tbltype']}_items (id,refid,skuid,barcode,divmul,item_spec,qty,unit,unitprice,amount,cost,disc) values ";
			$xtotal=0;
			$cost_total=0;
			foreach($_REQUEST['items'] as $key=>$val){
				if($flag)$sql2.=",";
				$sql2.="('{$val['id']}','{$refid}','{$val['skuid']}','{$val['barcode']}','{$val['divmul']}','".mysql_real_escape_string($val['items_spec'])."','{$val['qty']}','".mysql_real_escape_string($val['unit'])."','".preg_replace("/[^0-9.-]/", "", $val['unit_price'])."','".preg_replace("/[^0-9.-]/", "", $val['amount'])."','".preg_replace("/[^0-9.-]/", "", $val['cost'])."','".$val['disc']."')";
				$flag=true;
				$xtotal+=preg_replace("/[^0-9.-]/", "", $val['amount']);
				$cost_total+=preg_replace("/[^0-9.-]/", "", $val['cost']*$val['qty']);
			}
			$sql2.=" on duplicate key update cost=values(cost),skuid=values(skuid),barcode=values(barcode),divmul=values(divmul),item_spec=values(item_spec),qty=values(qty),unit=values(unit),unitprice=values(unitprice),amount=values(amount)";
			//echo $sql2;exit;
			$qry2=mysql_query($sql2);
			if(!$qry2){
				echo mysql_error();
			}
		}else{
			echo mysql_error();
		}
		if($qry1 && $qry2){
			mysql_query("COMMIT");
			//Insert Customer Transaction
			$del = mysql_query("delete from tbl_customers_trans where cust_id='{$_REQUEST['business_name']}' and receipt='$refid' and transtype='{$_REQUEST['tbltype']}'");
			$data=array(
				'cust_id'=>$_REQUEST['business_name'],
				'receipt'=>$refid,
				'date'=>$_REQUEST['info']['date'],
				'transtype'=>$_REQUEST['tbltype'],
				'amount'=>str_replace(",","",$xtotal),
				'details'=>strtoupper(str_replace("_"," ",$_REQUEST['tbltype']))." ".$refid
			);
			$sql_cust_trans = $db->genSqlInsert($data,'tbl_customers_trans');
			//echo $sql_cust_trans; exit;
			$qry_cust_trans = mysql_query($sql_cust_trans);
			if(!$qry_cust_trans){
				echo "Error CustTrans:".mysql_error()."<br/>";
			}
			//Insert Customer Transaction
			
			//Auto SJ
			$agent = $db->getWHERE("*","req_agent","where id='{$_REQUEST['info']['rep']}'");
			$cost_center = $agent['cost_center']?$agent['cost_center']:$_SESSION['connect'];
			$glref = preg_replace("/[^0-9]/", "", $refid);
			$total_amt = str_replace(",","",$xtotal);
			$sql="insert into tbl_vouchering (id,center,date,type,remarks,total,preparedby,`status`,`reference`) values 
				('".$glref."','{$_SESSION['connect']}','".$_REQUEST['info']['date']."','SJ', 
				'".strtoupper($_SESSION['connect']).":TO RECORD SALES INVOICE # $glref','".$total_amt."', 
				'".$_SESSION['xid']."','ForApproval','SI#{$_REQUEST['refid']}') 
				on duplicate key update `date`=values(`date`),center=values(center),type=values(type),remarks=values(remarks),total=values(total)";
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'7502','account_desc'=>'ACCOUNTS RECEIVABLE-TRADE','dr'=>$total_amt,'cr'=>'','center'=>"$cost_center",'ar_refid'=>"{$_REQUEST['business_name']}",'type'=>'SJ');
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'4000','account_desc'=>'SALES','dr'=>'','cr'=>$total_amt,'center'=>"$cost_center",'type'=>'SJ');
			//inventory
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'1402','account_desc'=>'INVENTORY REGULAR','dr'=>'','cr'=>$cost_total,'center'=>"$cost_center",'type'=>'SJ');
			$entry[]=array('fiscal_year'=>date('Y'),'account_code'=>'5000','account_desc'=>'COST OF SALES - BOOKS','dr'=>$cost_total,'cr'=>'','center'=>"$cost_center",'type'=>'SJ');
			//inventory
			$glid=$con->insertSJDiffApproach($glref,$sql,$_REQUEST['info']['date'],$entry,'SJ');
			//Auto SJ
			
			echo "<script>$(document).ready(function(){viewReport('./reports/dynamic_invoicing_tkc.php?refid=$refid&tbltype=sales_invoice');window.location=removeURLParameter(document.URL, 'refid');});</script>";
		}else{
			mysql_query("ROLLBACK");
			echo mysql_error();
		}
	}

if($_REQUEST['refid']){
	$header = $db->getWHERE("*","tbl_{$_REQUEST['tbltype']}_header","where id='".$_REQUEST['refid']."'");
	$items = $db->resultArray("*","tbl_{$_REQUEST['tbltype']}_items","where refid='".$_REQUEST['refid']."'");
	$_SESSION['dynamic_invoicing']['custid']=$header['cust_id'];
}
$info = $db->getWHERE("*","tbl_customers","where cust_id='".($header['cust_id']?$header['cust_id']:$_SESSION['dynamic_invoicing']['custid'])."'","main");
$si_nxtnum = ($_SESSION['settings']['system_name']=="RTK"?$db->getNextSI():$db->getNextID("id","tbl_sales_invoice_header"));

?>
<style>
.chosen-single span {
	width:120px;
}
</style>
<div class="content" style="min-height:300px;width:100%!important;">
	<h2><?=strtoupper(str_replace("_"," ",$_REQUEST['tbltype']))?> CREATION</h2>
	<?=$_SESSION['error']?>
	<form name="frminfo" id="frminfo" method="post">
	<div style="float:left;margin-right:5px;width:55%;">
		<div style="float:left;margin-right:5px;width:100px;">Business Name:</div>
		<div style="width:500px;float:left;">
			<select  name="business_name" id="business_name" data-placeholder="Choose Customer Name..." class="chosen-select"  tabindex="1">
				<option value=""></option>
				<?
				//$qry =mysql_query("select * from tbl_customers order by customer_name asc");
				// while($row=mysql_fetch_assoc($qry)){
					// echo "<option ".($header['cust_id']==$row['cust_id']?"selected":"")." value='{$row['cust_id']}'>{$row['customer_name']}</option>";
				// }
				$cust = $db->resultArray("*","tbl_customers","order by customer_name asc","main");
				foreach($cust as $k => $row){
					echo "<option ".($header['cust_id']==$row['cust_id']?"selected":"")." value='{$row['cust_id']}'>{$row['customer_name']}</option>";
				}
				?>
			</select>
		</div>
	</div>
	
	<div style="clear:both;height:10px;"></div>
		<div style="width:15%;float:left;">
			<fieldset>
				<legend>Customer Information</legend>
				<div style="width:150px;float:left;">Address</div>
				<input name="cust_info[address]" value="<?=$header['cust_address']?$header['cust_address']:$info['customer_address']?>" type="text" style="width:100%;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Telephone Number</div>
				<input name="cust_info[telnum]" value="<?=$header['cust_telnum']?$header['cust_telnum']:$info['contact_number']?>" type="text" style="width:100%;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Fax Number</div>
				<input name="cust_info[faxnum]" value="<?=$header['cust_faxnum']?$header['cust_faxnum']:$info['fax_person']?>" type="text" style="width:100%;"/>
				<div style="clear:both;height:5px;"></div>
				<div style="width:150px;float:left;">Contact Person</div>
				<input name="cust_info[contact_person]" value="<?=$header['cust_contactperson']?$header['cust_contactperson']:$info['contact_person']?>" type="text" name="info[attn]" style="width:100%;"/>
				<div style="clear:both;height:5px;"></div>
				<div  style="width:150px;float:left;">TIN</div>
				<input name="cust_info[tin]" value="<?=$header['cust_tin']?$header['cust_tin']:$info['tin']?>" type="text" style="width:100%;"/>
				<div style="clear:both;height:5px;"></div>
			</fieldset>
			<fieldset>
				<legend>Details</legend>
					<div style="width:150px;float:left;">SI No.:</div>
					<input type="text" name="refid" value="<?=$header['id']?$header['id']:$si_nxtnum?>" style="width:100%;" />
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Date</div>
					<input value="<?=$header['date']?$header['date']:date('Y-m-d')?>" type="text" name="info[date]" style="width:100%;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">Payment Terms</div>
					<select name="info[payment_terms]" style="width:100%;">
						<option value="">Select</option>
						<?php
						$terms = array("COD","15 DAYS","30 DAYS","60 DAYS");
						$def_val = $header['payment_terms']?$header['payment_terms']:"30 DAYS";
						foreach($terms as $key => $val){
							echo "<option ".($def_val==$val?"selected":"")." value='{$val}'>{$val}</option>";
						}
						?>
					</select>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">DR No.</div>
					<input value="<?=$header['regnum']?>" type="text" name="info[regnum]" style="width:100%;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">PO / SO NO.:</div>
					<input value="<?=$header['ponum']?>" type="text" name="info[ponum]" style="width:100%;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">BRANCH:</div>
					<input value="<?=$_SESSION['connect']?$_SESSION['connect']:$header['region']?>" type="text" name="info[region]" style="width:100%;"/>
					<div style="clear:both;height:5px;"></div>
					<div style="width:150px;float:left;">REP:</div>
					<div style="float:left;width:100%;">
						<select  name="info[rep]" id="refinfo" data-placeholder="Choose Sales Ref" class="rep-select"  tabindex="1" style="width:100%;">
							<option value="">Sales Rep</option>
							<?php
							$rep = $db->resultArray("*","req_agent","");
							foreach($rep as $key => $val){
								echo "<option ".($header['rep']==$val['id']?"selected":"")." value='{$val['id']}'>(".$val['id'].") {$val['agent_name']}</option>";
							}
							?>
						</select>
					</div>
					<div style="clear:both;height:5px;"></div>
					<input type="button" value="SAVE" onclick="save()" style="float:left;height:30px;width:100%;"/>
			</fieldset>
			<fieldset>
				<legend>Tax Type</legend>
				<input checked="checked" type="radio" name="taxtype" value="vatable" <?=$header['taxtype']=="vatable"?'checked="checked"':""?>/> VATable Sales </br>
				<input type="radio" name="taxtype" value="vatexempt" <?=$header['taxtype']=="vatexempt"?'checked="checked"':""?>/> VAT - Exempt Sales </br>
				<input type="radio" name="taxtype" value="zerorated" <?=$header['taxtype']=="zerorated"?'checked="checked"':""?>/> Zero Rated Sales </br>
			</fieldset>
		</div>
		<div style="width:83%;float:right;">
			<fieldset style="float:left;width:95%;">
				<legend>Menu</legend>
				<input id="bt7" class="buthov" type="button" value="List" onclick="viewlist()" style="float:right;height:40px;width:150px;float:left;"/>
				<input id="bt8" class="buthov" type="button" value="New" style="float:right;height:40px;width:150px;float:left;" onclick="createNew()"/>
				<input id="bt9" class="buthov" type="button" value="Daily Sales" style="float:right;height:40px;width:150px;float:left;" onclick="viewReport('reports/sales_invoice_dailysummary<?=$_SESSION['repExtension']?>.php');"/>
			</fieldset>
			<div style="clear:both;height:5px;"></div>
			<div style="display: flex;">
				<fieldset style="align-items: stretch;width:100%;float:left;">
					<legend>Item Details 
						<input type="button" value="Prod Search" id="prodsearch" style="width:80px;height:25px;"/>
						<input type="button" value="+" id="items" style="width:20px;height:25px;"/>
						<!--input type="text" name="stockoutref" id="stockoutref" style="float:left;width:80px;height:15px;float:right;margin-left:10px;"/>
						<input type="button" value="OUT" id="items" style="width:40px;height:20px;float:right;margin-left:10px;" onclick="getStockOut()"/-->
					</legend>
					<table class="navigateableMain" id="items_tbl" cellspacing="0" cellpadding="0" width="100%">
						<thead>
							<tr>
								<th style="border:none;">&nbsp;</th>
								<th style="border:none;">Barcode</th>
								<th style="border:none;width:180px;">Item/Specification</th>
								<th style="border:none;">Qty</th>
								<th style="border:none;width:40px;">Unit</th>
								<th style="border:none;">Unit Price</th>
								<th style="border:none;width:40px;">Disc</th>
								<th style="border:none;">Amount</th>
							</tr>
						</thead>
						<tbody>
						<?php
						if($items){
							$count=0;
							$subtotal=0;
							foreach($items as $key => $val){
								echo '<tr>
									<td><input type="checkbox" ><input type="hidden" name="items['.$count.'][id]" value="'.$val['id'].'"/></td>
									<td>
										<input type="text" name="items['.$count.'][barcode]" value="'.$val['barcode'].'" style="width:100%;"/>
										<input type="hidden" name="items['.$count.'][skuid]" value="'.$val['skuid'].'"/>
										<input type="hidden" name="items['.$count.'][divmul]" value="'.$val['divmul'].'"/>
										<input type="hidden" name="items['.$count.'][cost]" value="'.$val['cost'].'"/>
									</td>
									<td><input type="text" name="items['.$count.'][items_spec]" value="'.$val['item_spec'].'" style="width:100%;"/></td>
									<td><input type="text" class="qty" name="items['.$count.'][qty]" value="'.$val['qty'].'" style="width:100%;"/></td>
									<td><input type="text" name="items['.$count.'][unit]" value="'.$val['unit'].'" style="width:100%;"/></td>
									<td><input type="text" class="unitprice" name="items['.$count.'][unit_price]" value="'.$val['unitprice'].'" style="width:100%;text-align:right;"/></td>
									<td><input type="text" class="disc" name="items['.$count.'][disc]" value="'.$val['disc'].'" style="width:100%;"/></td>
									<td><input type="text" class="amt" name="items['.$count.'][amount]" style="width:100%;text-align:right;" value="'.$val['amount'].'"/></td>
								</tr>';
								$count++;
								$subtotal+=$val['amount'];
							}
						}else{
							if($_REQUEST['tbltype']=="billing_statement"){
								$count=1;
								echo '<tr>
									<td><input type="checkbox" ><input type="hidden" name="items['.$count.'][id]" value=""/></td>
									<td><input type="text" name="items['.$count.'][items_spec]" value="SERVICES BILL" style="width:100%;"/></td>
									<td><input type="text" class="qty" name="items['.$count.'][qty]" value="1" style="width:100%;"/></td>
									<td><input type="text" name="items['.$count.'][unit]" value="JOB" style="width:100%;"/></td>
									<td><input type="text" class="unitprice" name="items['.$count.'][unit_price]" value="0" style="width:100%;"/></td>
									<td><input type="text" class="amt" name="items['.$count.'][amount]" style="width:100%;text-align:right;" value="0"/></td>
								</tr>';
							}
						}
						?>
						</tbody>
						<tfoot>
							<th colspan="7">Total</th>
							<th><?=number_format($subtotal,2)?></th>
						</tfoot>
					</table>
				</fieldset>
			</div>
			<div style="clear:both;height:5px;"></div>
			<textarea style="width:100%;height:300px;" name="agreement"><?=$header['agreement']?></textarea>
		</div>
	</form>
</div>
<script>

$(document).ready(function() {
	tinymce.init({ selector:'textarea' });
	$("#business_name").val(<?=$_SESSION['dynamic_invoicing']['custid']?>);
	var config = {
	  '.chosen-select'           : {width: "80%",style:"float:left;height:40px;"},
	  '.rep-select'           : {width: "100%",style:"float:left;height:40px;"}
	}
	for (var selector in config) {
	  $(selector).chosen(config[selector]);
	}
	$(".chosen-select").chosen().change(function(){
		toSession();
	});
	
	$('input[name="info[date]"]').datepicker({
		changeMonth: true,
		changeYear: true,
		inline: true,
		dateFormat:"yy-mm-dd"
	});
	setChanges();
});

function getStockOut(){
	var ref = $("#stockoutref").val();
	$.ajax({
		url: './content/rtk/dynamic_invoicing.php?execute=getStockOut&stockoutref='+ref,
		type:"POST",
		dataType: 'json',
		success:function(data){
			//console.log(data.rec);
			$.each(data.rec, function(k, v) {
				var num =$("#items_tbl tbody tr").length;
				var txt = '<tr>\
					<td><input type="checkbox" ></td>\
					<td><input type="text" name="items['+num+'][items_spec]" value="'+v['item_desc']+'" style="width:100%;"/></td>\
					<td><input type="text" class="qty" name="items['+num+'][qty]" value="'+v['qty']+'" style="width:100%;"/></td>\
					<td><input type="text" name="items['+num+'][unit]" value="'+v['unit']+'" style="width:100%;"/></td>\
					<td><input type="text" class="unitprice" name="items['+num+'][unit_price]" value="'+v['selling']+'" style="width:100%;"/></td>\
					<td><input type="text" class="amt" name="items['+num+'][amount]" style="width:100%;text-align:right;" value="'+v['total']+'"/></td>\
				</tr>';
				$("#items_tbl tbody").prepend(txt);
			});
			var subtotal = sumName(".amt");
			$("#items_tbl tfoot tr:first th:eq(1)").html(strtocurrency(subtotal));
		}
	});
}
function toSession(){
	var custid = $("#business_name").val();
	if(custid!=""){
		$.ajax({
			url: './content/rtk/dynamic_invoicing.php?execute=toSession&custid='+custid,
			type:"POST",
			success:function(data){
				window.location=removeURLParameter(document.URL, 'refid');
			}
		});
	}
}
function delInvoice(refid){
	var r=confirm("Are you sure to delete this invoice?");
	if (r==true){
	  //window.location.reload();
	  $.ajax({
			url: './content/rtk/dynamic_invoicing.php?execute=deleteInvoice&refid='+refid,
			type:"POST",
			success:function(data){
				if(data=="success"){
					window.location=document.URL;
				}
				
			}
		});
	}
}
function createNew(){
	$.ajax({
		url: './content/rtk/dynamic_invoicing.php?execute=createNew',
		type:"POST",
		success:function(data){
			window.location=removeURLParameter(document.URL, 'refid');
		}
	});
}
function viewlist(){
	var custid = $("#business_name").val();
	clickDialogUrl("dialogbox3",900,500,'./content/rtk/dynamic_invoicing.php?tbltype='+getParam('tbltype')+'&execute=list&custid='+custid,"Listing");
}
function search(){
	var search = $("#search").val();
	var date = $("input[name='date']").val();
	clickDialogUrl("dialogbox3",900,500,'./content/rtk/dynamic_invoicing.php?tbltype='+getParam('tbltype')+'&execute=list&search='+search+'&date='+date,"Listing");
}
$("input[value='Prod Search']").on('click',function(){
	clickDialogUrl("prodlist",900,520,'./content/pos_ajax.php?execute=prodlist',"Product Search");
});
$("input[value='+']").on('click',function(){
	var id = $(this).attr('id');
	var num =$("#"+id+"_tbl tbody tr").length;
	switch(id){
		case'items':
			var txt = '<tr>\
					<td><input type="checkbox" ></td>\
					<td><input type="text" name="items['+num+'][barcode]" value="" style="width:100%;"/></td>\
					<td><input type="text" name="items['+num+'][items_spec]" value="" style="width:100%;"/></td>\
					<td><input type="text" class="qty" name="items['+num+'][qty]" value="" style="width:100%;"/></td>\
					<td><input type="text" name="items['+num+'][unit]" value="" style="width:100%;"/></td>\
					<td><input type="text" class="unitprice" name="items['+num+'][unit_price]" value="" style="width:100%;"/></td>\
					<td><input type="text" class="disc" name="items['+num+'][disc]" value="0" style="width:100%;"/></td>\
					<td><input type="text" class="amt" name="items['+num+'][amount]" style="width:100%;text-align:right;" value="0.00"/></td>\
				</tr>';
		break;
	}
	$("#"+id+"_tbl tbody").prepend(txt);
	setChanges();
	
});
function addItemDetails(barcode){
	
	if($("#tbllotexp:visible").length==1){
		var lotnum = $('#tbllotexp input[type="radio"]:checked').closest("tr").find("td:eq(2)").html();
		var expdate = $('#tbllotexp input[type="radio"]:checked').closest("tr").find("td:eq(3)").html();
		$('#dialogbox2').dialog('close');
	}
	var num =$("#items_tbl tbody tr").length;
	$.ajax({
		url: './content/rtk/dynamic_invoicing.php?execute=selectedProd&barcode='+barcode,
		type:"POST",
		dataType: 'json',
		success:function(data){
			$.each(data.rec, function(k, v) {
				var num =$("#items_tbl tbody tr").length;
				if(lotnum||expdate){
					var prodname = v['product_name']+" ["+lotnum+" "+expdate+"]";
				}else{
					var prodname = v['product_name'];
				}
				var txt = '<tr>\
					<td><input type="checkbox" ></td>\
					<td>\
					<input type="text" name="items['+num+'][barcode]" value="'+v['barcode']+'" style="width:100%;"/>\
					<input type="hidden" name="items['+num+'][skuid]" value="'+v['sku_id']+'" />\
					<input type="hidden" name="items['+num+'][divmul]" value="'+v['divmul']+'" />\
					<input type="hidden" name="items['+num+'][cost]" value="'+v['cost']+'" />\
					</td>\
					<td><input type="text" name="items['+num+'][items_spec]" value="'+prodname+'" style="width:100%;"/></td>\
					<td><input type="text" class="qty" name="items['+num+'][qty]" value="1" style="width:100%;"/></td>\
					<td><input type="text" name="items['+num+'][unit]" value="'+v['base_unit']+'" style="width:100%;"/></td>\
					<td><input type="text" class="unitprice" name="items['+num+'][unit_price]" value="'+strtocurrency(v['price'])+'" style="width:100%;text-align:right;"/></td>\
					<td><input type="text" class="disc" name="items['+num+'][disc]" value="0" style="width:100%;"/></td>\
					<td><input type="text" class="amt" name="items['+num+'][amount]" style="width:100%;text-align:right;" value="'+strtocurrency(v['price'])+'"/></td>\
				</tr>';
				$("#items_tbl tbody").prepend(txt);
				setChanges();
			});
			var subtotal = sumName(".amt");
			$("#items_tbl tfoot tr:first th:eq(1)").html(strtocurrency(subtotal));
		}
	});
}

function setChanges(){
	$(".qty, .unitprice, .disc").on("change paste keyup",function(){
		var tr = $(this).closest("tr");
		var qty = strtodouble(tr.find(".qty").val());
		var price = strtodouble(tr.find(".unitprice").val());
		var disc = strtodouble(tr.find(".disc").val());
		var total = (qty*price);
		total = (total - (total* (disc/100)));
		tr.find(".amt").val(strtocurrency(total));
		var subtotal = sumName(".amt");
		$("#items_tbl tfoot tr:first th:eq(1)").html(strtocurrency(subtotal));
	});
}
function sumName(name){
	var sum = 0;
	// iterate through each td based on class and add the values
	$(name).each(function() {
		//var value = $(this).find("'td:eq(1)'").children().val();
		var value = $(this).val().replace(/,/g, "");
		// add only if the value is number
		if(!isNaN(value) && value.length != 0) {
			sum += parseFloat(value);
		}
	});
	return sum;
}
// $(".disc").change(function(){
	// var val = $(this).val();
	// if(val>=1){
		// $(this).closest("tr").find("td:eq(6)").find(".disc").val(val/100);
	// }
// });
$("#items_tbl").bind('keydown',function(e){
	var chCode = e.keyCode==0 ? e.charCode : e.keyCode;
	if(chCode==46){ //pressing delete button
		$('input[type="checkbox"]:checked').closest("tr").remove();
	}
	var subtotal = sumName(".amt");
	$("#items_tbl tfoot tr:first th:eq(1)").html(strtocurrency(subtotal));
});
function save(){
	if($("input[name='refid']").val()==""){
		alert("Reference Number is required...");
		return false;
	}else{
		$("#frminfo").submit();
	}
}
</script>
<?php } ?>