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

        if ($Body == "1" || $Body == "2") {
            //look for relative patient number
            log_message("error", "body is 1 or 2 => $Body");
            $this->db->select("r_pv.id, r_pv.visit_confirmed");
            $this->db->from("referral_patient_info pat, records_patient_visit r_pv");
            $this->db->where(
                    array(
                        "pat.active" => 1,
                        "r_pv.active" => 1,
                        "r_pv.notify_sms" => 1,
                        "concat('+1',pat.cell_phone)" => $From
                    )
            );
            $this->db->where("r_pv.patient_id", "pat.id", false);
            $result = $this->db->get()->result();

            log_message("error", "webhook sql = " . $this->db->last_query());

            $change_status = false;

            $this->db->trans_start();
            foreach ($result as $row) {
                log_message("error", "row = " . json_encode($row));
                log_message("error", "body = " . $Body);
                if ($Body == "1") {
                    if ($row->visit_confirmed == "Awaiting Confirmation" || $row->visit_confirmed == "Change required") {
                        //change status to confirm
                        $this->db->where( array(
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
                }
                else if ($Body == "2") {
                    $msg = "Thank you. The clinic has been alerted of your change request, and will be in contact shortly. ";
                }

                log_message("error", "response = " . "<Response><Sms>" . $msg . "</Sms></Response>");
                echo "<Response><Sms>" . $msg . "</Sms></Response>";
            }
            //"Thank you for confirming your appointment! If you are unable to make your appointment, please type CHANGE to alert clinic staff as soon as possible.";
        }
    }
}
