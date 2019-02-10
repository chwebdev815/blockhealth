<?php

header('content-type: text/xml');

defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook_twilio_sms extends CI_Controller {

    public function xvdnWyBnrjfdZkTzbhhxpjfSTzYbYbTN() {
        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $data = $this->input->get();
        log_message("error", "webhook incoming sms = " . json_encode($data));

        $Body = strtoupper(trim($data["Body"]));
//
        $From = $data["From"];
        //$Body = "1";
        //$From = "6479066970";

        if ($Body === "0" || $Body === "1" || $Body === "2" || $Body === "3") {
            //look for relative patient number
            log_message("error", "body is 1 or 2 or 3 => $Body");
            $this->db->select("r_pvr.*");
            $this->db->from("referral_patient_info pat, records_patient_visit_reserved r_pvr");
            $this->db->where(array(
                "pat.active" => 1,
                "r_pvr.active" => 1,
                "concat('+1',pat.cell_phone)" => $From
            ));
            $this->db->where("r_pvr.patient_id", "pat.id", false);
            $this->db->order_by("r_pvr.id", "desc");
            $this->db->limit(1);
            log_message("error", "sql = " . $this->db->last_query());
            $result = $this->db->get()->result();
//            echo "sql = " . $this->db->last_query();
            //process latest visit only
            if ($result) {
                log_message("error", "if result");
                //if visit not expired 
                $reserved = $result[0];
                $msg = "";
                if ($reserved->visit_expire_time > date("Y-m-d H:i:s")) {
                    log_message("error", "alive visit_expire_time");

                    if ($Body === "0") {
                        log_message("error", "body 0");
                        $this->db->insert("records_patient_visit", array(
                            "visit_name" => $reserved->visit_name,
                            "patient_id" => $reserved->patient_id,
                            "notify_voice" => $reserved->notify_voice,
                            "notify_sms" => $reserved->notify_sms,
                            "notify_email" => $reserved->notify_email,
                            "visit_confirmed" => "Change required",
                            "confirm_visit_key" => $reserved->confirm_visit_key
                        ));
                        $msg = "Thank you. Staff from the clinic will be in touch shortly";


                        //set status in accepted_status
                        $referral_id = $this->db->select("c_ref.id")->from("clinic_referrals c_ref, referral_patient_info pat")->where(array(
                                    "pat.id" => $reserved->patient_id
                                ))->get()->result()[0]->id;

                        $this->db->where(array(
                            "id" => $referral_id
                        ))->update("clinic_referrals", array(
                            "accepted_status" => "Contact directly",
                            "accepted_status_icon" => "yellow"
                        ));
                    }

                    if ($Body === "1" || $Body === "2" || $Body === "3") {
                        log_message("error", "body $Body");
                        $insert_data = array(
                            "visit_name" => $reserved->visit_name,
                            "patient_id" => $reserved->patient_id,
                            "notify_voice" => $reserved->notify_voice,
                            "notify_sms" => $reserved->notify_sms,
                            "notify_email" => $reserved->notify_email,
                            "visit_confirmed" => "Confirmed",
                            "confirm_visit_key" => $reserved->confirm_visit_key
                        );

                        if ($Body === "1") {
                            $insert_data["visit_date"] = $reserved->visit_date1;
                            $insert_data["visit_time"] = $reserved->visit_start_time1;
                            $insert_data["visit_end_time"] = $reserved->visit_end_time1;
                        }
                        if ($Body === "2") {
                            $insert_data["visit_date"] = $reserved->visit_date2;
                            $insert_data["visit_time"] = $reserved->visit_start_time2;
                            $insert_data["visit_end_time"] = $reserved->visit_end_time2;
                        }
                        if ($Body === "3") {
                            $insert_data["visit_date"] = $reserved->visit_date3;
                            $insert_data["visit_time"] = $reserved->visit_start_time3;
                            $insert_data["visit_end_time"] = $reserved->visit_end_time3;
                        }
                        $this->db->insert("records_patient_visit", $insert_data);

                        $this->db->select("c_usr.address, c_usr.id")
                                ->from("clinic_user_info c_usr, referral_patient_info pat, "
                                        . "clinic_referrals c_ref, efax_info efax")
                                ->where("pat.id", $reserved->patient_id);
                        $this->db->where("pat.referral_id", "c_ref.id", false);
                        $this->db->where("c_ref.efax_id", "efax.id", false);
                        $this->db->where("efax.to", "c_usr.id", false);
                        $clinic = $this->db->get()->result();

                        $address = "";
                        if ($clinic) {
                            $address = $clinic[0]->address;
                        } else {
                            $address = "Clinic Address";
                        }

                        log_message("error", "insert = " . $this->db->last_query());
                        $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $insert_data["visit_date"] . " " . $insert_data["visit_time"]);
                        $date = $datetime->format("l M jS");
                        $time = $datetime->format("g:ia");
                        $msg = "Thank you. Your appointment has been scheduled for $date at $time.\n"
                                . "\n"
                                . "The address is:\n"
                                . "$address\n"
                                . "\n"
                                . "Please be sure to arrive on time.";

//                        //make reserved entry inactive
//                        $this->db->set("active", "0");
//                        $this->db->where("id", $reserved->id);
//                        $this->db->update("records_patient_visit_reserved");


                        //set status in accepted_status
                        $referral_id = $this->db->select("c_ref.id")->from("clinic_referrals c_ref, referral_patient_info pat")->where(array(
                                    "pat.id" => $reserved->patient_id
                                ))->get()->result()[0]->id;

                        $this->db->where(array(
                            "id" => $referral_id
                        ))->update("clinic_referrals", array(
                            "accepted_status" => "Confirmed",
                            "accepted_status_icon" => "green"
                        ));


                        $this->load->model("referral_model");
                        $this->referral_model->move_from_accepted_to_scheduled($reserved->patient_id, $clinic[0]->id);
                    }
                    if ($Body === "1" || $Body === "2" || $Body === "3" || $Body === "0") {
                        $this->db->where(array(
                            "id" => $reserved->id
                        ))->update("records_patient_visit_reserved", array(
                            "active" => 0,
                            "visit_confirmed" => "Booked"
                        ));
                    }
                } else {
                    $msg = "Visit response time is expired";
                }
                echo "<Response><Sms>" . $msg . "</Sms></Response>";
            } else {
                exit();
            }
        }
    }

}
