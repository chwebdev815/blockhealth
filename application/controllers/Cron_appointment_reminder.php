<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_appointment_reminder extends CI_Controller {

    public function index() {
        if (isset($argv)) {
            if (!empty($argv[1])) {
                switch ($argv[1]) {
                    case "ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE":
                        $this->ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE();
                        log_message("error", "Called function ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE");
                        break;
                }
            }
        }
    }

    public function ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE() {

        //get all to schedule a call
        $plus_72_hour = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("+3 day")));
        $string_plus_72_hour = $plus_72_hour->format("Y-m-d H:i:s");
        $plus_72_hour_5_min = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("+3 day 5 minute")));
        $string_plus_72_hour_5_min = $plus_72_hour_5_min->format("Y-m-d H:i:s");
//        $remindable = $this->db->select("*")->from("records_patient_visit")->where(array(
//                    "concat(visit_date, ' ', visit_time) > " => $string_plus_72_hour,
//                    "concat(visit_date, ' ', visit_time) < " => $string_plus_72_hour_5_min,
//                    "visit_confirmed" => "Confirmed",
//                ))->get()->result();
        $remindable = $this->db->select("*")->from("records_patient_visit")->where(array(
                    "id" => 47
                ))->get()->result();

        echo $this->db->last_query() . "<br/><br/>";
        echo json_encode($remindable) . "<br/><br/>";

        $this->load->model("referral_model");
        foreach ($remindable as $key => $value) {

            $visit = $value;

            if ($visit->notify_type == "call") {

                //get clinic id for patient
                $this->db->select('admin.id as clinic_id, '
                        . 'CASE WHEN (pat.cell_phone = NULL OR pat.cell_phone = "") THEN "false" ELSE "true" END AS allow_sms,' .
                        'CASE WHEN (pat.email_id = NULL OR pat.email_id = "") THEN "false" ELSE "true" END AS allow_email, ' .
                        "admin.address," .
                        "pat.email_id, pat.cell_phone, pat.home_phone, pat.work_phone, " .
                        "pat.fname, pat.lname, admin.clinic_institution_name, admin.call_address");
                $this->db->from("clinic_referrals c_ref, referral_patient_info pat, efax_info efax, clinic_user_info admin");
                $this->db->where(array(
                    "efax.active" => 1,
                    "admin.active" => 1,
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "pat.id" => $visit->patient_id
                ));
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $this->db->where("efax.to", "admin.id", false);
                $this->db->where("c_ref.efax_id", "efax.id", false);
                $call_data = $this->db->get()->result();

                if ($call_data) {
                    $call_data = $call_data[0];
                    $new_visit_duration = 30;
                    //find asignable slots
                    $allocations = $this->referral_model->assign_slots($new_visit_duration, $call_data->clinic_id);
                    //make call with proper data
                    //check if call or sms or both -  REMAINING

                    echo "checkig for clinic " . $call_data->clinic_id . "<br/>";


                    $post_arr = array(
                        'defaultContactFormName' => $call_data->fname,
                        "patient_lname" => $call_data->lname,
                        "defaultContactFormName2" => $visit->visit_name,
                        'defaultContactFormName3' => $call_data->clinic_institution_name,
                        'defaultContactFormName4' => $visit->visit_date,
                        'defaultContactFormName5' => $visit->visit_time,
                        'defaultContactFormName6' => $call_data->cell_phone,
                        'address' => $call_data->call_address,
                        'clinic_id' => $call_data->clinic_id,
                        'type' => 'Call reminder before 72 hour',
                        "patient_id" => $visit->patient_id,
                        "notify_voice" => $visit->notify_voice,
                        "notify_sms" => $visit->notify_sms,
                        "notify_email" => $visit->notify_email,
                        "reserved_id" => $visit->id
                    );

                    log_message("error", "Call should start now");
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_URL, base_url() . "cron_appointment_reminder/call");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_arr));
                    $resp = curl_exec($ch);
                    if (curl_errno($ch)) {
                        log_message("error", "Call error => " . json_encode(curl_error($ch)));
                        return curl_error($ch);
                    }
                    curl_close($ch);
                    log_message("error", "Call completed " . json_encode($resp));
                }
            } else if ($visit->notify_type == "sms") {
                echo "make sms";
            }
        }
    }

    //call scripts


    public function call() {
        $pname = $this->input->post('defaultContactFormName');
        $patient_lname = $this->input->post('patient_lname');
        $pvname = $this->input->post('defaultContactFormName2');
        $cname = $this->input->post('defaultContactFormName3');
        $aDate = $this->input->post('defaultContactFormName4');
        $aTime = $this->input->post('defaultContactFormName5');
//        $mob = $this->input->post('defaultContactFormName6');
        $mob = "+917201907712";
        $clinic_id = $this->input->post('clinic_id');
        $patient_id = $this->input->post('patient_id');
        $notify_voice = $this->input->post('notify_voice');
        $notify_sms = $this->input->post('notify_sms');
        $notify_email = $this->input->post('notify_email');
        $address = $this->input->post('address');
        $type = $this->input->post("type");
        $reserved_id = $this->input->post("reserved_id");

        if (!empty($mob)) {
            $dataNew = $this->call_confirm($reserved_id, $clinic_id, $patient_id, $notify_voice, $notify_sms, $notify_email, $type, $mob, $pname, $patient_lname, $pvname, $cname, $aDate, $aTime, $address);
            echo "<pre>";
            print_r($dataNew);
        }
    }

    public function call_confirm($reserved_id, $clinic_id, $patient_id, $notify_voice, $notify_sms, $notify_email, $type, $to_number, $pname, $patient_lname, $pvname, $cname, $aDate, $aTime, $address) {

        $sid = 'AC2da3b84b65b63ccf4f05c27ac1713060';
        $token = '342a214ee959d16bf97ea87579016762';
        $twilio_number = "+13658000973";
        //$to = "+919876907251";  
//        $to_number = "+917201907712";


        $url = "http://35.203.47.37/" . "cron_appointment_reminder/callhandle?"
                . "pname=" . urlencode($pname) . "&"
                . "patient_lname=" . urlencode($patient_lname) . "&"
                . "pvname=" . urlencode($pvname) . "&"
                . "cname=" . urlencode($cname) . "&"
                . "aDate=" . urlencode($aDate) . "&"
                . "aTime=" . urlencode($aTime) . "&"
                . "address=" . urlencode($address) . "&"
                . "clinic_id=" . urlencode($clinic_id) . "&"
                . "patient_id=" . urlencode($patient_id) . "&"
                . "reserved_id=" . urlencode($reserved_id) . "&"
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

    public function callhandle() {

        $address = $_GET['address'];
        $dataarray = http_build_query($_GET);
        $base_url = "http://35.203.47.37/";

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>
            <Gather  timeout='3' numDigits='1' action='" . $base_url . "cron_appointment_reminder/step_two?"
        . "pname=" . urlencode($_GET['pname']) . "&amp;"
        . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
        . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
        . "cname=" . urlencode($_GET['cname']) . "&amp;"
        . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
        . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
        . "address=" . urlencode($_GET['address']) . "&amp;"
        . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
        . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
        . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
        . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
        . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
        . "notify_email=" . urlencode($_GET["notify_email"]) . "' method='GET'>";


        echo "<Say  voice='Polly.Joanna'>Hello " . $_GET['pname'] . " " . $_GET['patient_lname'] . "</Say>";
        echo "<Pause length='1'/>";
        echo "<Say voice='Polly.Joanna'>Your appointment with  " . $_GET['cname'] . "  has been booked for  <say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $_GET['aDate'] . "</say-as>     at   <say-as interpret-as='time' format='hms12'> " . $_GET['aTime'] . " </say-as></Say>";
        echo "<Pause length='1'/>";
        echo "<Say voice='Polly.Joanna'>The address  is: " . $_GET['address'] . "  </Say>";
        echo "<Pause length='1'/>";
        echo "<Say voice='Polly.Joanna'>Please     type   1   to    confirm    this    booking .   If    this    date    does   not work please  type 2   to    alert    the    clinic    staff.</Say>";
        echo "<Say voice='Polly.Joanna'>Please type 3 to replay this message</Say>";
        echo "</Gather>";
        echo "<Pause length='10'/>";

        echo "<Redirect method='GET'>
            " . $base_url . "cron_appointment_reminder/callhandle?"
        . "pname=" . urlencode($_GET['pname']) . "&amp;"
        . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
        . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
        . "cname=" . urlencode($_GET['cname']) . "&amp;"
        . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
        . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
        . "address=" . urlencode($_GET['address']) . "&amp;"
        . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
        . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
        . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
        . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
        . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
        . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;
                    Digits=timeout</Redirect>
		</Response>";
    }

    public function step_two() {

        $clinic_id = $_GET["clinic_id"];
        $patient_id = $_GET["patient_id"];
        $reserved_id = $_GET["reserved_id"];


        $base_url = "http://35.203.47.37";

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        if ($_GET['Digits'] == 1) {
            echo "<Response><Say voice='Polly.Joanna'>Thank you, your appointment has been confirmed </Say></Response>";
        } elseif ($_GET['Digits'] == 2) {
            echo "<Response><Say voice='Polly.Joanna'>Thank you, the clinic has been notified and will be in touch shortly</Say></Response>";
            $this->db->where(array(
                "id" => $reserved_id
            ))->update("records_patient_visit", array(
                "visit_confirmed" => "Change required"
            ));
        } elseif ($_GET['Digits'] == 3) {
            echo "<Response>";
            echo "<Redirect method='GET'>
            " . $base_url . "cron_appointment_reminder/callhandle?"
            . "pname=" . urlencode($_GET['pname']) . "&amp;"
            . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
            . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
            . "cname=" . urlencode($_GET['cname']) . "&amp;"
            . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
            . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
            . "address=" . urlencode($_GET['address']) . "&amp;"
            . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
            . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
            . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
            . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
            . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
            . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;
                    Digits=timeout</Redirect>
		</Response>";
        } else {
            echo "<Response><Say voice='Polly.Joanna' >You entered wrong digit</Say></Response>";
        }



//        try {
//
//            $params = array(
//                'data' => $_REQUEST["Digits"],
//                'to' => $_REQUEST['To']
//            );
//
//            $defaults = array(
//                CURLOPT_URL => "$base_url/efax/call_handle",
//                CURLOPT_POST => true,
//                CURLOPT_POSTFIELDS => http_build_query($params)
//            );
//            $ch = curl_init("$base_url/efax/call_handle");
//            curl_setopt_array($ch, $defaults);
//
//            curl_exec($ch);
//            curl_close($ch);
//
////	$myFile = "testFile.txt";
////	$fh = fopen($myFile, 'a') or die("can't open file");
////	$stringData = "Data saving from response ===> " . json_encode($_REQUEST);
////	fwrite($fh, $stringData);
////	fclose($fh);
//        } catch (Exception $e) {
//            echo "Error in response file";
////	$myFile = "testFile.txt";
////	$fh = fopen($myFile, 'a') or die("can't open file");
////	$stringData = "Error in call handle" . $e->getMessage();
////	fwrite($fh, $stringData);
////	fclose($fh);
//        }
//
//        if ($Body == "1") {
//            if ($row->visit_confirmed == "Awaiting Confirmation" || $row->visit_confirmed == "Change required") {
//                //change status to confirm
//                $this->db->where(array(
//                    "id" => $row->id
//                ));
//                $this->db->set("visit_confirmed", "Confirmed");
//                $this->db->update("records_patient_visit");
//                $change_status = true;
//                log_message("error", "change (1) " . $this->db->last_query());
//            }
//        }
//        if ($Body == "2") {
//            if ($row->visit_confirmed == "Awaiting Confirmation") {
//                //change status to Change required
//                $this->db->where(array(
//                    "id" => $row->id
//                ));
//                $this->db->set("visit_confirmed", "Change required");
//                $this->db->update("records_patient_visit");
//                $change_status = true;
//                log_message("error", "change (2) " . $this->db->last_query());
//            }
//        }
    }

}
