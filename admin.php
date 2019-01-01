<html>
<head>
<?php
	require_once('Forms.php');
	require_once('Display.php');
?>
</head>
<body>
	<p>
		Welcome to the admin panel. If you are not an administrator please turn off your monitor and await Class-A amnestic exposure. <br>
	</p>
<?php 
	$forms = new Forms();
	$display = new Display();
	if (isset($_POST["delete_row"])) {
		$display->adminDeleteRow($_POST["delete_row"]);
	}
	$forms->defaultForms();
	if (!empty($_GET)) {
		$display->showTable($forms->start, $forms->end, $forms->frame, 1);
	}
	echo 'PHP version '.phpversion();
?>
</body>
</html>
