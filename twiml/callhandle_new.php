<?php

require_once 'vendor/autoload.php';

use Twilio\TwiML\VoiceResponse;

if (isset($_GET['aDate'])) {
    $time = strtotime($_GET['aDate']);
    $month = date("F", $time);
    $year = date("Y", $time);
    $day = date("d", $time);
}
$address = $_GET['address'];
$dataarray = http_build_query($_GET);
$base_url = "http://35.203.47.37";

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<Response>";
echo "<Gather  timeout='3' numDigits='1' action='$base_url/twiml/callhandle.php?pname=" . urlencode($_GET['pname']) . "&amp;patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;pvname=" . urlencode($_GET['pvname']) . "&amp;cname=" . urlencode($_GET['cname']) . "&amp;aDate=" . urlencode($_GET['aDate']) . "&amp;aTime=" . urlencode($_GET['aTime']) . "&amp;address=" . urlencode($_GET['address']) . "' method='GET'>";
echo "<Say  voice='Polly.Joanna'>Hello</Say>";
echo "<Pause length='1'/>";
echo "<Say voice='Polly.Joanna'>This is an automated appointment call for  " . $_GET['pname'] . "  " . $_GET['patient_lname'] . ".</Say>";
echo "<Pause length='1'/>";
echo "<Say voice='Polly.Joanna'>If you are  " . $_GET['pname'] . "  " . $_GET['patient_lname'] . " , please enter 1 to continue. If this is the wrong number, please type 2 to end the call</Say>";
echo "</Gather>";
echo "<Pause length='10'/>";
echo "<Redirect method='GET'>
$base_url/twiml/callhandle_new.php?pname=" . urlencode($_GET['pname']) . "&amp;patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;pvname=" . urlencode($_GET['pvname']) . "&amp;cname=" . urlencode($_GET['cname']) . "&amp;aDate=" . urlencode($_GET['aDate']) . "&amp;aTime=" . urlencode($_GET['aTime']) . "&amp;address=" . urlencode($_GET['address']) . "&amp;Digits=timeout</Redirect>";
echo "</Response>";
?>