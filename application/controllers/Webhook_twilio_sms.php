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
            $this->db->limit(3);


//                "r_pvr.notify_sms" => 1,

            $result = $this->db->get()->result();
            //process latest visit only
            if ($result && sizeof($result) === 3) {
                if ($Body === "0") {
                    $reserved = $result[0];
                    $this->db->insert("records_patient_visit", array(
                       "visit_name" => $reserved->visit_name,
                       "patient_id" => $reserved->patient_id,
//                       "patient_id" => $reserved->patient_id,
//                       "patient_id" => $reserved->patient_id,
                        
                    ));
                    $msg = "Thank you. Staff from the clinic will be in touch shortly";
                }
//                $latest_visit = $result[sizeof($result) - 1];
//                if($latest_visit->visit_confirmed === "Awaiting Confirmation") {
//                    $msg = "Default Message";
//                    if($Body === "0") {
//                        $this->db->where(array(
//                            "id" => $latest_visit->id
//                        ));
//                        $this->db->set("visit_confirmed", "Change required");
//                        $this->db->update("records_patient_visit");
//                        $msg = "Thank you. Staff from the clinic will be in touch shortly";
//                    }
//                    else if($Body === "1" || $Body === "2" || $Body === "3") {
//                        
//                    }
//                    echo "<Response><Sms>" . $msg . "</Sms></Response>";
//                }
            }

            $change_status = false;

            $this->db->trans_start();
            foreach ($result as $row) {
                log_message("error", "row = " . json_encode($row));
                log_message("error", "body = " . $Body);
                if ($Body == "1") {
                    if ($row->visit_confirmed == "Awaiting Confirmation" || $row->visit_confirmed == "Change required") {
                        //change status to confirm
                        $this->db->where(array(
                            "id" => $row->id
                        ));
                        $this->db->set("visit_confirmed", "Confirmed");
                        $this->db->update("records_patient_visit");
                        $change_status = true;

                        log_message("error", "confirming status of visit " . $this->db->last_query());
                    }
                }
                if ($Body == "2") {
                    if ($row->visit_confirmed == "Awaiting Confirmation") {
                        //change status to Change required
                        $this->db->where(
                                array(
                                    "id" => $row->id
                                )
                        );
                        $this->db->set("visit_confirmed", "Change required");
                        $this->db->update("records_patient_visit");
                        $change_status = true;


                        log_message("error", "changing status of visit " . $this->db->last_query());
                    }
                }
            }
            $this->db->trans_complete();

            if ($change_status) {
                //status changed successfully
                log_message("error", "body = $Body");
                $msg = "Nothing";
                if ($Body == "1") {
                    $msg = "Thank you for confirming your appointment! If you need to cancel, please type 2 to alert clinic staff at least 48 hours before your appointment.";
                } else if ($Body == "2") {
                    $msg = "Thank you. The clinic has been alerted of your change request, and will be in contact shortly. ";
                }

                log_message("error", "response = " . "<Response><Sms>" . $msg . "</Sms></Response>");
                echo "<Response><Sms>" . $msg . "</Sms></Response>";
            }
            //"Thank you for confirming your appointment! If you are unable to make your appointment, please type CHANGE to alert clinic staff as soon as possible.";
        }
    }

}
