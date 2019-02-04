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
        log_message("error", "data in post = " . $this->input->post());
        $this->call_confirm($this->input->post());
    }

    public function call_confirm($post) {

        $sid = 'AC2da3b84b65b63ccf4f05c27ac1713060';
        $token = '342a214ee959d16bf97ea87579016762';
        $twilio_number = "+13658000973";
        //$to = "+919876907251";  
//        $call_handle_file = "callhandle.php";
//        if($type == "visitCreate") {
//            $call_handle_file = "callhandle.php";
//        }

        if(isset($post["patient_name"])) {
            log_message("error", "val exist = " . $post["patient_name"]);
        }
        if(isset($post->patient_name)) {
            log_message("error", "val exist = " . $post->patient_name);
        }
        $patient_name = $post["patient_name"];
        $patient_lname = $post["patient_lname"];
        $clinic_name = $post["clinic_name"];
        $phone_number = $post["phone_number"];
        $address = $post["address"];
        $clinic_id = $post["clinic_id"];

        $to_number = $phone_number;
//        $to = "+919998207084";  
        $to_number =  "+919998207084";  

        $call_handle_file = "callhandle_new.php";
        if ($type == "visitCreate") {
            $call_handle_file = "callhandle_new.php";
        } else if ($type == "24hReminder") {
            $call_handle_file = "reminder_callhandle.php";
        }

        log_message("error", "Starting " . $call_handle_file . " for $type");

        $url = base_url() . "/call_view/callhandle?" .
                "patient_name=" . urlencode($patient_name) .
                "patient_lname=" . urlencode($patient_lname) .
                "&clinic_name=" . urlencode($clinic_name) .
                "&clinic_id=" . urlencode($clinic_id) .
                "&address=" . urlencode($address);
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
        $result = curl_exec($res);
        $resp = json_decode($result);
        $status = curl_getinfo($res, CURLINFO_HTTP_CODE);
//        echo "<pre>";
//        print_r($resp);
        if (curl_errno($res)) {
            log_message("error", " Error – " . curl_error($res));
            return false;
        } else {
            log_message("error", "Calling from callhandle");
            return true;
        }
    }

    public function call_resp() {
        //if(isset($_GET['pname'])){
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>";
        echo "<Gather  NumDigits='1' action='" . base_url() . "call_view/call_resp_handle/' method='GET'>";
        echo "<Say>";
        echo "Hello         " . $_GET['pname'] . "
                Your      appointment   " . $_GET['pvname'] . "    with    " . $_GET['cname'] . "    has been booked for " . $_GET['aDate'] . "    at    " . $_GET['aDate'] . "    .The    address    is:    " . $_GET['address'] . "    Please     type    1    to    confirm    this    booking.    If    this    date    does   not     work,    please   type   2    to    alert    the    clinic    staff";
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

    public function callhandle() {
        if (isset($_GET['patient_name'])) {
            $address = $_GET['address'];
            $dataarray = http_build_query($_GET);
            $base_url = "http://35.203.47.37";

            echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
            echo "<Response>
                    <Gather  timeout='3' numDigits='1' action='$base_url/call_view/step_two?"
            . "pname=" . urlencode($_GET['patient_name']) . "&amp;"
            . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
            . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
            . "cname=" . urlencode($_GET['clinic_name']) . "&amp;"
            . "address=" . urlencode($_GET['address']) . "' method='GET'>
			<Say  voice='Polly.Joanna'> Hello </Say>
                        <Pause length='1'/>
                        <Say voice='Polly.Joanna'> This is an automated appointment call for  " . $_GET['patient_name'] . "  " . $_GET['patient_lname'] . ".</Say>
                        <Pause length='1'/>
                        <Say voice='Polly.Joanna'> If you are  " . $_GET['patient_name'] . "  " . $_GET['patient_lname'] . " , please enter 1 to continue. If this is the wrong number, please type 2 to end the call</Say>
                    </Gather>
                    <Pause length='10'/>
                    <Redirect method='GET'>
                        $base_url/call_view/callhandle?"
            . "pname=" . urlencode($_GET['patient_name']) . "&amp;"
            . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
            . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
            . "cname=" . urlencode($_GET['clinic_name']) . "&amp;"
            . "address=" . urlencode($_GET['address']) . "&amp;
                        Digits=timeout
                    </Redirect>
		</Response>";
        }
    }

    function step_two() {

        $clinic_id = $_GET['clinic_id'];
        $this->load->model("referral_model");
        $data = $this->referral_model->assign_slots(30, $clinic_id);

        $date1 = date('F jS', strtotime($data[0]['start_time']));
        $day1 = date('l', strtotime($data[0]['start_time']));
        $time1 = date('g:i a', strtotime($data[0]['start_time']));

        $date2 = date('F jS', strtotime($data[1]['start_time']));
        $day2 = date('l', strtotime($data[1]['start_time']));
        $time2 = date('g:i a', strtotime($data[1]['start_time']));

        $date3 = date('F jS', strtotime($data[2]['start_time']));
        $day3 = date('l', strtotime($data[2]['start_time']));
        $time3 = date('g:i a', strtotime($data[2]['start_time']));

        if (isset($_GET["Digits"])) {
            $base_url = "http://35.203.47.37";
            if ($_GET['Digits'] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='$base_url/call_view/step_three?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say  voice='Polly.Joanna'>Hi  " . $_GET['pname'] . ",  Please select one of the following dates and times for your appointment with " . $_GET['cname'] . "</Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>For <emphasis level='moderate'>" . $day1 . " <say-as interpret-as='date' format='mmyyyy'  detail='1'>" . $date1 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time1 . " </say-as></emphasis> - please enter 1  </Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>For <emphasis level='moderate'>" . $day2 . " <say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date2 . " </say-as>     at  <say-as interpret-as='time' format='hms12'>  " . $time2 . " </say-as></emphasis>  - please enter 2  </Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>For   <emphasis level='moderate'> " . $day3 . " <say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date3 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time3 . " </say-as></emphasis> - please enter 3</Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>If you would like the clinic to contact you directly - please enter 0</Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>To replay this message, please enter 4 </Say>";
                echo "</Gather>";
                echo "<Pause length='2'/>";
                echo "<Redirect method='GET'>
		            $base_url/call_view/step_two?Digits=1&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 2) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Say voice='Polly.Joanna'>Thank you</Say></Response>";


                $params = array(
                    'data' => $_GET["Digits"],
                    'to' => $_GET['To']
                );
                $defaults = array(
                    CURLOPT_URL => "$base_url/call_view/vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc",
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($params)
                );
                $ch = curl_init("$base_url/call_view/vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc");
                curl_setopt_array($ch, $defaults);
                curl_exec($ch);
                curl_close($ch);
            } else {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Redirect method='GET'>
			$base_url/call_view/callhandle?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "Digits=timeout"
                . "</Redirect>"
                . "</Response>";
            }
        }
    }

    function step_three() {
        $clinic_id = $_GET['clinic_id'];
        $this->load->model("referral_model");
        $data = $this->referral_model->assign_slots(30, $clinic_id);

        $date1 = date('F jS', strtotime($data[0]['start_time']));
        $day1 = date('l', strtotime($data[0]['start_time']));
        $time1 = date('g:i a', strtotime($data[0]['start_time']));
        $date2 = date('F jS', strtotime($data[1]['start_time']));
        $day2 = date('l', strtotime($data[1]['start_time']));
        $time2 = date('g:i a', strtotime($data[1]['start_time']));
        $date3 = date('F jS', strtotime($data[2]['start_time']));
        $day3 = date('l', strtotime($data[2]['start_time']));
        $time3 = date('g:i a', strtotime($data[2]['start_time']));


        if (isset($_GET["Digits"])) {
            $base_url = "http://35.203.47.37";
            if ($_GET['Digits'] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='$base_url/call_view/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected  <emphasis level='moderate'>" . $day2 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date2 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time2 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>
		            $base_url/call_view/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 2) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='$base_url/call_view/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected  <emphasis level='moderate'>" . $day2 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date2 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time2 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>
		            $base_url/call_view/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 3) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='$base_url/call_view/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected   <emphasis level='moderate'>" . $day3 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date3 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time3 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>
		            $base_url/call_view/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 0) {
                echo "<Response><Say voice='Polly.Joanna' >Thank-you, the clinic will be in touch shortly'</Say></Response>";
            } elseif ($_GET['Digits'] == 4) {
                echo "<Response>";
                echo "<Redirect method='GET'>
		            $base_url/call_view/step_two?"
                . "Digits=1&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;</Redirect>";
                echo "</Response>";
            } else {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Say voice='Polly.Joanna' >You entered wrong digit</Say></Response>";
            }

            try {

                $params = array(
                    'data' => $_GET["Digits"],
                    'to' => $_GET['To']
                );

                $defaults = array(
                    CURLOPT_URL => "$base_url/efax/call_handle",
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($params)
                );
                $ch = curl_init("$base_url/efax/call_handle");
                curl_setopt_array($ch, $defaults);

                curl_exec($ch);
                curl_close($ch);
            } catch (Exception $e) {
                echo "Error in response file";
            }
        }
    }

    function step_four() {
        //echo $_GET["Digits"];
        $clinic_id = $_GET['clinic_id'];
        $this->load->model("referral_model");
        $data = $this->referral_model->assign_slots(30, $clinic_id);

        $date1 = date('F jS', strtotime($data[0]['start_time']));
        $day1 = date('l', strtotime($data[0]['start_time']));
        $time1 = date('g:i a', strtotime($data[0]['start_time']));
        $date2 = date('F jS', strtotime($data[1]['start_time']));
        $day2 = date('l', strtotime($data[1]['start_time']));
        $time2 = date('g:i a', strtotime($data[1]['start_time']));
        $date3 = date('F jS', strtotime($data[2]['start_time']));
        $day3 = date('l', strtotime($data[2]['start_time']));
        $time3 = date('g:i a', strtotime($data[2]['start_time']));
        if (isset($_GET["Digits"])) {
            $base_url = "http://35.203.47.37";
            if ($_GET['Digits'] == 2) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='$base_url/call_view/step_three?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say  voice='Polly.Joanna'>Hi  " . $_GET['pname'] . ",  Please select one of the following dates and times for your appointment with " . $_GET['cname'] . "</Say>";
                echo "<Say  voice='Polly.Joanna'>For <emphasis level='moderate'>" . $day1 . " <say-as interpret-as='date' format='mmyyyy'  detail='1'>" . $date1 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time1 . " </say-as></emphasis> - please enter 1  </Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>For <emphasis level='moderate'>" . $day2 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date2 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time2 . " </say-as></emphasis>  - please enter 2  </Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>For   <emphasis level='moderate'> " . $day3 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date3 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time3 . " </say-as></emphasis> - please enter 3</Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>If you would like the clinic to contact you directly - please enter 0</Say>";
                echo "<Pause length='1'/>";
                echo "<Say  voice='Polly.Joanna'>To replay this message, please enter 4 </Say>";
                echo "</Gather>";
                echo "<Pause length='2'/>";
                echo "<Redirect method='GET'>
			$base_url/call_view/step_four?"
                . "Digits=4&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Say  voice='Polly.Joanna'>Thank you</Say>";
                echo "<Hangup/>";
                echo "</Response>";
            } else {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Redirect method='GET'>$base_url/call_view/callhandle?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET['clinic_id']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "Digits=timeout"
                . "</Redirect>"
                . "</Response>";
            }
        }
    }

}
