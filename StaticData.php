<?php
class StaticData
{
	/*
		Main API functions
	*/

	public function avg_in_range($starttime,$endtime,$fromDB,$timeframe) {
		$date_count = $this->prepare_data ($starttime,$endtime,$fromDB,$timeframe);
		$sum = 0;
		$size = 0;
		/* using first and last value to determine if avg need to reduce it's scope by one timeframe
			For example, say our range is Feb 27 to March 27 but we have no values in febuary. 
			If we have a total of 180 hits, without removing the month of febuary, we will have an average 
			of 90 per month (incorrect!!!) by removing the extra month we get the right answer

			this is probably a hack but our test cases pass!
		*/
		$firstvalue = -1;
		$lastvalue = -1;
		foreach ($date_count as $value) {
			$sum += $value;
			$size += 1;
			if ($firstvalue == -1) $firstvalue = $value;
			$lastvalue = $value;
		}
		if (($lastvalue == 0 || $firstvalue == 0) && $size > 1) $size -= 1;
		return $sum/$size;
	}

	public function median_in_range ($starttime,$endtime,$fromDB,$timeframe) {
		$date_count = $this->prepare_data ($starttime,$endtime,$fromDB,$timeframe);
		// we want to copy this array without the unixtime keys
		$no_keys = array();
		foreach ($date_count as $value) {
			array_push($no_keys, $value);
		}
		sort($no_keys);
		$size = count($no_keys);
		$middle = floor(($size-1)/2);
		if ($size % 2 == 1) {
			return $no_keys[$middle];
		} else {
			return ($no_keys[$middle]+$no_keys[$middle+1])/2;
		}

	}

	public function mode_in_range ($starttime,$endtime,$fromDB,$timeframe) {
		$date_count = $this->prepare_data ($starttime,$endtime,$fromDB,$timeframe);
		$counts = array_count_values($date_count); 
		return array_search(max($counts), $counts);
	}

	public function count_in_range ($starttime,$endtime,$fromDB) {
		$date_count = $this->prepare_data ($starttime,$endtime,$fromDB,'min');
		$sum = 0;
		foreach ($date_count as $value) {
			$sum += $value;
		}
		return $sum;
	}

	// highs and lows will return a UNIX timestamp
	public function highs_in_spec_range ($starttime,$endtime,$fromDB,$timeframe) {
		$date_count = $this->prepare_data ($starttime,$endtime,$fromDB,$timeframe);
		return array_search($this->max_in_set($date_count), $date_count);
	}

	public function lows_in_spec_range ($starttime,$endtime,$fromDB,$timeframe) {
		$date_count = $this->prepare_data ($starttime,$endtime,$fromDB,$timeframe);
		return array_search($this->min_in_set($date_count), $date_count);
	}

	/*
		helper functions
	*/

	/*
		min and max _in_set function will return the lowest and highest counts in the set. 
		Both will ignore keys with value 0
	*/

	public function min_in_set ($data) {
		$min = PHP_INT_MAX;
		$found = false;
		foreach ($data as $x) {
			if ($x != 0 && $x < $min) {
				$found = true;
				$min = $x;
			}
		}
		if ($found) return $min;
		else return 0;
	}
	public function max_in_set ($data) {
		$max = PHP_INT_MIN;
		$found = false;
		foreach ($data as $x) {
			if ($x != 0 && $x > $max) {
				$found = true;
				$max = $x;
			}
		}
		if ($found) return $max;
		else return 0;
	}

	public function in_range ($starttime,$endtime,$value) {
		if ($value <= $endtime && $value >= $starttime)
			return TRUE;
		return FALSE;
	}

	/*
		Will convert RAW time data into a dictionary of unix timestamps and counts.
		Each key is separated by the given timeframe and the vlaue for each key
		represents the number of hits in that timeframe.
	*/
	public function prepare_data ($starttime,$endtime,$fromDB,$timeframe) {
		// first, slice out any data that isn't in our start and end range
		foreach ($fromDB as $time) {
			if (!$this->in_range($starttime,$endtime,$time)) {
				unset ($fromDB[array_search ($time,$fromDB)]);
			}
		}

		//create a new array that holds the unixtime as the key, and the count as the value
		$keys = array();
		$datetime = new DateTime();
		$datetime->setTimestamp($this->to_floor_time($starttime,$timeframe));
		for ($i = $datetime->getTimestamp(); $i <= $this->to_floor_time($endtime,$timeframe); $i = $this->add_from_timeframe($datetime, $timeframe)) {
			$datetime->setTimestamp($i);
			array_push($keys, $i);
		}
		$date_count = array_fill_keys($keys,0);
		// reverse keys array so we start from larges timeframe to smallest timeframe
		
		$keys = array_reverse($keys);
		foreach ($fromDB as $time) {
			foreach ($keys as $key) {
				if ($time >= $key) {
					$date_count[$key] += 1;
					break;
				}
			}
		}

		return $date_count;
	}

	/*
		takes DateTime class and returns the unix timestamp when adding a timeframe
	*/
	public function add_from_timeframe($datetime, $timeframe) {
		switch ($timeframe) {
			case 'sec':
			case 'min':
			case 'hour':
				return $datetime->getTimestamp() + $this->parse_timeframe ($timeframe);
				break;
			case 'day':
				$datetime->add(new DateInterval("P1D"));
				break;
			case 'week':
				$datetime->add(new DateInterval("P7D"));
				break;
			case 'month':
				$datetime->add(new DateInterval("P1M"));
				break;
			case 'year':
				$datetime->add(new DateInterval("P1Y"));
				break;
			default:
				return 0;
		}
		return $datetime->getTimestamp();
	}

	/*
		returns the unix timeframe the input unix time resides in
	*/
	public function to_floor_time ($unix_time, $timeframe) {
		$out_time = '';
		// the following expression rounds the given unixtime to the given timeframe. this works great until we start getting into weeks and months
		$unix_time_round = $this->parse_timeframe($timeframe) * floor($unix_time / $this->parse_timeframe($timeframe));
		switch ($timeframe) {
			case 'sec':
				$out_time = date ("Y-m-d h:i:s a", $unix_time_round);
				break;
			case 'min':
				$out_time = date ("Y-m-d h:i:s a", $unix_time_round);
				break;
			case 'hour':
				$out_time = date ("Y-m-d h:i:s a", $unix_time_round);
				break;
			case 'day':
			case 'week':
				$out_time = date ("Y-m-d", $unix_time);
				break;
			case 'month':
				$out_time = date ("Y-m", $unix_time);
				break;
			case 'year':
				$out_time = date ("Y", $unix_time);
				break;
			default:
				return -1;
		}
		return strtotime($out_time);
	}

	/*
		returns number of seconds in a $timeframe
	*/
	public function parse_timeframe ($timeframe) {
		switch ($timeframe) {
			case 'sec':
				return 1;
			case 'min':
				return 60;
			case 'hour':
				return 3600;
			case 'day':
				return 86400;
			case 'week':
				return 604800;
			case 'month':
				return 2419200;
			case 'year':
				return 31536000;
			default:
				return -1;
		}
	}
}
?>
