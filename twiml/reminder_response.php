<?php

require_once 'vendor/autoload.php';

use Twilio\TwiML\VoiceResponse;

$base_url = "http://35.203.47.37";


echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
if ($_REQUEST['Digits'] == 1) {
    echo "<Response><Say voice='Polly.Joanna'>Thank you, your appointment has been confirmed </Say></Response>";
} elseif ($_REQUEST['Digits'] == 2) {
    echo "<Response><Say voice='Polly.Joanna'>Thank you, the clinic has been notified and will be in touch shortly</Say></Response>";
} elseif ($_REQUEST['Digits'] == 3) {
    echo "<Response><Redirect method='GET'>
$base_url/twiml/reminder_allhandle.php?pname=" . urlencode($_GET['pname']) . "&amp;pvname=" . urlencode($_GET['pvname']) . "&amp;cname=" . urlencode($_GET['cname']) . "&amp;aDate=" . urlencode($_GET['aDate']) . "&amp;aTime=" . urlencode($_GET['aTime']) . "&amp;address=" . urlencode($_GET['address']) . "</Redirect></Response>";
} else {
    echo "<Response><Say voice='Polly.Joanna' >You entered wrong digit</Say></Response>";
}

try {

    $params = array(
        'data' => $_REQUEST["Digits"],
        'to' => $_REQUEST['To']
    );

    $defaults = array(
        CURLOPT_URL => "$base_url/efax/call_handle",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params)
    );
    $ch = curl_init();
    curl_setopt_array($ch, $defaults);

    curl_exec($ch);
    curl_close($ch);

    $myFile = "testFile.txt";
    $fh = fopen($myFile, 'a') or die("can't open file");
    $stringData = "Data saving from response ===> " . json_encode($_REQUEST);
    fwrite($fh, $stringData);
    fclose($fh);
} catch (Exception $e) {
    $myFile = "testFile.txt";
    $fh = fopen($myFile, 'a') or die("can't open file");
    $stringData = "Error in call handle" . $e->getMessage();
    fwrite($fh, $stringData);
    fclose($fh);
}


// $postData = array(
//     "data" => $_REQUEST['Digits']
// );
// $url = "http://dev.blockhealth.co/adi-dev/bh_fax/efax/call_handle";
// set_time_limit(300);
// $ch = curl_init($url);
// curl_setopt($ch, CURLOPT_POST, 1);
// // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
// // curl_setopt($ch, CURLOPT_TIMEOUT, 60);
// curl_exec($ch);
// // $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
// curl_close($ch);
// if ($status != 200) {
//     // handle API error...
//     // custom_log("API Error" . $status);
//     // return false;
// }
?>