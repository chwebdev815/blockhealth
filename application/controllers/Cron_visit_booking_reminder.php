<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_visit_booking_reminder extends CI_Controller {

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
        log_message("error", "Cron_visit_booking_reminder called");

        //get all to schedule a call
//        $before_1_hour = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-1 hour")));
//        $before_1_hour_5_min = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-1 hour 5 minute")));
//        $before_24_hour = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-1 day")));
//        $before_24_hour_5_min = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-1 day 5 minute")));
//        $before_48_hour = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-2 day")));
//        $before_48_hour_5_min = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-2 day 5 minute")));

        $cur_time = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $before_5_min = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-5 minute")));

        log_message("error", "1 hour = " . $cur_time->format("Y-m-d H:i:s") . " to " . $before_5_min->format("Y-m-d H:i:s") . "<br/>");
//        $string_plus_72_hour = $plus_72_hour->format("Y-m-d H:i:s");
//        $plus_72_hour_5_min = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("+3 day 5 minute")));
//        $string_plus_72_hour_5_min = $plus_72_hour_5_min->format("Y-m-d H:i:s");
        $remindable = $this->db->select("*, if(notify_type='sms' AND "
                                . "reminder_1h >= '" . $before_5_min->format("Y-m-d H:i:s") . "' AND "
                                . "reminder_1h < '" . $cur_time->format("Y-m-d H:i:s") . "', "
                                . "'1h', if("
                                . "reminder_24h >= '" . $before_5_min->format("Y-m-d H:i:s") . "' AND "
                                . "reminder_24h < '" . $cur_time->format("Y-m-d H:i:s") . "', "
                                . "'24h', if("
                                . "reminder_48h >= '" . $before_5_min->format("Y-m-d H:i:s") . "' AND "
                                . "reminder_48h < '" . $cur_time->format("Y-m-d H:i:s") . "', "
                                . "'48h', 0))) as reminder_type")
                        ->from("records_patient_visit_reserved")
                        ->group_start()->where(array(
                            "notify_type" => "sms",
                            "reminder_1h >= " => $before_5_min->format("Y-m-d H:i:s"),
                            "reminder_1h < " => $cur_time->format("Y-m-d H:i:s")
                        ))->or_group_start()->where(array(
                            "reminder_24h >= " => $before_5_min->format("Y-m-d H:i:s"),
                            "reminder_24h < " => $cur_time->format("Y-m-d H:i:s")
                        ))->group_end()
                        ->or_group_start()->where(array(
                            "reminder_48h >= " => $before_5_min->format("Y-m-d H:i:s"),
                            "reminder_48h < " => $cur_time->format("Y-m-d H:i:s")
                        ))->group_end()
                        ->group_end()
                        ->where(array(
                            "visit_confirmed" => "N/A",
                            "active" => 1
                        ))
                        ->get()->result();
//        $remindable = $this->db->select("*")->from("records_patient_visit_reserved")->where(array(
////                    "id" => 10
//                    "id" => 80
//                ))->get()->result();



        log_message("error", $this->db->last_query() . "<br/><br/>");
        log_message("error", json_encode($remindable) . "<br/><br/>");


        $this->load->model("referral_model");
        foreach ($remindable as $key => $value) {
            $visit = $value;

            //get clinic id for patient
            $this->db->select('admin.id as clinic_id, '
                    . 'CASE WHEN (pat.cell_phone = NULL OR pat.cell_phone = "") '
                    . 'THEN "false" ELSE "true" END AS allow_sms,' .
                    'CASE WHEN (pat.email_id = NULL OR pat.email_id = "") '
                    . 'THEN "false" ELSE "true" END AS allow_email, ' .
                    "admin.address," .
                    "pat.email_id, pat.cell_phone, pat.home_phone, pat.work_phone, " .
                    "pat.fname, pat.lname, admin.clinic_institution_name, admin.call_address");
            $this->db->from("clinic_referrals c_ref, referral_patient_info pat, "
                    . "efax_info efax, clinic_user_info admin");
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
            $patient_data = $this->db->get()->result();

            if ($patient_data) {
                $patient_data = $patient_data[0];
                echo "checkig for clinic " . $patient_data->clinic_id . "<br/>";
                $contact_number = $patient_data->cell_phone;
                if ($patient_data->home_phone != "") {
                    //home number
                    $contact_number = $patient_data->home_phone;
                } else if ($patient_data->work_phone != "") {
                    //work number
                    $contact_number = $patient_data->work_phone;
                }
                $new_visit_duration = 30;

                //find asignable slots
                $response = $this->referral_model->assign_slots($new_visit_duration, $visit->patient_id);
                if ($response["result"] === "error") {
                    continue;
                } else if ($response["result"] === "success") {
                    $allocations = $response["data"];
                }
                //make call with proper data
                log_message("error", "reminder = " . $visit->reminder_type . ", ");
//                $visit->reminder_type = intval($visit->reminder_type);
                $notification_status = $visit->notify_status;
                $notification_status_icon = "green";
                $notification_datetime = date("Y-m-d H:i:s");
//                if ($visit->reminder_type == 3) {
//                    $notification_status = "No response";
//                    $notification_status_icon = "red";
//                } else {
//                    $status_number = $visit->reminder_type + (($visit->notify_type === 'sms') ? 1 : 0);
//                    $split = explode(", ", $visit->notify_status);
//                    $split[] = "Call" . $status_number;
//                    $notification_status = implode(", ", $split);
//                    $notification_status_icon = "green";
//                }
                if ($visit->reminder_type === "1h") {
                    $notification_status .= ", Call1";
                    $notification_datetime = $visit->created_datetime;
                } else if ($visit->reminder_type === "24h") {
                    $notification_status .= ", Call2";
                    $notification_datetime = $visit->created_datetime;
                } else if ($visit->reminder_type === "48h") {
                    $notification_status = "No response";
                    $notification_status_icon = "red";
                }


                log_message("error", "status is changing from " . $visit->notify_status . " to " . $notification_status);
                $update_data = array(
                    "visit_date1" => substr($allocations[0]["start_time"], 0, 10),
                    "visit_start_time1" => substr($allocations[0]["start_time"], 10),
                    "visit_end_time1" => substr($allocations[0]["end_time"], 10),
                    "visit_date2" => substr($allocations[1]["start_time"], 0, 10),
                    "visit_start_time2" => substr($allocations[1]["start_time"], 10),
                    "visit_end_time2" => substr($allocations[1]["end_time"], 10),
                    "visit_date3" => substr($allocations[2]["start_time"], 0, 10),
                    "visit_start_time3" => substr($allocations[2]["start_time"], 10),
                    "visit_end_time3" => substr($allocations[2]["end_time"], 10),
                    "visit_expire_time" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT10M"))->format("Y-m-d H:i:s"),
                    "notify_status" => $notification_status,
                    "notify_status_icon" => $notification_status_icon
                );
                $this->db->where(array(
                    "id" => $visit->id
                ))->update("records_patient_visit_reserved", $update_data);

                //set status in accepted_status
                $referral_id = $this->db->select("c_ref.id")
                                ->from("clinic_referrals c_ref, referral_patient_info pat")
                                ->where(array(
                                    "pat.id" => $visit->patient_id
                                ))
                                ->where("c_ref.id", "pat.referral_id", false)
                                ->get()->result()[0]->id;

                $this->db->where(array(
                    "id" => $referral_id
                ))->update("clinic_referrals", array(
                    "accepted_status" => $notification_status,
                    "accepted_status_icon" => $notification_status_icon,
                    "accepted_status_date" => $notification_datetime
                ));
//                    $contact_number = "+917201907712";

                $post_arr = array(
                    'defaultContactFormName' => $patient_data->fname,
                    "patient_lname" => $patient_data->lname,
                    "defaultContactFormName2" => $visit->visit_name,
                    'defaultContactFormName3' => $patient_data->clinic_institution_name,
                    'defaultContactFormName4' => "aaa",
                    'defaultContactFormName5' => "bbb",
                    'defaultContactFormName6' => $contact_number,
                    'address' => $patient_data->call_address,
                    'clinic_id' => $patient_data->clinic_id,
                    'type' => 'booking_reminder',
                    "patient_id" => $visit->patient_id,
                    "notify_voice" => $visit->notify_voice,
                    "notify_sms" => $visit->notify_sms,
                    "notify_email" => $visit->notify_email,
                    "reserved_id" => $visit->id
                );

                echo "post array = " . json_encode($post_arr) . "<br/>";
                log_message("error", "Call should start now");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_URL, base_url() . "cron_visit_booking_reminder/call");
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
        $mob = $this->input->post('defaultContactFormName6');
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
//            echo "<pre>";
//            print_r($dataNew);
        }
    }

    public function call_confirm($reserved_id, $clinic_id, $patient_id, $notify_voice, $notify_sms, $notify_email, $type, $to_number, $pname, $patient_lname, $pvname, $cname, $aDate, $aTime, $address) {

        $sid = 'AC2da3b84b65b63ccf4f05c27ac1713060';
        $token = '342a214ee959d16bf97ea87579016762';
        $twilio_number = "+16475607989";
        //$to = "+919876907251";  
//        $to_number = "+917201907712";


        $url = "http://35.203.47.37/" . "cron_visit_booking_reminder/callhandle?"
                . "pname=" . urlencode($pname) . "&"
                . "patient_lname=" . urlencode($patient_lname) . "&"
                . "pvname=" . urlencode($pvname) . "&"
                . "cname=" . urlencode($cname) . "&"
                . "aDate=" . urlencode($aDate) . "&"
                . "aTime=" . urlencode($aTime) . "&"
                . "address=" . urlencode($address) . "&"
                . "type=" . urlencode($type) . "&"
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
            <Gather  timeout='3' numDigits='1' action='" . $base_url . "cron_visit_booking_reminder/step_two?"
        . "pname=" . urlencode($_GET['pname']) . "&amp;"
        . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
        . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
        . "cname=" . urlencode($_GET['cname']) . "&amp;"
        . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
        . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
        . "address=" . urlencode($_GET['address']) . "&amp;"
        . "type=" . urlencode($_GET['type']) . "&amp;"
        . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
        . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
        . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
        . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
        . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
        . "notify_email=" . urlencode($_GET["notify_email"]) . "' method='GET'>
                <Say  voice='Polly.Joanna'> Hello </Say>
                <Pause length='1'/>
                <Say voice='Polly.Joanna'> This is an automated appointment call for  <emphasis level='moderate'>" . $_GET['pname'] . "  " . $_GET['patient_lname'] . "</emphasis>.</Say>
                <Pause length='1'/>
                <Say voice='Polly.Joanna'> If you are  <emphasis level='moderate'>" . $_GET['pname'] . "  " . $_GET['patient_lname'] . "</emphasis> , please enter 1 to continue. If this is the wrong number, please type 2 to end the call</Say>
				</Gather>
            <Pause length='10'/>
            <Redirect method='GET'>
            " . $base_url . "cron_visit_booking_reminder/callhandle?"
        . "pname=" . urlencode($_GET['pname']) . "&amp;"
        . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
        . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
        . "cname=" . urlencode($_GET['cname']) . "&amp;"
        . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
        . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
        . "address=" . urlencode($_GET['address']) . "&amp;"
        . "type=" . urlencode($_GET['type']) . "&amp;"
        . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
        . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
        . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
        . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
        . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
        . "notify_email=" . urlencode($_GET["notify_email"]) .
        "</Redirect>
        </Response>";
    }

    public function step_two() {

        $clinic_id = $_GET["clinic_id"];
        $patient_id = $_GET["patient_id"];
        $reserved_id = $_GET["reserved_id"];

        $reserved_data = $this->db->select("*")->from("records_patient_visit_reserved")->where(array(
                    "id" => $reserved_id
                ))->get()->result()[0];

        $date1 = date('F jS', strtotime($reserved_data->visit_date1 . " " . $reserved_data->visit_start_time1));
        $day1 = date('l', strtotime($reserved_data->visit_date1 . " " . $reserved_data->visit_start_time1));
        $time1 = date('g:i a', strtotime($reserved_data->visit_date1 . " " . $reserved_data->visit_start_time1));
        $date2 = date('F jS', strtotime($reserved_data->visit_date2 . " " . $reserved_data->visit_start_time2));
        $day2 = date('l', strtotime($reserved_data->visit_date2 . " " . $reserved_data->visit_start_time2));
        $time2 = date('g:i a', strtotime($reserved_data->visit_date2 . " " . $reserved_data->visit_start_time2));
        $date3 = date('F jS', strtotime($reserved_data->visit_date3 . " " . $reserved_data->visit_start_time3));
        $day3 = date('l', strtotime($reserved_data->visit_date3 . " " . $reserved_data->visit_start_time3));
        $time3 = date('g:i a', strtotime($reserved_data->visit_date3 . " " . $reserved_data->visit_start_time3));

        if (isset($_GET["Digits"])) {
            $base_url = "http://35.203.47.37/";
            if ($_GET['Digits'] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "cron_visit_booking_reminder/step_three?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
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
                echo "<Redirect method='GET'>" . $base_url . "cron_visit_booking_reminder/step_two?"
                . "Digits=1&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
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

                //set visit status
                $this->db->where(array(
                    "id" => $reserved_id
                ))->update("records_patient_visit", array(
                    "notify_status" => "Wrong Number",
                    "notify_status_icon" => "blue"
                ));

                log_message("error", "wrong number 1 => " . $this->db->last_query());

                //set status in accepted_status
                $referral_id = $this->db->select("c_ref.id")
                                ->from("clinic_referrals c_ref, referral_patient_info pat")
                                ->where(array(
                                    "pat.id" => $_GET["patient_id"]
                                ))->get()->result()[0]->id;

                $this->db->where(array(
                    "id" => $referral_id
                ))->update("clinic_referrals", array(
                    "accepted_status" => "Wrong Number",
                    "accepted_status_icon" => "blue",
                    "accepted_status_date" => date("Y-m-d H:i:s")
                ));
                log_message("error", "wrong number 2=> " . $this->db->last_query());



                //all visits book status changed to wrong number
                $this->db->where(array(
                    "patient_id" => $_GET["patient_id"]
                ))->update("records_patient_visit", array(
                    "notify_status" => "Wrong Number",
                    "notify_status_icon" => "blue"
                ));
                log_message("error", "all visits wrong number = > " . $this->db->last_query());

//                $params = array(
//                    'data' => $_GET["Digits"],
//                    'to' => $_GET['To'],
//                    'patient_id' => $_GET["patient_id"]
//                );
//                $defaults = array(
//                    CURLOPT_URL => $base_url . "cron_visit_booking_reminder/vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc",
//                    CURLOPT_POST => true,
//                    CURLOPT_POSTFIELDS => http_build_query($params)
//                );
//                $ch = curl_init($base_url . "cron_visit_booking_reminder/vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc");
//                curl_setopt_array($ch, $defaults);
//                curl_exec($ch);
//                curl_close($ch);
            } else {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Redirect method='GET'>" . $base_url . "cron_visit_booking_reminder/callhandle?pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
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
            $base_url = "http://35.203.47.37/";
            if ($_GET['Digits'] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "cron_visit_booking_reminder/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "selected_slot=" . 1 . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected  <emphasis level='moderate'>" . $day1 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date1 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time1 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>" . $base_url . "cron_visit_booking_reminder/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 2) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "cron_visit_booking_reminder/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "selected_slot=" . 2 . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected  <emphasis level='moderate'>" . $day2 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date2 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time2 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>" . $base_url . "cron_visit_booking_reminder/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 3) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "cron_visit_booking_reminder/step_four?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "selected_slot=" . 3 . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "' method='GET'>";
                echo "<Say voice='Polly.Joanna'> You have selected   <emphasis level='moderate'>" . $day3 . "<say-as interpret-as='date' format='ddmmyyyy'  detail='1'>" . $date3 . " </say-as>     at   <say-as interpret-as='time' format='hms12'>  " . $time3 . " </say-as></emphasis></Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>if this is correct, enter 1 to confirm.</Say>";
                echo "<Pause length='1'/>";
                echo "<Say voice='Polly.Joanna'>If this is incorrect, enter 2 to select another date. </Say>";
                echo "</Gather>";
                echo "<Pause length='4'/>";
                echo "<Redirect method='GET'>" . $base_url . "cron_visit_booking_reminder/step_three?"
                . "Digits=" . $_GET['Digits'] . "&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 0) {
                echo "<Response><Say voice='Polly.Joanna' >Thank-you, the clinic will be in touch shortly'</Say></Response>";
                $reserved_id = $_GET["reserved_id"];
                $reserved_data = $this->db->select("*")->from("records_patient_visit_reserved")->where(array(
                            "id" => $reserved_id
                        ))->get()->result_array()[0];

                $get = $_GET;
                //insert in scheduled visit
                $insert_data = array(
                    "patient_id" => $get["patient_id"],
                    "visit_name" => $get["pvname"],
                    "notify_type" => $reserved_data->notify_type,
                    "notify_voice" => $reserved_data->notify_voice,
                    "notify_sms" => $reserved_data->notify_sms,
                    "notify_email" => $reserved_data->notify_email,
                    "visit_confirmed" => "Change required",
                    "notify_status" => "Contact directly",
                    "notify_status_icon" => "yellow"
                );
                $this->db->insert("records_patient_visit", $insert_data);

                //disable from reserved table
                $this->db->where(array(
                    "id" => $reserved_id
                ));
                $this->db->update("records_patient_visit_reserved", array(
                    "active" => 0,
                    "visit_confirmed" => "Booked"
                ));

                //set status in accepted_status
                $referral_id = $this->db->select("c_ref.id")
                                ->from("clinic_referrals c_ref, referral_patient_info pat")
                                ->where(array(
                                    "pat.id" => $get["patient_id"]
                                ))
                                ->where("c_ref.id", "pat.referral_id", false)
                                ->get()->result()[0]->id;

                $this->db->where(array(
                    "id" => $referral_id
                ))->update("clinic_referrals", array(
                    "accepted_status" => "Contact directly",
                    "accepted_status_icon" => "yellow",
                    "accepted_status_date" => date("Y-m-d H:i:s")
                ));
                log_message("error", "updated for 0 with sql = $referral_id  => " . $this->db->last_query());
            } elseif ($_GET['Digits'] == 4) {
                log_message("error", "for 44444 =>.>>> " . json_encode($_GET));
                echo "<Response>";
                echo "<Redirect method='GET'>" . $base_url . "cron_visit_booking_reminder/step_two?"
                . "Digits=1&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;</Redirect>";
                echo "</Response>";
            } else {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Say voice='Polly.Joanna' >You entered wrong digit</Say></Response>";
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
            $base_url = "http://35.203.47.37/";
            if ($_GET['Digits'] == 2) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Gather  timeout='3' numDigits='1' action='" . $base_url . "cron_visit_booking_reminder/step_three?"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
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
                echo "<Redirect method='GET'>" . $base_url . "cron_visit_booking_reminder/step_four?"
                . "Digits=4&amp;"
                . "pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "date1=" . urlencode($date1) . "&amp;"
                . "day1=" . urlencode($day1) . "&amp;"
                . "time1=" . urlencode($time1) . "&amp;"
                . "date2=" . urlencode($date2) . "&amp;"
                . "day2=" . urlencode($day2) . "&amp;"
                . "time2=" . urlencode($time2) . "&amp;"
                . "date3=" . urlencode($date3) . "&amp;"
                . "day3=" . urlencode($day3) . "&amp;"
                . "time3=" . urlencode($time3) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;</Redirect>";
                echo "</Response>";
            } elseif ($_GET['Digits'] == 1) {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response>";
                echo "<Say  voice='Polly.Joanna'>Thank you</Say>";
                echo "<Hangup/>";
                echo "</Response>";

                $num = $_GET['selected_slot'];
                if ($num == 1 || $num == 2 || $num == 3) {
                    $reserved_id = $_GET["reserved_id"];
                    $reserved_data = $this->db->select("*")->from("records_patient_visit_reserved")->where(array(
                                "id" => $reserved_id
                            ))->get()->result_array()[0];

                    $visit_date = $reserved_data["visit_date" . $num];
                    $visit_time = $reserved_data["visit_start_time" . $num];
                    $visit_end_time = $reserved_data["visit_end_time" . $num];

                    $get = $_GET;

                    $insert_data = array(
                        "patient_id" => $get["patient_id"],
                        "visit_name" => $get["pvname"],
                        "visit_date" => $visit_date,
                        "visit_time" => $visit_time,
                        "visit_end_time" => $visit_end_time,
                        "notify_type" => $reserved_data["notify_type"],
                        "notify_voice" => $reserved_data["notify_voice"],
                        "notify_sms" => $reserved_data["notify_sms"],
                        "notify_email" => $reserved_data["notify_email"],
                        "visit_confirmed" => "Awaiting Confirmation",
                        "notify_status" => $reserved_data["notify_status"],
                        "notify_status_icon" => "green"
                    );
                    //insert in scheduled visit
                    $this->db->insert("records_patient_visit", $insert_data);

                    $this->db->where(array(
                        "id" => $reserved_id
                    ));
                    $this->db->update("records_patient_visit_reserved", array(
                        "active" => 0,
                        "visit_confirmed" => "Booked"
                    ));


                    //set status in accepted_status
                    $referral_id = $this->db->select("c_ref.id")
                                    ->from("clinic_referrals c_ref, referral_patient_info pat")
                                    ->where(array(
                                        "pat.id" => $get["patient_id"]
                                    ))
                                    ->where("c_ref.id", "pat.referral_id", false)
                                    ->get()->result()[0]->id;

                    $this->db->where(array(
                        "id" => $referral_id
                    ))->update("clinic_referrals", array(
                        "accepted_status" => "Confirmed",
                        "accepted_status_icon" => "green",
                        "accepted_status_date" => $reserved_data["create_datetime"]
                    ));

                    $this->load->model("referral_model");
                    $this->referral_model->move_from_accepted_to_scheduled($get["patient_id"], $clinic_id);
                }
            } else {
                echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
                echo "<Response><Redirect method='GET'>" . $base_url . "cron_visit_booking_reminder/callhandle?pname=" . urlencode($_GET['pname']) . "&amp;"
                . "patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;"
                . "clinic_id=" . urlencode($_GET["clinic_id"]) . "&amp;"
                . "patient_id=" . urlencode($_GET["patient_id"]) . "&amp;"
                . "reserved_id=" . urlencode($_GET["reserved_id"]) . "&amp;"
                . "notify_voice=" . urlencode($_GET["notify_voice"]) . "&amp;"
                . "notify_sms=" . urlencode($_GET["notify_sms"]) . "&amp;"
                . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
                . "pvname=" . urlencode($_GET['pvname']) . "&amp;"
                . "cname=" . urlencode($_GET['cname']) . "&amp;"
                . "aDate=" . urlencode($_GET['aDate']) . "&amp;"
                . "aTime=" . urlencode($_GET['aTime']) . "&amp;"
                . "address=" . urlencode($_GET['address']) . "&amp;"
                . "type=" . urlencode($_GET['type']) . "&amp;"
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

    public function call_resp() {
        //if(isset($_GET['pname'])){
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>";
        echo "<Gather  NumDigits='1' action='" . "http://35.203.47.37/" . "cron_visit_booking_reminder/call_resp_handle/' method='GET'>";
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
        $data = $this->referral_model->assign_slots(30, 1);
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
                "r_pv.active" => 1
            ))->group_start()->where(array(
                "pat.cell_phone" => $From
            ))->or_where(array(
                "pat.home_phone" => $From
            ))->or_where(array(
                "pat.work_phone" => $From
            ))->group_end();
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
