<?php

/* echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  echo "<Response>";
  echo "<Gather  NumDigits='1' action='http://dev.blockhealth.co/adi-dev/bh_fax/twiml/response.php' method='GET'>";
  echo "<Say voice='woman'><prosody rate='85%' volume='-6dB'>";
  echo  "Hello         ".$_GET['pname']."
  Your      appointment   ".$_GET['pvname']."    with    ".$_GET['cname']."    has been booked for <say-as interpret-as='date' format='dmy'>".$_GET['aDate']." </say-as>   at   <say-as interpret-as='date' format='dmy'> ".$_GET['aDate']." </say-as>   .The    address    is:    ".$_GET['address']."    Please     type    1    to    confirm    this    booking.    If    this    date    does   not     work,    please   type   2    to    alert    the    clinic    staff";
  echo "</prosody></Say>";
  echo "</Gather>";

  echo "</Response>"; */

/*
  require_once 'vendor/autoload.php';
  use Twilio\TwiML\VoiceResponse;

  $response = new VoiceResponse();
  $say = $response->say('Hello   '.$_GET['pname'], ['voice' => 'Polly.Joanna']);
  $say->break_(['strength' => 'x-weak', 'time' => '100ms']);
  $say->p('Your  appointment   '.$_GET['pvname'].'    with');

  $say->s($_GET['cname']);
  $say->p('has been booked for');
  $say->say_as($_GET['aDate'], ['interpret-as' => 'date','format'=>'dmy']);
  $say->s('at');
  $say->say_as($_GET['aDate'], ['interpret-as' => 'time','format'=>'hms24']);
  $say->break_(['strength' => 'x-weak', 'time' => '100ms']);
  $say->w('The    address    is:    '.$_GET['address'].'    Please     type    1    to    confirm    this    booking.    If    this    date    does   not     work,    please   type   2    to    alert    the    clinic    staff');

  echo $response; */

require_once 'vendor/autoload.php';

use Twilio\TwiML\VoiceResponse;

if (isset($_GET['aDate'])) {
    $time = strtotime($_GET['aDate']);
    $month = date("F", $time);
    $year = date("Y", $time);
    $day = date("d", $time);
}
$address = $_GET['address'];
/*  $msg = "Hello <emphasis level='moderate'><patient name></emphasis><break time='200ms'/>," .
  "\n" .
  "Your appointment   <emphasis level='moderate'><patient visit name></emphasis>  with <emphasis level='moderate'><clinic name></emphasis>  has been booked for  <say-as interpret-as='date' format='ddmmyyyy'> <date> </say-as>    at    <say-as interpret-as='time' format='hms12'> <time> </say-as><break time='200ms'/>" .
  "The address is:   \n" .
  "<emphasis level='moderate'><Address> </emphasis><break time='200ms'/>" .
  "Please type   <emphasis level='moderate'>1</emphasis>   to  confirm this booking. If this date does not work, please type   <emphasis level='moderate'>2</emphasis>    to alert the clinic staff.";
  $msg = str_replace("<patient name>", $_GET['pname'], $msg);
  $msg = str_replace("<date>", $_GET['aDate'], $msg);
  $msg = str_replace("<time>", $_GET['aTime'], $msg);
  $msg = str_replace("<patient visit name>", $_GET['pvname'], $msg);
  $msg = str_replace("<clinic name>", $_GET['cname'], $msg);
  $msg = str_replace("<Address>", $_GET['address'], $msg);


  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
  echo "<Response>";
  echo "<Gather NumDigits='1' action='http://dev.blockhealth.co/adi-dev/bh_fax/twiml/response.php' method='GET'>";
  echo "<Say  voice='woman' >".$msg."</Say>";
  echo "</Gather>";
  echo "</Response>"; */
/* $array = array('pname'=> $_GET['pname'].'&','pvname'=> $_GET['pvname'].'&',''=> $_GET['cname'].'&','aDate'=> $_GET['aDate'].'&','aTime'=> $_GET['aTime'].'&','address'=> $_GET['address']); */
$dataarray = http_build_query($_GET);


// $myFile = "testFile.txt";
// $fh = fopen($myFile, 'a') or die("can't open file");
// $stringData = "New Stuff 1\n";
// fwrite($fh, $stringData);
// $stringData = "New Stuff 2\n";
// fwrite($fh, $stringData);
// $stringData = "New Stuff 2\n";
// fwrite($fh, json_encode($dataarray));
// fclose($fh);




echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<Response>";
// echo "<Gather  timeout='2' NumDigits='1' action='http://dev.blockhealth.co/adi-dev/bh_fax/twiml/reminder_response.php?pname=" . urlencode($_GET['pname']) . "&amp;pvname=" . urlencode($_GET['pvname']) . "&amp;cname=" . urlencode($_GET['cname']) . "&amp;aDate=" . urlencode($_GET['aDate']) . "&amp;aTime=" . urlencode($_GET['aTime']) . "&amp;address=" . urlencode($_GET['address']) . "' method='GET'>";


echo "<Say  voice='Polly.Joanna'>Hello " . $_GET['pname'] . ",</Say>";
echo "<Pause length='1'/>";
// echo "<Say voice='Polly.Joanna'>Your appointment " . $_GET['pvname'] . " with  " . $_GET['cname'] . "  has been booked for  <say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $_GET['aDate'] . "</say-as>     at   <say-as interpret-as='time' format='hms12'> " . $_GET['aTime'] . " </say-as></Say>";
echo "<Say voice='Polly.Joanna'>Your appointment with  " . $_GET['cname'] . "   is tomorrow  <say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $_GET['aDate'] . "</say-as>     at   <say-as interpret-as='time' format='hms12'> " . $_GET['aTime'] . " </say-as></Say>";
echo "<Pause length='1'/>";
echo "<Say voice='Polly.Joanna'>The address  is: " . $_GET['address'] . "  </Say>";
echo "<Pause length='1'/>";
echo "<Say voice='Polly.Joanna'>Please make sure to arrive on time</Say>";
// echo "<Say voice='Polly.Joanna'>Please type 3 to replay this message</Say>";
// echo "</Gather>";
// echo "<Pause length='10'/>";
// echo "<Redirect method='GET'>
http://dev.blockhealth.co/adi-dev/bh_fax/twiml/reminder_callhandle.php?pname=" . urlencode($_GET['pname']) . "&amp;pvname=" . urlencode($_GET['pvname']) . "&amp;cname=" . urlencode($_GET['cname']) . "&amp;aDate=" . urlencode($_GET['aDate']) . "&amp;aTime=" . urlencode($_GET['aTime']) . "&amp;address=" . urlencode($_GET['address']) . "&amp;Digits=timeout</Redirect>";
echo "</Response>";
?>