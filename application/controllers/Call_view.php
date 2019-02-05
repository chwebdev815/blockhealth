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
        $pname = $this->input->post('defaultContactFormName');
        $patient_lname = $this->input->post('patient_lname');
        $pvname = $this->input->post('defaultContactFormName2');
        $cname = $this->input->post('defaultContactFormName3');
        $aDate = $this->input->post('defaultContactFormName4');
        $aTime = $this->input->post('defaultContactFormName5');
        $mob = $this->input->post('defaultContactFormName6');
        $clinic_id = $this->input->post('clinic_id');
        $patient_id = $this->input->post('patient_id');
        $notify_voice = $this->input->post('notify_voice');
        $notify_sms = $this->input->post('notify_sms');
        $notify_email = $this->input->post('notify_email');
        $address = $this->input->post('address');
        $type = $this->input->post("type");

        if (!empty($mob)) {
            $dataNew = $this->call_confirm($clinic_id, $patient_id, $notify_voice, $notify_sms, $notify_email, $type, $mob, $pname, $patient_lname, $pvname, $cname, $aDate, $aTime, $address);
            echo "<pre>";
            print_r($dataNew);
        }
    }

    public function call_confirm($clinic_id, $patient_id, $notify_voice, $notify_sms, $notify_email, $type, $to_number, $pname, $patient_lname, $pvname, $cname, $aDate, $aTime, $address) {

        $sid = 'AC2da3b84b65b63ccf4f05c27ac1713060';
        $token = '342a214ee959d16bf97ea87579016762';
        $twilio_number = "+13658000973";
        //$to = "+919876907251";  

        $to_number = "+917201907712";


        $url = base_url() . "call_view/callhandle?"
                . "pname=" . urlencode($pname) . "&"
                . "patient_lname=" . $patient_lname . "&"
                . "pvname=" . urlencode($pvname) . "&"
                . "cname=" . urlencode($cname) . "&"
                . "aDate=" . urlencode($aDate) . "&"
                . "aTime=" . urlencode($aTime) . "&"
                . "address=" . urlencode($address) . "&"
                . "clinic_id=" . urlencode($clinic_id) . "&"
                . "patient_id=" . urlencode($patient_id) . "&"
                . "notify_voice=" . urlencode($notify_voice) . "&"
                . "notify_sms=" . urlencode($notify_sms) . "&"
                . "notify_email=" . urlencode($notify_email);
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
            log_message("error", "Calling");
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

        $address = $_GET['address'];
        $dataarray = http_build_query($_GET);
        $base_url = base_url();

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>
            <Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_two?"
        . "pname=" . urlencode($_GET['pname']) . "&amp;"
        . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
        . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
        . "cname=" . urlencode($_GET['cname']) . "&amp;"
        . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
        . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
        . "address=" . urlencode($_GET['address']) . "&amp;"
        . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
        . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
        . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
        . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
        . "notify_email=" . urlencode($_GET["notify_email"]) . "' method='GET'>
                <Say  voice='Polly.Joanna'> Hello </Say>
                <Pause length='1'/>
                <Say voice='Polly.Joanna'> This is an automated appointment call for  " . $_GET['pname'] . "  " . $_GET['patient_lname'] . ".</Say>
                <Pause length='1'/>
                <Say voice='Polly.Joanna'> If you are  " . $_GET['pname'] . "  " . $_GET['patient_lname'] . " , please enter 1 to continue. If this is the wrong number, please type 2 to end the call</Say>
				</Gather>
            <Pause length='10'/>
            <Redirect method='GET'>
            " . $base_url . "call_view/callhandle?"
        . "pname=" . urlencode($_GET['pname']) . "&amp;"
        . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
        . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
        . "cname=" . urlencode($_GET['cname']) . "&amp;"
        . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
        . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
        . "address=" . urlencode($_GET['address']) . "&amp;"
        . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
        . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
        . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
        . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
        . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;
                    Digits=timeout</Redirect>
		</Response>";
    }

    public function step_two() {
        $clinic_id = $_GET["clinic_id"];
        $patient_id = $_GET["patient_id"];

        $this->load->model("referral_model");
        $data = $this->referral_model->assign_slots(30, $clinic_id);

        //save datetime as reserved till call ends
        $allocations = $data;
        $start_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["start_time"]);
        $end_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["end_time"]);
        $start_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["start_time"]);
        $end_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["end_time"]);
        $start_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["start_time"]);
        $end_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["end_time"]);

        $insert_data = array(
            "patient_id" => $patient_id,
            "visit_name" => $_GET["pvname"],
            "visit_date1" => $start_time1->format("Y-m-d"),
            "visit_start_time1" => $start_time1->format("H:i:s"),
            "visit_end_time1" => $end_time1->format("H:i:s"),
            "visit_date2" => $start_time2->format("Y-m-d"),
            "visit_start_time2" => $start_time2->format("H:i:s"),
            "visit_end_time2" => $end_time2->format("H:i:s"),
            "visit_date3" => $start_time3->format("Y-m-d"),
            "visit_start_time3" => $start_time3->format("H:i:s"),
            "visit_end_time3" => $end_time3->format("H:i:s"),
            "visit_expire_time" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT10M"))->format("Y-m-d H:i:s"),
            "notify_voice" => $_GET["notify_voice"],
            "notify_sms" => $_GET["notify_sms"],
            "notify_email" => $_GET["notify_email"],
            "visit_confirmed" => "Awaiting Confirmation"
        );

        $this->db->insert("records_patient_visit_reserved", $insert_data);
        $insert_id = $this->db->insert_id();

        //data reserved for 10 mins

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
            $base_url = base_url();
            if ($_GET['Digits'] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_three?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
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
                echo "<Redirect method='GET'>" . $base_url . "call_view/step_two?"
                . "Digits=1&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3)
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
                    CURLOPT_URL => $base_url . "call_view/vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc",
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($params)
                );
                $ch = curl_init($base_url . "call_view/vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc");
                curl_setopt_array($ch, $defaults);
                curl_exec($ch);
                curl_close($ch);
            } else {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Redirect method='GET'>" . $base_url . "call_view/callhandle?pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "Digits=timeout"
                . "</Redirect>"
                . "</Response>";
            }
        }
    }

    function step_three() {
        $clinic_id = $_GET["clinic_id"];
        $date1 = $_GET["date1"];
        $day1 = $_GET["day1"];
        $time1 = $_GET["time1"];

        $date2 = $_GET["date2"];
        $day2 = $_GET["day2"];
        $time2 = $_GET["time2"];

        $date3 = $_GET["day3"];
        $day3 = $_GET["date3"];
        $time3 = $_GET["time3"];


        if (isset($_GET["Digits"])) {
            $base_url = base_url();
            if ($_GET['Digits'] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected  <emphasis level='moderate'>" . $day1 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date1 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time1 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>" . $base_url . "call_view/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 2) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected  <emphasis level='moderate'>" . $day2 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date2 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time2 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>" . $base_url . "call_view/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 3) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected   <emphasis level='moderate'>" . $day3 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date3 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time3 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>" . $base_url . "call_view/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 0) {
                echo "<Response><Say voice='Polly.Joanna' >Thank-you, the clinic will be in touch shortly'</Say></Response>";
            } elseif ($_GET['Digits'] == 4) {
                echo "<Response>";
                echo "<Redirect method='GET'>" . $base_url . "call_view/step_two?"
                . "Digits=1&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
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
                    CURLOPT_URL => $base_url . "efax/call_handle",
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($params)
                );
                $ch = curl_init($base_url . "efax/call_handle");
                curl_setopt_array($ch, $defaults);

                curl_exec($ch);
                curl_close($ch);
            } catch (Exception $e) {
                echo "Error in response file";
            }
        }
    }

    function step_four() {
        $clinic_id = $_GET["clinic_id"];

        $date1 = $_GET["date1"];
        $day1 = $_GET["day1"];
        $time1 = $_GET["time1"];

        $date2 = $_GET["date2"];
        $day2 = $_GET["day2"];
        $time2 = $_GET["time2"];

        $date3 = $_GET["day3"];
        $day3 = $_GET["date3"];
        $time3 = $_GET["time3"];

        if (isset($_GET["Digits"])) {
            $base_url = base_url();
            if ($_GET['Digits'] == 2) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "call_view/step_three?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
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
                echo "<Redirect method='GET'>" . $base_url . "call_view/step_four?"
                . "Digits=4&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
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
                echo "<Response><Redirect method='GET'>" . $base_url . "call_view/callhandle?pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "Digits=timeout"
                . "</Redirect>"
                . "</Response>";
            }
        }
    }

}
