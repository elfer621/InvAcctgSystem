<?php
error_reporting(0);
session_start();
require_once"../settings.php";
require_once"../class/dbConnection.php";
$db=new dbConnect();
$db->openDb();
// if(!$con){
	// $db->openDb_local();
// }
$readingNum = $db->getReadingnum($_SESSION['counter_num']);
switch($_REQUEST['execute']){
	case'genTbl':
		if($_REQUEST['page']=="stockin"||$_REQUEST['page']=="stockout"){
		if(isset($_SESSION['stockin'])){ $change="";$xtotal=0;?>
			<?php $count=1; foreach($db->subval_sort($_SESSION['stockin'],'count',arsort) as $val){ ?>
				<tr>
					<td><a href="javascript:backToBarcode('<?=$val['bcode']?>');" class="activationMain"><?php echo $val['bcode']; ?></a></td>
					<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
					<td style="text-align:right;"><?php echo number_format($val['cost'],2) ?></td>
					<td style="text-align:right;"><?php echo $val['qty'] ?></td>
					<td  style="text-align:left;"><?php echo $val['unit'] ?></td>
					<td  style="text-align:left;"><?php echo $val['discount'] ?></td>
					<td  style="text-align:right;"><?php echo number_format(($val['cost']*$val['qty'])*(1-$val['discount']),2) ?></td>
					<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
				</tr>
			<?php $xtotal+=(($val['cost']*$val['qty'])*(1-$val['discount']));$count++;} ?>
		<?php }
		}else{
			if(isset($_SESSION['sales'])){ $change="";$xtotal=0;
				$count=1; foreach($db->subval_sort($_SESSION['sales'],'count',arsort) as $val){ ?>
					<tr>
						<td><a href="javascript:backToBarcode();" class="activationMain"><?php echo $val['bcode']; ?></a></td>
						<td  width="500px" style="text-align:left;"><?php echo $val['prod_name'] ?></td>
						<td style="text-align:right;"><?php echo number_format($val['price'],2) ?></td>
						<td style="text-align:right;"><?php echo $val['qty'] ?></td>
						<td  style="text-align:left;"><?php echo $val['unit'] ?></td>
						<td  style="text-align:right;"><?php echo number_format($val['total'],2) ?></td>
						<td  style="text-align:right;display:none;"><?php echo $val['sku'] ?></td>
						<td  style="text-align:right;display:none;"><?php echo $val['cost'] ?></td>
					</tr>
				<?php $xtotal+=$val['total'];$count++;}
			}
		}
		echo "|$xtotal";
	break;
}
//$db->closeDb();
?>