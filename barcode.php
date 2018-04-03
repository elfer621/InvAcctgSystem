<html>
<head>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<script type="text/javascript" src="http://barcode-coder.com/js/jquery-ui-1.7.custom.min.js"></script>
<script type="text/javascript" src="http://barcode-coder.com/dp.SyntaxHighlighter/Scripts/shCore.js"></script>
<script type="text/javascript" src="http://barcode-coder.com/dp.SyntaxHighlighter/Scripts/shBrushXml.js"></script>
<script type="text/javascript" src="http://barcode-coder.com/dp.SyntaxHighlighter/Scripts/shBrushJScript.js"></script>
<script type="text/javascript" src="http://barcode-coder.com/js/jquery-barcode-last.min.js"></script>

<script type="text/javascript">
    $(document).ready(function()
    {
        $("#ean").barcode("9785903979165", "ean13", {barWidth:2});
    });
</script>
<style>
#ean {
    position: absolute;
    left: 0;
    top: 0;
    width: 40mm;
    height: 23mm;
}

</style>
</head>
<body>
<div id="ean">
</div>
</body>
</html>