<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function filter_only_numbers($mixed_value) {
//    $filter = '!a-b.c3@j+dk9.0$3e8`~]\]2';
    $filtered_value = str_replace(['+', '-'], '', filter_var($mixed_value, FILTER_SANITIZE_NUMBER_INT));
    return $filtered_value;
}

function referring_physician_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') &&
            $CI->session->userdata('login_role') === "referring_physician") {
        return true;
    } else {
        return false;
    }
}

function clinic_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && ($CI->session->userdata('login_role') === "clinic_admin" || $CI->session->userdata('login_role') === "clinic_physician")) {
        return TRUE;
    } else {
        return false;
    }
}

function clinic_admin_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && ($CI->session->userdata('login_role') === "clinic_admin")) {
        return TRUE;
    } else {
        return false;
    }
}

function clinic_physician_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && ($CI->session->userdata('login_role') === "clinic_physician")) {
        return TRUE;
    } else {
        return false;
    }
}

function admin_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && $CI->session->userdata('login_role') === "admin") {
        return true;
    } else {
        return false;
    }
}

function get_decrypted_id($md5_id, $table_name) {
    $CI = & get_instance();
    $CI->db->select("id");
    $CI->db->from($table_name);
    $CI->db->where(array("md5(id)" => $md5_id));
    $result = $CI->db->get()->result();
    //log_message("error", "get decrypted sql = " . $CI->db->last_query());
    return ($result) ? $result[0]->id : 0;
}

function tracker_assets() {
    return base_url() . "assets/tracker/";
}

function generate_random_string($length = 32) {
    $timestamp = time();
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $timestamp . "_" . $randomString;
}

function get_pdf_page_count($filepath) {
    //log_message("error", "function get_pdf_page_count");
    $fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $filepath), "r");
    $max = 0;
    if (!$fp) {
        //log_message("error", "not fp");
        return 0;
    } else {
        while (!@feof($fp)) {
            // //log_message("error", "while loop ");
            $line = @fgets($fp, 255);
            if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                //log_message("error", "if yes");
                preg_match('/[0-9]+/', $matches[0], $matches2);
                if ($max < $matches2[0]) {
                    //log_message("error", "if yes 2");
                    $max = trim($matches2[0]);
                    break;
                }
            }
            // //log_message("error", "current max = $max");
        }
        @fclose($fp);
    }
    return $max;
}

function add_fax_count($sender, $receiver, $clinic_id, $fax_type, $login_role = "") {
    $CI = & get_instance();
    if (!$login_role || $login_role === "") {
        $login_role = $CI->session->userdata("login_role");
    }
    if ($sender === NULL) {
        $sender = "123";
    }
    $CI->db->insert("count_sent_fax", array(
        "sender" => $sender,
        "receiver" => $receiver,
        "login_user_id" => $clinic_id,
        "login_role" => $login_role,
        "fax_type" => $fax_type
    ));
}

function send_mail($from, $from_name, $to, $to_name, $subject, $content, $attachment = "", $file_name = "attachment.pdf") {
    //log_message("error", "SENDGRID - sending mail to $to");
    if (empty($from)) {
        $from = "alerts@blockhealth.co";
    }
    if (empty($to_name)) {
        $to_name = $to;
    }

    require 'vendor/sendgrid-php/vendor/autoload.php';
    $email = new \SendGrid\Mail\Mail();

    $email->setFrom($from, $from_name);
    $email->setSubject($subject);
    $email->addTo($to, $to_name);
//    $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
    $email->addContent("text/html", $content);
    $sendgrid = new \SendGrid('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');

    try {
        $response = $sendgrid->send($email);
        return true;
        //log_message("error", "mail status code = " . json_encode($response->statusCode()));
        //log_message("error", "mail header code = " . json_encode($response->headers()));
        //log_message("error", "mail body code = " . json_encode($response->body()));
    } catch (Exception $e) {
        //log_message("error", "Error while mail - " . $e->getMessage());
        return false;
    }
}

function make_two_digit($digit) {
    //check if number
    try {
        $digit = (int) $digit;
        if ($digit < 10) {
            return "0" . $digit;
        } else {
            return "" . $digit;
        }
    } catch (Exception $ex) {
        //log_message("error", "error while converting to two digit from $digit");
        return "00";
    }
}

function files_dir() {
//    return "/var/emrsftp/clinics/";
    return "uploads/clinics/";
}

function get_metadata_path($md5_clinic_id) {
    return "/var/emrsftp/clinics/$md5_clinic_id/metadata.json";
}

function get_twilio_sid() {
    return "AC2da3b84b65b63ccf4f05c27ac1713060";
}

function get_twilio_token() {
    return "342a214ee959d16bf97ea87579016762";
}

function save_json($clinic_id, $data_object) {
    $clinic_dir = "uploads/clinics/" . md5($clinic_id);
    $arr_data = array(); // create empty array
    try {
        $myFile = $clinic_dir . "/metadata.json";
        if (!file_exists($clinic_dir)) {
            mkdir($clinic_dir);
        }
        if (!file_exists($myFile)) {
            $file_object = fopen($myFile, "w");
            fwrite($file_object, "[]");
            fclose($file_object);
        }

        //Get data from existing json file
        $jsondata = file_get_contents($myFile);

        // converts json data into array
        $arr_data = json_decode($jsondata, true);

        // Push user data to array
        array_push($arr_data, $data_object);

        //Convert updated array to JSON
        $jsondata = json_encode($arr_data, JSON_PRETTY_PRINT);

        //write json data into data.json file
        if (file_put_contents($myFile, $jsondata)) {
            return array(
                "result" => "success"
            );
        } else {
            return array(
                "result" => "error",
                "message" => "Failed to save data"
            );
        }
//            echo "error";
    } catch (Exception $e) {
        return array(
            "result" => "error",
            "message" => $e->getMessage()
        );
    }
}

function convert_priority_to_display_name($db_priority) {
    switch ($db_priority) {
        case "routine":
            return "Routine";
        case "sub_urgent":
            return "Sub Urgent";
        case "urgent":
            return "Urgent";
        case "not_specified":
            return "";
    }
}

function is_physician_under_current_clinic($physician_id) {
    $CI = & get_instance();
    $physician_data = $CI->db->select("c_dr.id")
                    ->from("clinic_physician_info c_dr")
                    ->where(array(
                        "c_dr.active" => 1,
                        "c_dr.clinic_id" => $CI->session->userdata("user_id")
                    ))->get()->result();

    if ($physician_data) {
        return true;
    } else {
        return false;
    }
}

function session_expired() {
    return array(
        "result" => "error",
        "message" => "Session has expired"
    );
}

function patient_visit_integration($type, $patient_id, $appointment_id, $update_data = array()) {
    $CI = & get_instance();
    if ($type === "update") {
        $CI->db->where(array(
            "bh_appointment_no" => $appointment_id
        ))->update("records_patient_visit_integration", $update_data);
    }
    if ($type === "insert") {
//        $clinic_id = $CI->session->userdata("user_id");

        $clinic_data = $CI->db->select("c_usr.id, c_usr.emr_pathway")
                        ->from("clinic_user_info c_usr, referral_patient_info pat, "
                                . "clinic_referrals c_ref, efax_info efax")
                        ->where(array(
                            "c_usr.active" => 1,
                            "pat.active" => 1,
                            "efax.active" => 1,
                            "c_ref.active" => 1,
                            "pat.id" => $patient_id
                        ))
                        ->where("pat.referral_id", "c_ref.id", false)
                        ->where("c_ref.efax_id", "efax.id", false)
                        ->where("efax.to", "c_usr.id", false)
                        ->get()->result();
        
        $clinic_id = null;
        if($clinic_data) {
            $clinic_id = $clinic_data[0]->id;
        }
        else {
            //log_message("error", "INTEGRATION DATA NOT SAVED");
            //log_message("error", "false q = " . $CI->db->last_query());
            return;
        }

        $appt_data = $CI->db->select("visit_date, visit_time, visit_end_time, "
                                . "if(visit_confirmed = 'Confirmed', 'yes', 'no') as "
                                . "is_visit_confirmed")
                        ->from("records_patient_visit")
                        ->where(array(
                            "active" => 1,
                            "id" => $appointment_id
                        ))->get()->result();

        $patient_info = $CI->db->select("pat.next_visit, pat.fname, pat.lname, pat.dob, "
                                . "pat.ohip, pat.gender, pat.cell_phone, "
                                . "pat.home_phone, pat.work_phone, pat.email_id, pat.address,"
                                . "c_loc.sms_address")
                        ->from("referral_patient_info pat")
                        ->join("clinic_locations c_loc", "c_loc.active = 1 and c_loc.clinic_id = '$clinic_id' and "
                                . "pat.location_id = c_loc.id", "left")
                        ->where(array(
                            "pat.active" => 1,
                            "pat.id" => $patient_id
                        ))
                        ->get()->result();

        $provider_info = $CI->db->select("c_dr.id, c_dr.first_name, c_dr.last_name, c_dr.emr_provider_id")
                        ->from("clinic_physician_info c_dr, "
                                . "clinic_referrals c_ref, "
                                . "referral_patient_info pat")
                        ->where(array(
                            "pat.active" => 1,
                            "c_ref.active" => 1,
                            "c_dr.active" => 1,
                            "pat.id" => $patient_id
                        ))
                        ->where("pat.referral_id", "c_ref.id", false)
                        ->where("c_dr.id", "c_ref.assigned_physician", false)
                        ->get()->result();


        if ($clinic_data && $appt_data && $provider_info) {

            $clinic_data = $clinic_data[0];
            $appt_data = $appt_data[0];
            $provider_info = $provider_info[0];
            $patient_info = $patient_info[0];

            $CI->db->insert("records_patient_visit_integration", array(
                //patient info
                "bh_demographic_id" => $patient_id,
                "pat_fname" => $patient_info->fname,
                "pat_lname" => $patient_info->lname,
                "pat_dob" => $patient_info->dob,
                "pat_ohip" => $patient_info->ohip,
                "pat_gender" => $patient_info->gender,
                "pat_cell_phone" => $patient_info->cell_phone,
                "pat_home_phone" => $patient_info->home_phone,
                "pat_work_phone" => $patient_info->work_phone,
                "pat_email_id" => $patient_info->email_id,
                "pat_address" => $patient_info->address,
                "visit_type" => $patient_info->next_visit,
                "location" => $patient_info->sms_address,
                //referring physician info
                "bh_provider_no" => $provider_info->id,
                "dr_fname" => $provider_info->first_name,
                "dr_lname" => $provider_info->last_name,
                "emr_provider_no" => $provider_info->emr_provider_id,
                //appointment info
                "bh_appointment_no" => $appointment_id,
                "appointment_date" => $appt_data->visit_date,
                "start_time" => $appt_data->visit_time,
                "end_time" => $appt_data->visit_end_time,
                "is_confirmed" => $appt_data->is_visit_confirmed,
                //clinic info
                "clinic_id" => $clinic_id,
                "clinic_emr_type" => $clinic_data->emr_pathway,
                //record info
                "created_by" => "bh",
                "operation_type" => "NEW",
                "status" => ($clinic_data->emr_pathway === "OscarEMR") ? "NEW" : ""
            ));
            //log_message("error", "schedule integration data inserted => " .
                    //$CI->db->last_query());
        }
    }
}
