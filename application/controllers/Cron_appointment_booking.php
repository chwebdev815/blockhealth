<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cron_appointment_booking extends CI_Controller {

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
        $remindable = $this->db->select("*")->from("records_patient_visit")->where(array(
//            "concat(visit_date, ' ', visit_time) > " => $string_plus_72_hour,
//                    "concat(visit_date, ' ', visit_time) < " => $string_plus_72_hour_5_min,
                    "visit_confirmed" => "Confirmed",
                ))->get()->result();

        echo $this->db->last_query() . "<br/><br/>";
        echo json_encode($remindable) . "<br/><br/>";

        $this->load->model("referral_model");
        foreach ($remindable as $key => $value) {
            $visit = $value;
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


                $post_arr = array(
                    'defaultContactFormName' => $call_data->fname,
                    "patient_lname" => $call_data->lname,
                    "defaultContactFormName2" => $visit->visit_name,
                    'defaultContactFormName3' => $call_data->clinic_institution_name,
                    'defaultContactFormName4' => "ddd",
                    'defaultContactFormName5' => "ttt",
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
                curl_setopt($ch, CURLOPT_URL, base_url() . "call_view/call");
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

}
