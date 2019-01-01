<?php
date_default_timezone_set('America/Chicago');

class Forms
{
	public $start;
	public $end;
	public $frame;
	private $timeframes = array('min', 'hour', 'day', 'week', 'month', 'year');
	function defaultValues () {
		// default range is 30 days ago to right now
		$this->start = (time() - 2592000);
		$this->end = time();
		$this->frame = 'hour';
	}
	function __construct() {
		if (empty($_GET)) {
			$this->defaultValues ();
		} else if (isset($_GET['reset'])) {
			$this->defaultValues ();
			header('Location: '.$_SERVER['PHP_SELF']);
			exit;
		} else if (isset($_GET['show_all'])) {
			$this->start = 'min';
			$this->end = 'max';
			$this->frame = $_GET["range_frame"];
		} else {
			// check to make sure user hasnt selected min or max and then set the start and end times
			if ($_GET["range_start"] != 'min') {
				$this->start = $this->stringToDate($_GET["range_start"]);
			} else {
				$this->start = 'min';
			}
			if ($_GET["range_end"] != 'max') {
				$this->end = $this->stringToDate($_GET["range_end"]);
			} else {
				$this->end = 'max';
			}
			$this->frame = $_GET["range_frame"];
		}
		if ($this->start == -1 || $this->end == -1) {
			echo "Submitted dates were invalid.";
			$this->defaultValues ();
		}
    }

    public function stringToDate ($input) {
    	/*
			These are the common date formats we came up with. 
			We expect the user to stick to the format provided when first loading the page.
			Just incase the user wants to try out other formats they can do so.
			If you want to add other formats (or automate the process of filling $date_format_queue)
			you can find more information in the php documentation.

			http://php.net/manual/en/datetime.createfromformat.php
    	*/
    	$date_format_queue = array('m#d#y| e', 'm#d#y| g:i:s |e', 'm#d#y| g:i', 'm#d#y|', 'y M j| e', 'm#d#Y| e', 'm#d#Y| g:i:s |e', 'm#d#Y g:i', 'm#d#Y|', 'Y M j| e');
    	// First, try to parse the given time using the $date_format_queue
    	foreach ($date_format_queue as $format) {
    		if (($try_time = DateTime::createFromFormat($format, $input)) != FALSE){
    			return $try_time->getTimestamp();
    		}
    	}
    	//If that fails, try a simple strtotime as a backup
    	if (($out = strtotime($input)) === false) {
    		return -1;
    	} else {
			return strtotime($input);
		}
	}

	public function dateToString ($input) {
		return date('M d, Y H:i:s', $input);
	}

	/*
		HTML functions
		These functions output HTML directly to the page, thus cannot be used with Unit Tests
	*/

	// This function lays out the default form that is used to specify data range and timeframe
	public function defaultForms() {
		$timeframes = $this->timeframes;
		echo '<form>';
		echo 'Select data range from <input type="text" name="range_start" value="'.($this->start == 'min' ? 'min' : $this->dateToString($this->start)).'"> to ';
		echo '<input type="text" name="range_end" value="'.($this->end == 'max' ? 'max' : $this->dateToString($this->end)).'"> per ';
		echo '<select name="range_frame">';
		foreach ($timeframes as $timeframe) {
			echo '<option value="'.$timeframe.'"'.($this->frame == $timeframe ? ' selected' : '').'>'.$timeframe.'</option>';
		} 
		echo '</select> for <select name="lot"> <option value="lot54">Lot 54</option> </select> <br>';
  		echo'<input type="submit" value="Submit"><input type="submit" name="show_all" value="Show All"><input type="submit" name="reset" value="Reset">';
		echo'</form>';
	}
}
?>
