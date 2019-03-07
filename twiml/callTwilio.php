<?php

function send_sms() {
    $base_url = "http://35.203.47.37";

    $sid = 'AC2da3b84b65b63ccf4f05c27ac1713060';
    $token = '342a214ee959d16bf97ea87579016762';
    $twilio_number = "+16475607989";
    $to_number = "+919998207084";
    $url = "$base_url/twiml/playfile.xml";
    $uri = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Calls.json';
    $auth = $sid . ':' . $token;
    $fields = '&Url=' . urlencode($url) .
            '&To=' . urlencode($to_number) .
            '&From=' . urlencode($twilio_number);
    $res = curl_init($uri);
    curl_setopt($res, CURLOPT_URL, $uri);
    curl_setopt($res, CURLOPT_POST, 3);
    curl_setopt($res, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($res, CURLOPT_USERPWD, $auth);
    curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($res);
    $res = json_decode($result);
    return $res;
}

$data = send_sms($res);
echo "<pre>";
print_r($data);
?>