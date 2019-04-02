<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

function referring_physician_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && $CI->session->userdata('login_role') == "referring_physician") {
        return true;
    } else {
        return false;
    }
}

function clinic_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && ($CI->session->userdata('login_role') == "clinic_admin" || $CI->session->userdata('login_role') == "clinic_physician")) {
        return TRUE;
    } else {
        return false;
    }
}

function clinic_admin_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && ($CI->session->userdata('login_role') == "clinic_admin")) {
        return TRUE;
    } else {
        return false;
    }
}

function clinic_physician_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && ($CI->session->userdata('login_role') == "clinic_physician")) {
        return TRUE;
    } else {
        return false;
    }
}

function admin_login() {
    $CI = & get_instance();
    if ($CI->session->userdata('login_role') && $CI->session->userdata('login_role') == "admin") {
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
    log_message("error", "get decrypted sql = " . $CI->db->last_query());
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
    log_message("error", "function get_pdf_page_count");
    $fp = @fopen(preg_replace("/\[(.*?)\]/i", "", $filepath), "r");
    $max = 0;
    if (!$fp) {
        log_message("error", "not fp");
        return 0;
    } else {
        while (!@feof($fp)) {
            // log_message("error", "while loop ");
            $line = @fgets($fp, 255);
            if (preg_match('/\/Count [0-9]+/', $line, $matches)) {
                log_message("error", "if yes");
                preg_match('/[0-9]+/', $matches[0], $matches2);
                if ($max < $matches2[0]) {
                    log_message("error", "if yes 2");
                    $max = trim($matches2[0]);
                    break;
                }
            }
            // log_message("error", "current max = $max");
        }
        @fclose($fp);
    }
    return $max;
}

function add_fax_count($sender, $receiver, $clinic_id, $fax_type, $login_role = "") {
    $CI = & get_instance();
    if (!$login_role || $login_role == "") {
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
    log_message("error", "SENDGRID - sending mail to $to");
    if ($from == "") {
        $from = "alerts@blockhealth.co";
    }
    if ($to_name == "") {
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
        log_message("error", "mail status code = " . json_encode($response->statusCode()));
        log_message("error", "mail header code = " . json_encode($response->headers()));
        log_message("error", "mail body code = " . json_encode($response->body()));
    } catch (Exception $e) {
        log_message("error", "Error while mail - " . $e->getMessage());
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
        log_message("error", "error while converting to two digit from $digit");
        return "00";
    }
}

function save_json($clinic_id, $data_object) {
    $myFile = "uploads/clinics/" . md5($clinic_id) . "/metadata.json";
    $arr_data = array(); // create empty array
    try {
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
