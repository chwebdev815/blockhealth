<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Fax_manager extends CI_Controller {

    public function index() {
        if (isset($argv)) {
            if (!empty($argv[1])) {
                switch ($argv[1]) {
                    case "hwaBoWSDmTNblPFakqzEhzASerOeKGAc":
                        $this->hwaBoWSDmTNblPFakqzEhzASerOeKGAc();
//                        log_message("error", "Called function hwaBoWSDmTNblPFakqzEhzASerOeKGAc");
                        break;
                }
            }
        }
    }

    public function hwaBoWSDmTNblPFakqzEhzASerOeKGAc() {
//        log_message("error", "===========================> version => 1.8 at same time");
        //addedd 
        $system_time = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        $before_5_mins = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-5 minute")));
        $before_10_mins = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("-10 minute")));
        // echo "system time = " . json_encode(date('Y-m-d H:i:s')) . "<br/>";
        // echo "-6 hours = " . json_encode($cur_time) . "<br/>";
//        $sStartDate = "20180606"; //$cur_time->sub(new DateInterval('PT5M'))->format("Ymd");
        $sStartDate = $before_10_mins->format("Ymd");
        $sEndDate = $before_5_mins->format('Ymd');
//        $before_5_mins = $system_time->sub(new DateInterval('PT5M'));
//        $before_10_mins = $system_time->sub(new DateInterval('PT5M'));
//        log_message("error", "start = " . $sStartDate . ", and end = " . $sEndDate);
//        log_message("error", "systime => " . json_encode($system_time));
//        log_message("error", "5 min before => " . json_encode($before_5_mins));
//        log_message("error", "10 min before => " . json_encode($before_10_mins));
        // echo "start = " . $sStartDate . ", and end = " . $sEndDate . "<br/>";


        $this->db->select("id, srfax_number, srfax_email, srfax_pass, srfax_account_num");
        $this->db->from("clinic_user_info");
        $this->db->where(array(
            "active" => 1
        ));
        $clinics = $this->db->get()->result();

        foreach ($clinics as $key => $clinic) {
            $clinic_id = $clinic->id;
            $access_id = $clinic->srfax_account_num;
            $access_pwd = $clinic->srfax_pass;
            $caller_id = $clinic->srfax_number;
            $sender_mail = $clinic->srfax_email;

            $postVariables = array(
                "action" => "Get_Fax_Inbox",
                "access_id" => $access_id,
                "access_pwd" => $access_pwd,
                "sPeriod" => "RANGE",
                "sStartDate" => $sStartDate,
                "sEndDate" => $sEndDate,
                // "sStartDate" => "20180201",
                // "sEndDate" => "20180331",
                "sIncludeSubUsers" => "Y",
//                "sViewedStatus" => "UNREAD"
            );
            $curlDefaults = array(
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_URL => "https://www.srfax.com/SRF_SecWebSvc.php",
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_POSTFIELDS => http_build_query($postVariables),
            );
            $ch = curl_init();
            curl_setopt_array($ch, $curlDefaults);
            $result = json_decode(curl_exec($ch));
            $faxes = null;
            if($result && isset($result->Result) && $result->Result) {
                $faxes = $result->Result;
            }
            
            if ($faxes != null) {
//                log_message("error", "faxes = " . json_encode($faxes));
                foreach ($faxes as $fax) {
//                    log_message("error", "fax = " . json_encode($fax));
                    $fax_date = DateTime::createFromFormat('M d/y h:i a', $fax->Date);
//                    log_message("error", "fax time = " . json_encode($fax_date));
//                    log_message("error", "<5 condition = " . ($fax_date < $before_5_mins));
//                    log_message("error", ">=10 condition = " . ($fax_date >= $before_10_mins));
//                    log_message("error", "fax time = " . json_encode($fax_date));

                    if ($fax_date < $before_5_mins && $fax_date >= $before_10_mins) {
//                        log_message("error", "The fax was in time");
                        $fax_details_id = substr($fax->FileName, strpos($fax->FileName, "|") + 1);
                        $this->db->select("id as family_physician_id");
                        $this->db->from("physician_info");
                        $this->db->where(array(
                            "fax_number" => $fax->CallerID
                        ));
                        $result = $this->db->get()->result();
                        $from = ($result) ? $result[0]->family_physician_id : "";
                        $to = $clinic_id; // clinic id
                        $pages = $fax->Pages;
                        $sender_fax = $fax->CallerID;
                        if (strlen($sender_fax) == 10) {
                            $sender_fax = "1$sender_fax";
                        }
//                        log_message("error", "fax received. detail id = " . $fax_details_id . "<br/>");
//                        echo "fax details = $fax_details_id, $from, $to, $pages, $sender_fax" . "<br/>";
                        $this->retrieve_fax($fax_details_id, $from, $to, $pages, $sender_fax, $access_id, $access_pwd);
                    } else {
//                        log_message("error", "The fax was out of time");
                    }
                }
            }
        }

//        log_message("error", "=======================================================================");
//        log_message("error", "=======================================================================");
//        log_message("error", "=======================================================================");
    }

    private function retrieve_fax($fax_details_id, $from, $to, $pages, $sender_fax, $access_id, $access_pwd) {
        $this->db->trans_start();
        $pdf_fax_file_name = "";
        $tiff_fax_file_name = "";
        //download fax as PDF
        $postVariables = array(
            "action" => "Retrieve_Fax",
            "access_id" => $access_id,
            "access_pwd" => $access_pwd,
            "sFaxDetailsID" => $fax_details_id,
            "sDirection" => "IN"
        );
        $curlDefaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://www.srfax.com/SRF_SecWebSvc.php",
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_POSTFIELDS => http_build_query($postVariables),
        );
        $ch = curl_init();
        curl_setopt_array($ch, $curlDefaults);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "Error – " . curl_error($ch) . "<br/>";
            curl_close($ch);
            return;
        } else {
            $decodedResult = json_decode($result, 1);
            // save the result to a file
            $pdf_fax_file_name = $this->generate_random_string(32);
            //get patient id here.
            $file_path = getcwd() . "/uploads/efax/" . $pdf_fax_file_name . ".pdf";
            
//            log_message("error", "pdf saved at = " . $file_path);
            file_put_contents($file_path, base64_decode($decodedResult["Result"]));
            curl_close($ch);
//            echo "PDF file saved" . "<br/>";
        }
//        download fax as tiff
        $postVariables = array(
            "action" => "Retrieve_Fax",
            "access_id" => $access_id,
            "access_pwd" => $access_pwd,
            "sFaxDetailsID" => $fax_details_id,
            "sDirection" => "IN",
            "sFaxFormat" => "TIFF"
        );
        $curlDefaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://www.srfax.com/SRF_SecWebSvc.php",
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_POSTFIELDS => http_build_query($postVariables),
        );
        $ch = curl_init();
        curl_setopt_array($ch, $curlDefaults);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo "Error – " . curl_error($ch) . "<br/>";
            curl_close($ch);
        } else {
            $decodedResult = json_decode($result, 1);
            // save the result to a file
            $tiff_fax_file_name = $this->generate_random_string(32) . ".tif";
            $file_path = getcwd() . "/uploads/efax_tiff/" . $tiff_fax_file_name;
//            echo "file path = " . $file_path . "<br/>";
            file_put_contents($file_path, base64_decode($decodedResult["Result"]));
            curl_close($ch);
//            log_message("error", "tiff also saved");
//            echo "TIFF also saved";
        }
        if($from == "") {
            $from = "0";
        }
        //save record in efax table
        $this->db->insert("efax_info", array(
            "from" => $from,
            "to" => $to,
            "file_name" => $pdf_fax_file_name,
            "tiff_file_name" => $tiff_fax_file_name,
            "pages" => $pages,
            "sender_fax_number" => $sender_fax
//            "created_datetime" => date("Y-m-d H:i:s")
        ));
//        log_message("error", " ==================================== > saved fax files => $pdf_fax_file_name $tiff_fax_file_name");
        //make viewed status read
//        $postVariables = array(
//            "action" => "Update_Viewed_Status",
//            "access_id" => $access_id,
//            "access_pwd" => $access_pwd,
//            "sFaxDetailsID" => $fax_details_id,
//            "sDirection" => "IN",
//            "sMarkasViewed" => "Y"
//        );
//        $curlDefaults = array(
//            CURLOPT_POST => 1,
//            CURLOPT_HEADER => 0,
//            CURLOPT_URL => "https://www.srfax.com/SRF_SecWebSvc.php",
//            CURLOPT_FRESH_CONNECT => 1,
//            CURLOPT_RETURNTRANSFER => 1,
//            CURLOPT_FORBID_REUSE => 1,
//            CURLOPT_TIMEOUT => 60,
//            CURLOPT_SSL_VERIFYPEER => false,
//            CURLOPT_SSL_VERIFYHOST => 2,
//            CURLOPT_POSTFIELDS => http_build_query($postVariables),
//        );
//        $ch = curl_init();
//        curl_setopt_array($ch, $curlDefaults);
//        $result = curl_exec($ch);
//        if (curl_errno($ch)) {
//            log_message("error", "Error – " . json_encode(curl_error($ch)));
//            return;
//        }
//        log_message("error", "view status changed");
//        echo "View status changed " . "<br/>";
        $this->db->trans_complete();
    }

    private function generate_random_string($length = 32) {
        $timestamp = time();
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $timestamp . "_" . $randomString;
    }

    //change updated
}
