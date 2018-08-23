<!DOCTYPE html>
<html lang="en">
<head>

    <title>DemoGrid</title>
    <meta name="author" content="Edgar">
	
    <!--link rel="stylesheet" type="text/css" media="screen" href="js/jquery_ui/css/smoothness/jquery-ui-1.8.16.custom.css" /--> 
     <link rel="stylesheet" type="text/css" media="screen" href="js/jquery_ui/css/redmond/jquery-ui-1.9.2.custom.css" /> 
    <link rel="stylesheet" type="text/css" media="screen" href="js/jqGrid/ui.multiselect.css">
    <link rel="stylesheet" href="js/jqGrid/ui.jqgrid.css">
    <link rel="stylesheet" href="js/jqGrid/font/css/font-awesome.css">
	
	<style>
        html, body { font-size: 75%; }
        .ui-datepicker select.ui-datepicker-year,
        .ui-datepicker select.ui-datepicker-month {
            color: black
        }
    </style>

	
<script type="text/javascript" src="js/jquery/jquery-1.7.js"></script>
<script type="text/javascript" src="js/jquery_ui/js/jquery-ui-1.8.16.custom.min.js"></script>
<script src="js/jqGrid/ui.multiselect.js" type="text/javascript"></script> 
	
<script>
	$.jgrid = $.jgrid || {};
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;
</script>

<script src="js/jqGrid.js"></script>


<script>
$(document).ready(function(){
	$("#mygrid").load("gridDemoProcesa.php");
	$("#btnLoad").click(function(){
		$("#mygrid").load("gridDemoProcesa.php");
	});
});
</script>

</head>
<body>
<button id="btnLoad">Load</button>

<div id="mygrid"></div>

</body>
</html>