<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule_appointment_remainder extends CI_Controller {

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
    	log_message("error", "############# =============== >>>>>>>>>>>>>>>>>>>> SMS Schedular called");
        $now = date('Y-m-d H:i:s');
        $this->db->select("c_pv.visit_name, date_format(`c_pv`.`visit_date`,'%M %D, %Y') as visit_date, " .
                "date_format(`c_pv`.`visit_time`, '%I:%i %p') as visit_time, c_pv.notify_sms, c_pv.notify_voice, c_pv.notify_email, admin.address, admin.call_address, " .
                "pat.email_id, pat.cell_phone, pat.fname, admin.clinic_institution_name");
        $this->db->from("`records_patient_visit` `c_pv`, referral_patient_info pat, efax_info efax, clinic_user_info admin, `clinic_referrals` `c_ref`");
        $this->db->join("clinic_physician_info c_dr", "c_ref.assigned_physician = c_dr.id and c_dr.active = 1", "left");
        $this->db->where(
                array(
                    "c_pv.active" => 1,
                    "efax.active" => 1,
                    "admin.active" => 1,
                    "c_ref.active" => 1,
                    "pat.active" => 1
                )
        );
        $this->db->where("pat.referral_id", "c_ref.id", false);
        $this->db->where("c_pv.patient_id", "pat.id", false);
        $this->db->where("efax.`to`", "admin.id", false);
        $this->db->where("`c_ref`.efax_id", "efax.id", false);
        $this->db->where("concat(c_pv.visit_date, ' ', c_pv.visit_time) > DATE_ADD('$now', INTERVAL +1440 HOUR_MINUTE)");
        $this->db->where("concat(c_pv.visit_date, ' ', c_pv.visit_time) <= DATE_ADD('$now', INTERVAL +1445 HOUR_MINUTE)");
        $result = $this->db->get()->result();
        log_message("error", "schedular sms sql = " . $this->db->last_query());
        if ($result) {
            log_message("error", "sms remainder sent to " . json_encode($result));
            $msg_data = $result[0];
            $visit_name = (empty($msg_data->visit_name))?"":" '$msg_data->visit_name'";
            //check sms and send notification
            log_message("error", "sms = $msg_data->notify_sms and mail = $msg_data->notify_email");
            $first_name = $msg_data->fname;
            if ($msg_data->notify_sms == "1") {
                $msg = "Hello $first_name,\n\n".
                        "Your appointment$visit_name with $msg_data->clinic_institution_name".
                        " is tomorrow ($msg_data->visit_date) at $msg_data->visit_time.\n\n".
                        "The address is:\n".
                        "$msg_data->address\n\n".
                        "Please make sure to arrive on time. Thanks!";

                log_message("error", "sms = " . $msg);
                $this->load->model("referral_model");
                $response = $this->referral_model->send_sms($msg_data->cell_phone, $msg);
                log_message("error", "response = " . json_encode($response));
            }
            //send patient visit booked email
            if ($msg_data->notify_email == "1") {
                //template implement starts
                $template = file_get_contents("assets/templates/email_day_of.html");
                $template = str_replace("<name/>", $msg_data->fname, $template);
                $template = str_replace("<patientVisitName/>", $visit_name, $template);
                $template = str_replace("<clinicName/>", $msg_data->clinic_institution_name, $template);
                $template = str_replace("<clinicAddress/>", $msg_data->address, $template);
                $template = str_replace("<time/>", $msg_data->visit_time, $template);

                log_message("error", "email = " . $template);
                //template implement ends
                //send mail informing ticket raised
                // old mail code starts
//                $this->load->library('email');
//                $this->email->from($this->email->smtp_user, "BlockHealth");
//                $this->email->to($msg_data->email_id);
//                $this->email->subject($msg_data->clinic_institution_name . ": Appointment Reminder");
//                $this->email->message($template);
//                $this->email->send();
                //old mail code ends
                $response = send_mail("", "BlockHealth", $msg_data->email_id, "", $msg_data->clinic_institution_name . ": Appointment Reminder", $template);
            }
            if($msg_data->notify_voice == "1") {
                $post_arr = array(
                    'defaultContactFormName' => $msg_data->fname,
                    'defaultContactFormName2' => $visit_name,
                    'defaultContactFormName3' => $msg_data->clinic_institution_name,
                    'defaultContactFormName4' => $msg_data->visit_date,
                    'defaultContactFormName5' => $msg_data->visit_time,
                    'defaultContactFormName6' => $msg_data->cell_phone,
                    'address' => $msg_data->call_address,
                    'type' => '24hReminder'
                );

                log_message("error", "Call should start now");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_URL, base_url()."call_view/call");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_arr));
                $resp = curl_exec($ch);
                if (curl_errno($ch)) {
                    return curl_error($ch);
                }
                curl_close($ch);
                log_message("error", "Call completed " . json_encode($resp));
            }
        }
    }

}
