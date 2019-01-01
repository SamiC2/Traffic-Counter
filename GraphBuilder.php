<?php
require_once('StaticData.php');
require_once('Display.php');

class GraphBuilder 
{
	private $graph;
	function __construct() {
        $this->static_data = new StaticData();
    }	

	public function showGraph($starttime, $endtime, $query_result, $timeframe) {

		// the query grab the data needed in the provided timeframe,
		// so extract each row

		$data = array();
		while($row = $query_result->fetch()) {
			array_push($data, strtotime($row['date']));
	    }

		$date_count = $this->static_data->prepare_data($starttime,$endtime,$data,$timeframe);

		$date = array_keys($date_count);
		$totalPasses = array_values($date_count);

		//$table = array($date, $totalPasses);
		$table = array();
		$date_format = "";
		switch ($timeframe) {
			case 'min':	case 'hour':
				$date_format = 'H:i M d, Y';
	    		break;
			case 'day':case 'week':
				$date_format = 'M d, Y';
	    		break;
			case 'month':
				$date_format = 'M Y';
	    		break;
			case 'year':
				$date_format = 'Y';
	    		break;
		}
		for ($i = 0; $i < count($date); $i++) {
			array_push($table, array(date($date_format, $date[$i]), $totalPasses[$i]));
		}

	    $jsonTable = json_encode($table, JSON_NUMERIC_CHECK);

		echo "<html>";
		echo "<head>";

		$load = $this -> loadGoogleCharts();

		# drawChart() method for JavaScript()
		echo "function drawChart() {";

			echo "var data = new google.visualization.DataTable($jsonTable);";

			echo "data.addColumn('string', 'Date');";
			echo "data.addColumn('number', 'Number of Passes');";
			echo "data.addRows($jsonTable);";

			echo "var options = {
	          chart: {
	            title: 'Number of Passes at the Rec Center'
	          },			
	          width: 800,
	          height: 500,
	          series: {
	          	0: {axis: 'number of passes'}
	          },

	          axes: {
	            y: {
	            	'number of passes': {label: 'Number of Passes'}
	            }
	          }
		};";

		#instantiate and draw chart

		echo "var chart = new google.charts.Scatter(document.getElementById('scatterplot'));";

		echo "chart.draw(data, google.charts.Scatter.convertOptions(options));";

		echo "}"; // end drawChart()

		#close script tag
		echo "</script>";
		echo "</head>";

		echo '<div id="scatterplot"></div>';
	    
	} // end showGraph()

	# javaScript code within HTML
	public function loadGoogleCharts() {
		echo "<script type='text/javascript' src='https://www.gstatic.com/charts/loader.js'></script>";
		echo "<script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js'></script>";

		# open script tag
		echo "<script type='text/javascript'>";

		#load scatter plot package

		echo "google.charts.load('current', {'packages':['corechart', 'scatter']});";

		echo "google.charts.setOnLoadCallback(drawChart);";		

		return 0;
	}

} // end of class
?>