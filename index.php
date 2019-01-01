<html>
<head>
<?php
	require_once('Forms.php');
	require_once('Display.php');
?>

</head>
<body>
<?php 
	$forms = new Forms();
	$display = new Display();
	$forms->defaultForms();
	if (!empty($_GET)) {
		// data is passed let's display it
		$display->showStaticData($forms->start, $forms->end, $forms->frame);
		echo "<br>";
		$display->showGraph($forms->start, $forms->end, $forms->frame);
	} else {
		// no data yet
	}
?>

<form action="predict.php" method='post'>
  Or view a predictive model for 
  <select name="lot">
    <option value="lot54">Lot 54</option>
  </select>
  for the times 
  <input type="time" name="time_start" required>
  to
  <input type="time" name="time_end" required>
  <br>
  <input type="submit">
</form>
</body>
</html>
