<?php
// $qry = mysql_query($sql);
// if(!$qry){
	// echo mysql_error();
// }	
?>
<table class="navigateable" id="mytbl" style="width:100%;">
	
	<tbody>
		<?php 	
		$dr=0;$cr=0;
		while($row = mysql_fetch_assoc($qry)){ ?>
		<tr>
			<td><a href="javascript:viewRefid(<?= $row['refid']?>,'<?=$row['type']?>')" title="View Details"><?= $row['refid']?></a></td>
			<td><?= $row['date']?></td>
			<td><?= $row['account_code']?></td>
			<td><?= $row['account_desc']?></td>
			<td><?= $row['dr']==0?"":number_format($row['dr'],2)?></td>
			<td><?= $row['cr']==0?"":number_format($row['cr'],2) ?></td>
		</tr>
		<?php $dr+=$row['dr'];$cr+=$row['cr'];} ?>
	</tbody>
	<thead>
		<tr style="border-top:1px solid #000;border-bottom:1px solid #000;">
			<th style="border:none;" colspan="4" >Total</th>
			<th style="border:none;"><?= number_format($dr,2)?></th>
			<th style="border:none;"><?= number_format($cr,2)?></th>
		</tr>
		<tr style="border-top:1px solid #000;border-bottom:1px solid #000;">
			<th style="border:none;" >Ref#</th>
			<th style="border:none;" >Date</th>
			<th style="border:none;" >Account Code</th>
			<th style="border:none;width:300px;">Account Desc</th>
			<th style="border:none;">Debit</th>
			<th style="border:none;">Credit</th>
		</tr>
	</thead>
</table>