<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Call_view extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
    }

    public function index() {
        $this->load->view('call_view');
    }

    public function call() {
        $data = $this->input->post();
//        
//        $first_name = $data["first_name"];
//        $last_name = $data["last_name"];
//        $visit_name = $data["visit_name"];
//        $clinic_name = $data["clinic_name"];
//        $contact_number = $data["contact_number"];
//        $address = $data["address"];
//
//        $reservation_id = $data['reservation_id'];
//
//        $data['speak_date1'] = $data['date1'];
//        $data['speak_time1'] = $data['time1'];
//        $data['speak_date2'] = $data['date2'];
//        $data['speak_time2'] = $data['time2'];
//        $data['speak_date3'] = $data['date3'];
//        $data['speak_time3'] = $data['time3'];
//        $speak_date1 = $data['speak_date1'];
//        $speak_day1 = $data['speak_day1'];
//        $speak_time1 = $data['speak_time1'];
//        $speak_date2 = $data['speak_date2'];
//        $speak_day2 = $data['speak_day2'];
//        $speak_time2 = $data['speak_time2'];
//        $speak_date3 = $data['speak_date3'];
//        $speak_day3 = $data['speak_day3'];
//        $speak_time3 = $data['speak_time3'];
    }

    public function call_start() {
        //$data = $_REQUEST;
        $data = '{"first_name":"Lisa","last_name":"Roy","visit_name":"rose wood","clinic_name":"Test Clinic","address":"1 0 1 0 EASY ST, OTTAWA, ONTARIO, K1A 0B1","reservation_id":"0","date1":"2019-02-07","time1":"10:00:00","date2":"2019-02-08","time2":"10:00:00","date3":"2019-02-11","time3":"10:00:00","speak_date1":"February 7th","speak_day1":"Thursday","speak_time1":"10:00 am","speak_date2":"February 8th","speak_day2":"Friday","speak_time2":"10:00 am","speak_date3":"February 11th","speak_day3":"Monday","speak_time3":"10:00 am"}';
        echo "\n\nat call_start";
        echo "data = " . json_encode($data);
       
        $sid = 'AC2da3b84b65b63ccf4f05c27ac1713060';
        $token = '342a214ee959d16bf97ea87579016762';
        $twilio_number = "+13658000973";
//        $to = "+919876907251";  
//        $to_number = $data["contact_number"];
        $to_number = "+917201907712";

        $url = base_url() . "call_view/confirm_call?"
                . "first_name=" . urlencode($data["first_name"]) . '&'
                . "last_name=" . urlencode($data["last_name"]) . '&'
                . "visit_name=" . urlencode($data["visit_name"]) . '&'
                . "clinic_name=" . urlencode($data["clinic_name"]) . '&'
                . "address=" . urlencode($data["address"]) . '&'
                . "reservation_id=" . urlencode($data["reservation_id"]) . '&'
                . "date1=" . urlencode($data['date1']) . '&'
                . "time1=" . urlencode($data['time1']) . '&'
                . "date2=" . urlencode($data['date2']) . '&'
                . "time2=" . urlencode($data['time2']) . '&'
                . "date3=" . urlencode($data['date3']) . '&'
                . "time3=" . urlencode($data['time3']) . '&'
                . "speak_date1=" . urlencode($data['speak_date1']) . '&'
                . "speak_day1=" . urlencode($data['speak_day1']) . '&'
                . "speak_time1=" . urlencode($data['speak_time1']) . '&'
                . "speak_date2=" . urlencode($data['speak_date2']) . '&'
                . "speak_day2=" . urlencode($data['speak_day2']) . '&'
                . "speak_time2=" . urlencode($data['speak_time2']) . '&'
                . "speak_date3=" . urlencode($data['speak_date3']) . '&'
                . "speak_day3=" . urlencode($data['speak_day3']) . '&'
                . "speak_time3=" . urlencode($data['speak_time3']);

        $uri = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Calls.json';
        $auth = $sid . ':' . $token;
        $fields = '&Url=' . urlencode($url) .
                '&To=' . urlencode($to_number) .
                '&From=' . urlencode($twilio_number);
        $res = curl_init();
        curl_setopt($res, CURLOPT_URL, $uri);
        curl_setopt($res, CURLOPT_POST, 3);
        curl_setopt($res, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($res, CURLOPT_USERPWD, $auth);
        curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
        curl_exec($res);

        if (curl_errno($res)) {
            log_message("error", " Error – " . curl_error($res));
            echo curl_error($res);
            return false;
        } else {
            log_message("error", "Calling");
            return true;
        }
    }

    public function confirm_call() {
      //  $data = $this->input->get();
        $base_url = base_url();
                $data = '{"first_name":"Lisa","last_name":"Roy","visit_name":"rose wood","clinic_name":"Test Clinic","address":"1 0 1 0 EASY ST, OTTAWA, ONTARIO, K1A 0B1","reservation_id":"0","date1":"2019-02-07","time1":"10:00:00","date2":"2019-02-08","time2":"10:00:00","date3":"2019-02-11","time3":"10:00:00","speak_date1":"February 7th","speak_day1":"Thursday","speak_time1":"10:00 am","speak_date2":"February 8th","speak_day2":"Friday","speak_time2":"10:00 am","speak_date3":"February 11th","speak_day3":"Monday","speak_time3":"10:00 am"}';
        echo "\n\nat confirm_call";
        echo "data = " . json_encode($data);

        $params = "first_name=" . urlencode($data["first_name"]) . '&'
                . "last_name=" . urlencode($data["last_name"]) . '&'
                . "visit_name=" . urlencode($data["visit_name"]) . '&'
                . "clinic_name=" . urlencode($data["clinic_name"]) . '&'
                . "address=" . urlencode($data["address"]) . '&'
                . "reservation_id=" . urlencode($data["reservation_id"]) . '&'
                . "date1=" . urlencode($data['date1']) . '&'
                . "time1=" . urlencode($data['time1']) . '&'
                . "date2=" . urlencode($data['date2']) . '&'
                . "time2=" . urlencode($data['time2']) . '&'
                . "date3=" . urlencode($data['date3']) . '&'
                . "time3=" . urlencode($data['time3']) . '&'
                . "speak_date1=" . urlencode($data['speak_date1']) . '&'
                . "speak_day1=" . urlencode($data['speak_day1']) . '&'
                . "speak_time1=" . urlencode($data['speak_time1']) . '&'
                . "speak_date2=" . urlencode($data['speak_date2']) . '&'
                . "speak_day2=" . urlencode($data['speak_day2']) . '&'
                . "speak_time2=" . urlencode($data['speak_time2']) . '&'
                . "speak_date3=" . urlencode($data['speak_date3']) . '&'
                . "speak_day3=" . urlencode($data['speak_day3']) . '&'
                . "speak_time3=" . urlencode($data['speak_time3']);

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>
            <Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_two?"
        . $params . "' method='GET'>
                <Say  voice='Polly.Joanna'> Hello </Say>
                <Pause length='1'/>
                <Say voice='Polly.Joanna'> This is an automated appointment call for  " . $data["first_name"] . "  " . $data["last_name"] . ".</Say>
                <Pause length='1'/>
                <Say voice='Polly.Joanna'> If you are  " . $data["first_name"] . "  " . $data["last_name"] . " , please enter 1 to continue. If this is the wrong number, please type 2 to end the call</Say>
				</Gather>
            <Pause length='10'/>
            <Redirect method='GET'>
            " . $base_url . "call_view/confirm_call?"
        . $params . '&'
        . "Digits=timeout"
        . "</Redirect>"
        . "</Response>";
    }

    public function step_two() {
        //$data = $this->input->get();
        $base_url = base_url();
                $data = '{"first_name":"Lisa","last_name":"Roy","visit_name":"rose wood","clinic_name":"Test Clinic","address":"1 0 1 0 EASY ST, OTTAWA, ONTARIO, K1A 0B1","reservation_id":"0","date1":"2019-02-07","time1":"10:00:00","date2":"2019-02-08","time2":"10:00:00","date3":"2019-02-11","time3":"10:00:00","speak_date1":"February 7th","speak_day1":"Thursday","speak_time1":"10:00 am","speak_date2":"February 8th","speak_day2":"Friday","speak_time2":"10:00 am","speak_date3":"February 11th","speak_day3":"Monday","speak_time3":"10:00 am"}';
        echo "\n\nat step_two";
        echo "data = " . json_encode($data);

        $params = "first_name=" . urlencode($data["first_name"]) . '&'
                . "last_name=" . urlencode($data["last_name"]) . '&'
                . "visit_name=" . urlencode($data["visit_name"]) . '&'
                . "clinic_name=" . urlencode($data["clinic_name"]) . '&'
                . "address=" . urlencode($data["address"]) . '&'
                . "reservation_id=" . urlencode($data["reservation_id"]) . '&'
                . "date1=" . urlencode($data['date1']) . '&'
                . "time1=" . urlencode($data['time1']) . '&'
                . "date2=" . urlencode($data['date2']) . '&'
                . "time2=" . urlencode($data['time2']) . '&'
                . "date3=" . urlencode($data['date3']) . '&'
                . "time3=" . urlencode($data['time3']) . '&'
                . "speak_date1=" . urlencode($data['speak_date1']) . '&'
                . "speak_day1=" . urlencode($data['speak_day1']) . '&'
                . "speak_time1=" . urlencode($data['speak_time1']) . '&'
                . "speak_date2=" . urlencode($data['speak_date2']) . '&'
                . "speak_day2=" . urlencode($data['speak_day2']) . '&'
                . "speak_time2=" . urlencode($data['speak_time2']) . '&'
                . "speak_date3=" . urlencode($data['speak_date3']) . '&'
                . "speak_day3=" . urlencode($data['speak_day3']) . '&'
                . "speak_time3=" . urlencode($data['speak_time3']);

        if ($data['Digits'] == 1) {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response>";
            echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_three?"
            . $params . "' method='GET'>";
            echo "<Say  voice='Polly.Joanna'>Hi  " . $data["first_name"] . ",  Please select one of the following dates and times for your appointment with " . $data["clinic_name"] . "</Say>";
            echo "<Pause length='1'/>";
            echo "<Say  voice='Polly.Joanna'>For <emphasis level='moderate'>" . $data['speak_day1'] . " <say-as interpret-as='date' format='mmyyyy'  detail='1'>" . $data['speak_date1'] . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $data['speak_time1'] . " </say-as></emphasis> - please enter 1  </Say>";
            echo "<Pause length='1'/>";
            echo "<Say  voice='Polly.Joanna'>For <emphasis level='moderate'>" . $data['speak_day2'] . " <say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $data['speak_date2'] . " </say-as>     at  <say-as interpret-as='time' format='hms12'>  " . $data['speak_time2'] . " </say-as></emphasis>  - please enter 2  </Say>";
            echo "<Pause length='1'/>";
            echo "<Say  voice='Polly.Joanna'>For   <emphasis level='moderate'> " . $data['speak_day3'] . " <say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $data['speak_date3'] . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $data['speak_time3'] . " </say-as></emphasis> - please enter 3</Say>";
            echo "<Pause length='1'/>";
            echo "<Say  voice='Polly.Joanna'>If you would like the clinic to contact you directly - please enter 0</Say>";
            echo "<Pause length='1'/>";
            echo "<Say  voice='Polly.Joanna'>To replay this message, please enter 4 </Say>";
            echo "</Gather>";
            echo "<Pause length='2'/>";
            echo "<Redirect method='GET'>" . $base_url . "call_view/step_two?"
            . "Digits=1&"
            . $params
            . "</Redirect>";
            echo "</Response>";
        } elseif ($data["Digits"] == 2) {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response><Say voice='Polly.Joanna'>Thank you</Say></Response>";


            $http_params = array(
                'data' => $data["Digits"],
                'to' => $data["To"]
            );
            $defaults = array(
                CURLOPT_URL => $base_url . "call_view/vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc",
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($http_params)
            );
            $ch = curl_init($base_url . "call_view/vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc");
            curl_setopt_array($ch, $defaults);
            curl_exec($ch);
            curl_close($ch);
        } else {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response><Redirect method='GET'>" . $base_url . "call_view/confirm_call?$params" . '&'
            . "Digits=timeout"
            . "</Redirect>"
            . "</Response>";
        }
    }

    function step_three() {
        //$data = $this->input->get();
        $base_url = base_url();
                $data = '{"first_name":"Lisa","last_name":"Roy","visit_name":"rose wood","clinic_name":"Test Clinic","address":"1 0 1 0 EASY ST, OTTAWA, ONTARIO, K1A 0B1","reservation_id":"0","date1":"2019-02-07","time1":"10:00:00","date2":"2019-02-08","time2":"10:00:00","date3":"2019-02-11","time3":"10:00:00","speak_date1":"February 7th","speak_day1":"Thursday","speak_time1":"10:00 am","speak_date2":"February 8th","speak_day2":"Friday","speak_time2":"10:00 am","speak_date3":"February 11th","speak_day3":"Monday","speak_time3":"10:00 am"}';
        echo "\n\nat step_three";
        echo "data = " . json_encode($data);
        

        $params = "first_name=" . urlencode($data["first_name"]) . '&'
                . "last_name=" . urlencode($data["last_name"]) . '&'
                . "visit_name=" . urlencode($data["visit_name"]) . '&'
                . "clinic_name=" . urlencode($data["clinic_name"]) . '&'
                . "address=" . urlencode($data["address"]) . '&'
                . "reservation_id=" . urlencode($data["reservation_id"]) . '&'
                . "date1=" . urlencode($data['date1']) . '&'
                . "time1=" . urlencode($data['time1']) . '&'
                . "date2=" . urlencode($data['date2']) . '&'
                . "time2=" . urlencode($data['time2']) . '&'
                . "date3=" . urlencode($data['date3']) . '&'
                . "time3=" . urlencode($data['time3']) . '&'
                . "speak_date1=" . urlencode($data['speak_date1']) . '&'
                . "speak_day1=" . urlencode($data['speak_day1']) . '&'
                . "speak_time1=" . urlencode($data['speak_time1']) . '&'
                . "speak_date2=" . urlencode($data['speak_date2']) . '&'
                . "speak_day2=" . urlencode($data['speak_day2']) . '&'
                . "speak_time2=" . urlencode($data['speak_time2']) . '&'
                . "speak_date3=" . urlencode($data['speak_date3']) . '&'
                . "speak_day3=" . urlencode($data['speak_day3']) . '&'
                . "speak_time3=" . urlencode($data['speak_time3']);

        if ($data["Digits"] == 1) {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response>";
            echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_four?"
            . $params . "' method='GET'>";
            echo "<Say voice='Polly.Joanna'> You have selected  <emphasis level='moderate'>" . $data['speak_day1'] . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $data['speak_date1'] . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $data['speak_time1'] . " </say-as></emphasis></Say>";
            echo "<Pause length='1'/>";
            echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
            echo "<Pause length='1'/>";
            echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
            echo "</Gather>";
            echo "<Pause length='4'/>";
            echo "<Redirect method='GET'>" . $base_url . "call_view/step_three?"
            . "Digits=" . $data["Digits"] . '&'
            . $params
            . "</Redirect>";
            echo "</Response>";
        } elseif ($data["Digits"] == 2) {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response>";
            echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_four?"
            . $params . "' method='GET'>";
            echo "<Say voice='Polly.Joanna'> You have selected  <emphasis level='moderate'>" . $data['speak_day2'] . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $data['speak_date2'] . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $data['speak_time2'] . " </say-as></emphasis></Say>";
            echo "<Pause length='1'/>";
            echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
            echo "<Pause length='1'/>";
            echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
            echo "</Gather>";
            echo "<Pause length='4'/>";
            echo "<Redirect method='GET'>" . $base_url . "call_view/step_three?"
            . "Digits=" . $data["Digits"] . '&'
            . $params . "</Redirect>";
            echo "</Response>";
        } elseif ($data["Digits"] == 3) {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response>";
            echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_four?" . $params . "' method='GET'>";
            echo "<Say voice='Polly.Joanna'> You have selected   <emphasis level='moderate'>" . $data['speak_day3'] . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $data['speak_date3'] . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $data['speak_time3'] . " </say-as></emphasis></Say>";
            echo "<Pause length='1'/>";
            echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
            echo "<Pause length='1'/>";
            echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
            echo "</Gather>";
            echo "<Pause length='4'/>";
            echo "<Redirect method='GET'>" . $base_url . "call_view/step_three?"
            . "Digits=" . $data["Digits"] . '&'
            . $params . "</Redirect>";
            echo "</Response>";
        } elseif ($data["Digits"] == 0) {
            echo "<Response><Say voice='Polly.Joanna' >Thank-you, the clinic will be in touch shortly'</Say></Response>";
        } elseif ($data["Digits"] == 4) {
            echo "<Response>";
            echo "<Redirect method='GET'>" . $base_url . "call_view/step_two?"
            . "Digits=1&"
            . $params . "</Redirect>";
            echo "</Response>";
        } else {
            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response><Say voice='Polly.Joanna' >You entered wrong digit</Say></Response>";
        }



        try {

            $http_params = array(
                'data' => $data["Digits"],
                'to' => $data["To"]
            );

            $defaults = array(
                CURLOPT_URL => $base_url . "efax/call_handle",
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query($http_params)
            );
            $ch = curl_init($base_url . "efax/call_handle");
            curl_setopt_array($ch, $defaults);

            curl_exec($ch);
            curl_close($ch);
        } catch (Exception $e) {
            echo "Error in response file";
        }
    }

    function step_four() {

       // $data = $this->input->get();
        $base_url = base_url();
                        $data = '{"first_name":"Lisa","last_name":"Roy","visit_name":"rose wood","clinic_name":"Test Clinic","address":"1 0 1 0 EASY ST, OTTAWA, ONTARIO, K1A 0B1","reservation_id":"0","date1":"2019-02-07","time1":"10:00:00","date2":"2019-02-08","time2":"10:00:00","date3":"2019-02-11","time3":"10:00:00","speak_date1":"February 7th","speak_day1":"Thursday","speak_time1":"10:00 am","speak_date2":"February 8th","speak_day2":"Friday","speak_time2":"10:00 am","speak_date3":"February 11th","speak_day3":"Monday","speak_time3":"10:00 am"}';
        echo "\n\nat step_four";
        echo "data = " . json_encode($data);

        $params = "first_name=" . urlencode($data["first_name"]) . '&'
                . "last_name=" . urlencode($data["last_name"]) . '&'
                . "visit_name=" . urlencode($data["visit_name"]) . '&'
                . "clinic_name=" . urlencode($data["clinic_name"]) . '&'
                . "address=" . urlencode($data["address"]) . '&'
                . "reservation_id=" . urlencode($data["reservation_id"]) . '&'
                . "date1=" . urlencode($data['date1']) . '&'
                . "time1=" . urlencode($data['time1']) . '&'
                . "date2=" . urlencode($data['date2']) . '&'
                . "time2=" . urlencode($data['time2']) . '&'
                . "date3=" . urlencode($data['date3']) . '&'
                . "time3=" . urlencode($data['time3']) . '&'
                . "speak_date1=" . urlencode($data['speak_date1']) . '&'
                . "speak_day1=" . urlencode($data['speak_day1']) . '&'
                . "speak_time1=" . urlencode($data['speak_time1']) . '&'
                . "speak_date2=" . urlencode($data['speak_date2']) . '&'
                . "speak_day2=" . urlencode($data['speak_day2']) . '&'
                . "speak_time2=" . urlencode($data['speak_time2']) . '&'
                . "speak_date3=" . urlencode($data['speak_date3']) . '&'
                . "speak_day3=" . urlencode($data['speak_day3']) . '&'
                . "speak_time3=" . urlencode($data['speak_time3']);

        if (isset($data["Digits"])) {
            $base_url = base_url();
            if ($data["Digits"] == 2) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_three?"
                . $params . "' method='GET'>";
                echo "<Say  voice='Polly.Joanna'>Hi  " . $data["first_name"] . ",  Please select one of the following dates and times for your appointment with " . $data["clinic_name"] . "</Say>";
                echo "<Say  voice='Polly.Joanna'>For <emphasis level='moderate'>" . $data['speak_day1'] . " <say-as interpret-as='date' format='mmyyyy'  detail='1'>" . $data['speak_date1'] . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $data['speak_time1'] . " </say-as></emphasis> - please enter 1  </Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>For <emphasis level='moderate'>" . $data['speak_day2'] . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $data['speak_date2'] . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $data['speak_time2'] . " </say-as></emphasis>  - please enter 2  </Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>For   <emphasis level='moderate'> " . $data['speak_day3'] . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $data['speak_date3'] . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $data['speak_time3'] . " </say-as></emphasis> - please enter 3</Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>If you would like the clinic to contact you directly - please enter 0</Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>To replay this message, please enter 4 </Say>";
                echo "</Gather>";
                echo "<Pause length='2'/>";
                echo "<Redirect method='GET'>" . $base_url . "call_view/step_four?"
                . "Digits=4&"
                . $params . "</Redirect>";
                echo "</Response>";
            } elseif ($data["Digits"] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Say  voice='Polly.Joanna'>Thank you</Say>";
                echo "<Hangup/>";
                echo "</Response>";
            } else {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Redirect method='GET'>" . $base_url . "call_view/confirm_call?"
                . $params . '&'
                . "Digits=timeout"
                . "</Redirect>"
                . "</Response>";
            }
        }
    }

    public function call_resp() {
        //if(isset($data["first_name"])){
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>";
        echo "<Gather  NumDigits='1' action='" . base_url() . "call_view/call_resp_handle/' method='GET'>";
        echo "<Say>";
        echo "Hello         " . $data["first_name"] . "
                Your      appointment   " . $_GET['pvname'] . "    with    " . $data["clinic_name"] . "    has been booked for " . $_GET['aDate'] . "    at    " . $_GET['aDate'] . "    .The    address    is:    " . $_GET['address'] . "    Please     type    1    to    confirm    this    booking.    If    this    date    does   not     work,    please   type   2    to    alert    the    clinic    staff";
        echo "</Say>";
        echo "</Gather>";

        echo "</Response>";
        //}
    }

    public function call_resp_handle() {
        if (isset($_REQUEST['Digits'])) {
            /*    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
              if($_REQUEST['Digits'] == 1){
              echo "<Response><Say>You entered  " . $_REQUEST['Digits'] . " , Thank you </Say></Response>";
              }else{
              echo "<Response><Say>You entered wrong digit</Say></Response>";
              } */
            echo "<pre>";
            print_r($_REQUEST);
            echo "<pre>";
        }
    }

    public function test() {
        $this->load->model("referral_model");
        $data = $this->referral_model->assign_slots(30);
        echo "<pre>";
        print_r($data);
        echo "<pre>";
    }

    //for wrong number
    public function vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc() {

        if (isset($_REQUEST['data']) && isset($_REQUEST['to'])) {

            $data = $this->input->post();
            $From = $data["to"];
            // $Body = $data["data"];
            //remove + sign
            $From = substr($From, 2);

            // log_message("error", "body is 1 or 2 is => " . $Body);
            $this->db->select("DISTINCT(r_pv.id), r_pv.visit_confirmed, r_pv.notify_email");
            $this->db->from("referral_patient_info pat, records_patient_visit r_pv");
            $this->db->where(array(
                "pat.active" => 1,
                "r_pv.active" => 1,
                "pat.cell_phone" => $From
            ));
            $this->db->where("r_pv.patient_id", "pat.id", false);
            $result = $this->db->get()->result();

            log_message("error", "webhook sql = " . $this->db->last_query());

            $change_status = false;

            $this->db->trans_start();
            foreach ($result as $row) {
                //change status to confirm
                $this->db->where(array(
                    "id" => $row->id
                ));
                // if()//Confirmed by email (wrong number)
                // if($row->notify_email === "1") {
                //     $this->db->set("visit_confirmed", "Confirmed by email (wrong number)");
                // } else {
                $this->db->set("visit_confirmed", "Wrong Number");
                // }

                $this->db->set("notify_voice", "0");
                $this->db->set("notify_sms", "0");

                $this->db->update("records_patient_visit");
                $change_status = true;
                log_message("error", "change (1) " . $this->db->last_query());
            }
            $this->db->trans_complete();
        }
    }

}
