<html>
<head>
<!--
** File:    rangeStats.php
** Project: CSCE 315 Project 1, Spring 2018
** Date:    3/28/2018
** Section: 505
-->

</head>
<body>


<h1>Traffic Prediction</h1>
<?php
$debug=false;
include("CommonMethods.php");
$COMMON = new Common($debug);

$time_start = $_POST['time_start'];
$time_end = $_POST['time_end'];
if($time_end<$time_start){
  echo "Invalid time range.";
}
else{
  $sql = "SELECT COUNT(*) as total FROM `Traffic` WHERE `date` BETWEEN '2018-04-15 ".$time_start."' AND '2018-04-15 ".$time_end."'";
  $rs = $COMMON->executeQuery($sql, $_SERVER['SCRIPT_NAME']);

  $row = $rs->fetch();
  $count1=$row['total'];

  $sql = "SELECT COUNT(*) as total FROM `Traffic` WHERE `date` BETWEEN '2018-04-16 ".$time_start."' AND '2018-04-16 ".$time_end."'";
  $rs = $COMMON->executeQuery($sql, $_SERVER['SCRIPT_NAME']);

  $row = $rs->fetch();
  $count2=$row['total'];

  $sql = "SELECT COUNT(*) as total FROM `Traffic` WHERE `date` BETWEEN '2018-04-17 ".$time_start."' AND '2018-04-17 ".$time_end."'";
  $rs = $COMMON->executeQuery($sql, $_SERVER['SCRIPT_NAME']);

  $row = $rs->fetch();
  $count3=$row['total'];

  $sql = "SELECT COUNT(*) as total FROM `Traffic` WHERE `date` BETWEEN '2018-04-18 ".$time_start."' AND '2018-04-18 ".$time_end."'";
  $rs = $COMMON->executeQuery($sql, $_SERVER['SCRIPT_NAME']);

  $row = $rs->fetch();
  $count4=$row['total'];

  $sql = "SELECT COUNT(*) as total FROM `Traffic` WHERE `date` BETWEEN '2018-04-19 ".$time_start."' AND '2018-04-19 ".$time_end."'";
  $rs = $COMMON->executeQuery($sql, $_SERVER['SCRIPT_NAME']);

  $row = $rs->fetch();
  $count5=$row['total'];

  $sql = "SELECT COUNT(*) as total FROM `Traffic` WHERE `date` BETWEEN '2018-04-20 ".$time_start."' AND '2018-04-20 ".$time_end."'";
  $rs = $COMMON->executeQuery($sql, $_SERVER['SCRIPT_NAME']);

  $row = $rs->fetch();
  $count6=$row['total'];

  $sql = "SELECT COUNT(*) as total FROM `Traffic` WHERE `date` BETWEEN '2018-04-21 ".$time_start."' AND '2018-04-21 ".$time_end."'";
  $rs = $COMMON->executeQuery($sql, $_SERVER['SCRIPT_NAME']);

  $row = $rs->fetch();
  $count7=$row['total'];

  $weighted_avg = $count7*7/28 + $count6*6/28 + $count5*5/28 + $count4*4/28 + $count3*3/28 + $count2*2/28 + $count1*1/28;
  echo "We expect there to be ".$weighted_avg." passes from ".$time_start." to ".$time_end."<br>";

  $avg = ($count7 + $count6 + $count5 + $count4 + $count3 + $count2 + $count1)/7;
  $percent = $weighted_avg*100/$avg;
  if($percent>=100){
    $percent=$percent-100;
    echo "This is a ".$percent."% increase.";
  }
  else{
    $percent=100-$percent;
    echo "This is a ".$percent."% decrease.";
  }
  
}
?>
<form action='index.php' method='post' name='home'>
  <input type='Submit' value ='Return Home'>
  </form>

</body>