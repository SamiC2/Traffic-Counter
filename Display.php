<?php

require_once('DBLogin.php');
require_once('Query.php');
require_once('GraphBuilder.php');
require_once('StaticData.php');

class Display
{
	private $min_unixtime = 0;
	private $max_unixtime = 2147483647;
	private $query;
	private $graph;
	function __construct() {
        $this->query = new Query();
        $this->graph = new GraphBuilder();
        $this->static_data = new StaticData();
    }

	/*
		HTML functions
		These functions output HTML directly to the page, thus cannot be used with Unit Tests
	*/

	// This function just spits out the table as is in the database
	public function showTable($starttime, $endtime, $timeframe, $admin = 0) {
		if ($starttime == 'min') $starttime = $this->min_unixtime;
		if ($endtime == 'max') $endtime = $this->max_unixtime;
		$result = $this->query->getResult($starttime, $endtime);
		if ($result == "" || $result->rowCount() == 0) {
			echo "No data in Range.";
		} else {
			// display the table
			echo '<table>';
			echo $this->showTableRow(array('count', 'date', 'confidence', 'location', 'device_id', ''));
			$table_rows = array();
			while($row = $result->fetch()) {
				$s = '';
				$s = $s.'<tr>';
	        	$s = $s.$this->showTableRow(array($row['count'], $row['date'], $row['confidence'], $row['location'], $row['device_id'], ($admin == 1 ? $this->adminDeleteButton($row['count']) : '')));
	        	$s = $s.'</tr>';
	        	array_push($table_rows, $s);
	    	}
	    	$table_rows = array_reverse($table_rows);
	    	foreach ($table_rows as $table_row) {
	    		echo $table_row;
	    	}
			echo '</table>';
		}
	}

	// This function will display the basic avg, count, high, and lows for the given range and timeframe
	public function showStaticData ($starttime, $endtime, $timeframe) {
		if ($starttime == 'min') $starttime = $this->min_unixtime;
		if ($endtime == 'max') $endtime = $this->max_unixtime;
		$result = $this->query->getResult($starttime, $endtime);
		$data = array();
		while($row = $result->fetch()) {
			array_push($data, strtotime($row['date']));
	    }

	    // If SHOW ALL was slected, clamp start and end times to the lowest and highest dates given from the data
	    if ($starttime == $this->min_unixtime) $starttime = min($data);
	    if ($endtime == $this->max_unixtime) $endtime = max($data);

	    $avg = $this->static_data->avg_in_range($starttime, $endtime, $data, $timeframe);
	    $median = $this->static_data->median_in_range($starttime, $endtime, $data, $timeframe);
	    $mode = $this->static_data->mode_in_range($starttime, $endtime, $data, $timeframe);
	    $count = $this->static_data->count_in_range($starttime, $endtime, $data);
	    $high = $this->static_data->highs_in_spec_range($starttime, $endtime, $data, $timeframe);
	    $low = $this->static_data->lows_in_spec_range($starttime, $endtime, $data, $timeframe);

	    echo "<p>";
	    echo "Average of traffic is ".$avg." people per $timeframe. <br>";
	    echo "Median of traffic is ".$median." people per $timeframe. <br>";
	    echo "Mode of traffic is ".$mode." people per $timeframe. <br>";
	    echo "Total traffic in specified range is ".$count.". <br>";
	    switch ($timeframe) {
			case 'min':
				echo "Most traffic in a minute was at ".date('h:i a \o\n M d, Y', $high)."<br>";
	    		echo "Least traffic in a minute was at ".date('h:i a \o\n M d, Y', $low)."<br>";
	    		break;
			case 'hour':
				echo "Most traffic in an $timeframe was at ".date('h:i a \o\n M d, Y', $high)."<br>";
	    		echo "Least traffic in an $timeframe was at ".date('h:i a \o\n M d, Y', $low)."<br>";
	    		break;
			case 'day':case 'week':
				echo "Most traffic in a $timeframe was on ".date('M d, Y', $high)."<br>";
	    		echo "Least traffic in a $timeframe was on ".date('M d, Y', $low)."<br>";
	    		break;
			case 'month':
				echo "Most traffic in a $timeframe was in ".date('F Y', $high)."<br>";
	    		echo "Least traffic in a $timeframe was in ".date('F Y', $low)."<br>";
	    		break;
			case 'year':
				echo "Most traffic in a $timeframe was in ".date('Y', $high)."<br>";
	    		echo "Least traffic in a $timeframe was in ".date('Y', $low)."<br>";
	    		break;
			default:
				// we're using a drop down, how do they even get here???
				echo "Timeframe Specified not valid.";
		}
	    echo "</p>";
	}

	public function showGraph ($starttime, $endtime, $timeframe) {
		if ($starttime == 'min') $starttime = $this->min_unixtime;
		if ($endtime == 'max') $endtime = $this->max_unixtime;
		$result = $this->query->getResult($starttime, $endtime);
		$this->graph->showGraph($starttime, $endtime, $result, $timeframe);
	}

	/*
		helper functions
	*/

	public function showTableRow ($arr) {
		$s = '';
		foreach ($arr as $cell) {
			$s = $s.'<th>'.$cell.'</th>';
		}
		return $s;
	}

	public function adminDeleteButton ($count) {
		$button = '<form method="post"><input type="hidden" name="delete_row" value="'.$count.'"><input type="submit" value="DELETE"></form>';
		return $button;
	}

	public function adminDeleteRow ($count) {
		echo "<p>";
		$this->query->deleteRowCount($count);
		echo "<br>";
		echo "</p>";
	}
}
?>
