<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_appointment_reminder extends CI_Controller {
//Confirmation cron job 2day (dynamic) to visit datetime

    public function index() {
        if (isset($argv)) {
            if (!empty($argv[1])) {
                switch ($argv[1]) {
                    case "ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE":
                        $this->ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE();
//                        log_message("error", "Called function ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE");
                        break;
                }
            }
        }
    }

    public function ujEtsjgFvRIJZOtbOhidSXqaUxFSltiE() {

//        log_message("error", "Cron_appointment_reminder called $$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");
        //get all clinic and loop all
        $clinics = $this->db->select("id, visit_confirm_time")
                        ->from("clinic_user_info")->where("active", 1)->get()->result();
//        log_message("error", "Clinic users for timing = " . json_encode($clinics));
        if ($clinics) {
            foreach ($clinics as $key => $clinic) {
//                log_message("error", "clinic = " . $clinic->id);
                $hour = $clinic->visit_confirm_time;
                $day = floor($hour / 24);
                $hour = ($hour % 24);

                //get all to schedule a call for specific clinic
                $remind_hour = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("+$day day $hour hour")));
                $string_remind_hour = $remind_hour->format("Y-m-d H:i:s");
                $remind_hour_5min = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("+$day day $hour hour 5 minute")));
                $string_remind_hour_5min = $remind_hour_5min->format("Y-m-d H:i:s");
                $remindable = $this->db->select("r_pv.*")
                                ->from("records_patient_visit r_pv, referral_patient_info pat, "
                                        . "clinic_referrals c_ref, efax_info efax, clinic_user_info c_usr")
                                ->where(array(
                                    "concat(r_pv.visit_date, ' ', r_pv.visit_time) > " => $string_remind_hour,
                                    "concat(r_pv.visit_date, ' ', r_pv.visit_time) < " => $string_remind_hour_5min,
                                    "r_pv.visit_confirmed" => "N/A",
                                    "pat.active" => 1,
                                    "c_ref.active" => 1,
                                    "c_usr.active" => 1,
                                    "r_pv.active" => 1
                                ))
                                ->where("pat.id", "r_pv.patient_id", false)
                                ->where("c_ref.id", "pat.referral_id", false)
                                ->where("efax.id", "c_ref.efax_id", false)
                                ->where("c_usr.id", "efax.to", false)
                                ->where("efax.to", "c_usr.id", false)
                                ->where("c_usr.id", $clinic->id)
                                ->get()->result();
//                log_message("error", "calculating reminder = " . $this->db->last_query());
                $this->init_reminder($remindable);
//        $remindable = $this->db->select("*")->from("records_patient_visit")->where(array(
//                    "id" => 47
//                ))->get()->result();
            }
        }
    }

    public function init_reminder($remindable) {

//        log_message("error", "init => " . json_encode($remindable) . "<br/><br/>");

        $this->load->model("referral_model");
        foreach ($remindable as $key => $value) {
            $visit = $value;
            //update it to awaiting confirmation
            $this->db->where("id", $visit->id)->update("records_patient_visit", array(
                "visit_confirmed" => "Awaiting Confirmation"
            ));
//            log_message("error", "update visit " . $visit->id . " before call");
//            log_message("error", "q = " . $this->db->last_query());


            //get clinic id for patient
            $this->db->select('admin.id as clinic_id, '
                    . 'CASE WHEN (pat.cell_phone = NULL OR pat.cell_phone = "") '
                    . 'THEN "false" ELSE "true" END AS allow_sms, '
                    . 'CASE WHEN (pat.email_id = NULL OR pat.email_id = "") '
                    . 'THEN "false" ELSE "true" END AS allow_email, ' .
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
            $patient_data = $this->db->get()->result();

            if ($patient_data) {
                $patient_data = $patient_data[0];

//                log_message("error", "checkig for patient " . $visit->patient_id);
                $contact_number = $patient_data->cell_phone;
                if ($patient_data->home_phone != "") {
                    //home number
                    $contact_number = $patient_data->home_phone;
                } else if ($patient_data->work_phone != "") {
                    //work number
                    $contact_number = $patient_data->work_phone;
                }

                if ($visit->notify_type == "call" || 1) {

                    $new_visit_duration = 30;
                    //find asignable slots
                    $allocations = null;
                    //make call with proper data

                    $response = $this->referral_model->assign_slots($new_visit_duration, $visit->patient_id);
                    if ($response["result"] === "error") {
                        continue;
                    } else if ($response["result"] === "success") {
                        $allocations = $response["data"];

                        $post_arr = array(
                            'defaultContactFormName' => $patient_data->fname,
                            "patient_lname" => $patient_data->lname,
                            "defaultContactFormName2" => $visit->visit_name,
                            'defaultContactFormName3' => $patient_data->clinic_institution_name,
                            'defaultContactFormName4' => $visit->visit_date,
                            'defaultContactFormName5' => $visit->visit_time,
                            'defaultContactFormName6' => $contact_number,
                            'address' => $patient_data->call_address,
                            'clinic_id' => $patient_data->clinic_id,
                            'type' => 'Call reminder before 48 hour',
                            "patient_id" => $visit->patient_id,
                            "notify_voice" => $visit->notify_voice,
                            "notify_sms" => $visit->notify_sms,
                            "notify_email" => $visit->notify_email,
                            "reserved_id" => $visit->id
                        );

//                        log_message("error", "Call should start now");
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_URL, base_url() . "cron_appointment_reminder/call");
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_arr));
                        $resp = curl_exec($ch);
                        if (curl_errno($ch)) {
//                            log_message("error", "Call error => " . json_encode(curl_error($ch)));
                            return curl_error($ch);
                        }
                        curl_close($ch);
//                        log_message("error", "Call completed " . json_encode($resp));
                    }
                }
                if ($visit->notify_type === "sms" || 1) {
//                    log_message("error", "before sms data = " . json_encode($visit));
                    if ($visit->visit_name && $visit->visit_name != "") {
                        $visit->visit_name = "'" . $visit->visit_name . "'";
                    }
                    $msg = "Hello <patient name>,\n"
                            . "\n"
                            . "Your appointment<patient visit name> with <clinic name> has been booked for <date> at <time>.\n"
                            . "\n"
                            . "The address is:\n"
                            . "<Address>\n"
                            . "\n"
                            . "Please type 1 to confirm this booking. "
                            . "If this date does not work, please type 2 to alert the clinic staff.\n"
                            . "Thank-you.";
                    $msg = str_replace("<patient name>", $patient_data->fname, $msg);
                    $msg = str_replace("<date>", $visit->visit_date, $msg);
                    $msg = str_replace("<time>", $visit->visit_time, $msg);
                    $msg = str_replace("<patient visit name>", $visit->visit_name, $msg);
                    $msg = str_replace("<clinic name>", $patient_data->clinic_institution_name, $msg);
                    $msg = str_replace("<Address>", $patient_data->address, $msg);

                    $this->referral_model->send_sms($contact_number, $msg);
                }
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
//        $mob = "+917201907712";
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
        $twilio_number = "+16475607989";
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
//            log_message("error", "Calling");
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
        . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;"
        . "Digits=timeout</Redirect></Response>";
    }

    public function step_two() {

        $clinic_id = $_GET["clinic_id"];
        $patient_id = $_GET["patient_id"];
        $reserved_id = $_GET["reserved_id"];


        $base_url = "http://35.203.47.37/";

        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        if ($_GET['Digits'] == 1) {
            echo "<Response><Say voice='Polly.Joanna'>Thank you, your appointment has been confirmed </Say></Response>";
            //set status for visit
            $this->db->where(array(
                "id" => $reserved_id
            ))->update("records_patient_visit", array(
                "visit_confirmed" => "Confirmed"
            ));

            //set status in accepted_status
//            $referral_id = $this->db->select("c_ref.id")
//                            ->from("clinic_referrals c_ref, referral_patient_info pat")
//                            ->where(array(
//                                "pat.id" => $patient_id
//                            ))
//                            ->where("c_ref.id", "pat.referral_id", false)
//                            ->get()->result()[0]->id;
//            $this->db->where(array(
//                "id" => $referral_id
//            ))->update("clinic_referrals", array(
//                "accepted_status" => "Confirmed",
//                "accepted_status_icon" => "green"
//            ));
        } elseif ($_GET['Digits'] == 2) {
            echo "<Response><Say voice='Polly.Joanna'>Thank you, the clinic has been notified and will be in touch shortly</Say></Response>";
            $this->db->where(array(
                "id" => $reserved_id
            ))->update("records_patient_visit", array(
                "visit_confirmed" => "Change required",
                "notify_status" => "Contact directly",
                "notify_status_icon" => "yellow"
            ));

            //set status in accepted_status
            $referral_id = $this->db->select("c_ref.id")
                            ->from("clinic_referrals c_ref, referral_patient_info pat")
                            ->where(array(
                                "pat.id" => $_GET["patient_id"]
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
            //,
//                    "accepted_status_date" => date("Y-m-d")
        } elseif ($_GET['Digits'] == 3) {
            echo "<Response><Redirect method='GET'>" .
            $base_url . "cron_appointment_reminder/callhandle?"
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
            . "notify_email=" . urlencode($_GET["notify_email"]) . "&amp;Digits=timeout</Redirect></Response>";
        } else {
            echo "<Response><Say voice='Polly.Joanna' >You entered wrong digit</Say></Response>";
        }
    }

}
