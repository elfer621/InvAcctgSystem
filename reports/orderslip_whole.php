<html>
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
	<style type="text/css">
		body {
			width: 8.5in;
			height: 11in;
		}
		@page {
			size: A4;
			margin: 0;
		}
		/*@media print {
		  html, body {
			width: 210mm;
			height: 297mm;
		  }
		}*/
	</style>
	</head>
	<?php
		$id=explode(",",$_REQUEST['ids']);
	?>
	<body>
		<table cellspacing="0" cellpadding="0" border="0" width="100%" height="100%" >
			<tr>
				<td><iframe src="orderslip.php?id=<?=$id[0]?>" style="height:100%;width:100%;" frameBorder="0" scrolling="no"></iframe></td>
				<td style="width:50px;">&nbsp;</td>
				<td><iframe src="orderslip.php?id=<?=$id[1]?$id[1]:$id[0]?>" style="height:100%;width:100%;" frameBorder="0" scrolling="no" ></iframe></td>
			</tr>
			<tr>
				<td><iframe src="orderslip.php?id=<?=$id[2]?$id[2]:$id[0]?>" style="height:100%;width:100%;" frameBorder="0" scrolling="no"></iframe></td>
				<td>&nbsp;</td>
				<td><iframe src="orderslip.php?id=<?=$id[3]?$id[3]:$id[0]?>" style="height:100%;width:100%;" frameBorder="0" scrolling="no" ></iframe></td>
			</tr>
		</table>
	</body>
	<script>
	 function resizeIframe(obj) {
		obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	  }
	</script>
</html>