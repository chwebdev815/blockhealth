<?php

header('content-type: text/xml');

defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook_twilio_sms extends CI_Controller {

    public function xvdnWyBnrjfdZkTzbhhxpjfSTzYbYbTN() {
        try {
            echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $data = $_REQUEST;
//        $data = $this->input->get();
            log_message("error", "webhook incoming sms = " . json_encode($data));
            file_put_contents('/var/www/html/inSMS4.txt', json_encode($data));
            file_put_contents('/var/www/html/inSMS5.txt', 'demo');
            //$Body = strtoupper(trim($data["Body"]));
//

            if (isset($data["Body"])) {
                $Body = strtoupper(trim($data["Body"]));
                $From = $data["From"];
                if ($Body === "0" || $Body === "1" || $Body === "2" || $Body === "3") {
                    //find patient
                    $patients = $this->db->select("pat.id")->from("referral_patient_info pat")
                                    ->where(array(
                                        "active" => 1,
                                        "concat('+1',pat.cell_phone)" => $From
                                    ))
                                    ->group_end()
                                    ->get()->result();

                    log_message("error", "sql for find patient = " . $this->db->last_query());
                    log_message("error", "patient ids found = " . json_encode($patients));
                    $possible_patients = array();
                    foreach ($patients as $key => $value) {
                        $possible_patients[] = $value->id;
                    }

                    if (sizeof($possible_patients) > 0) {
                        $reserved = $this->db->select("*")
                                        ->from("records_patient_visit_reserved")
                                        ->where_in("patient_id", $possible_patients)
                                        ->order_by("id", "desc")
                                        ->limit(1)->get()->result();
                        log_message("error", "sql for find reserved = " . $this->db->last_query());
                        log_message("error", "reserved ids found = " . json_encode($reserved));

                        $scheduled = $this->db->select("*")
                                        ->from("records_patient_visit")
                                        ->where_in("patient_id", $possible_patients)
                                        ->order_by("id", "desc")
                                        ->limit(1)->get()->result();
                        log_message("error", "sql for find scheduled = " . $this->db->last_query());
                        log_message("error", "scheduled ids found = " . json_encode($scheduled));


                        $visit = null;


                        if ($scheduled && $reserved) {
                            $visit = ($scheduled[0]->create_datetime > $reserved[0]->create_datetime) ? $scheduled[0] : $reserved[0];
                            log_message("error", "from both selected to 1 = " . json_encode($visit));
                            log_message("error", "compared ' " . $scheduled[0]->create_datetime . " with " . $reserved[0]->create_datetime);
                        } else if ($scheduled) {
                            $visit = $scheduled[0];
                            log_message("error", "from both selected shcduled = " . json_encode($visit));
                        } else if ($reserved) {
                            $visit = $reserved[0];
                            log_message("error", "from both selected reserved = " . json_encode($visit));
                        } else {
                            log_message("error", "nothing at all = " . json_encode($visit));
                            return;
                        }

                        $msg = "";
                        log_message("error", "visit = " . json_encode($visit));

                        if ($visit->visit_confirmed === "N/A") {
                            //will check if status is "N/A" then will response like response of choosing date

                            $reserved = $visit;
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
                                        "confirm_visit_key" => $reserved->confirm_visit_key,
                                        "notify_status" => "Contact directly",
                                        "notify_status_icon" => "yellow"
                                    ));
                                    $msg = "Thank you. Staff from the clinic will be in touch shortly";


                                    //set status in accepted_status
                                    $referral_id = $this->db->select("c_ref.id")
                                                    ->from("clinic_referrals c_ref, referral_patient_info pat")
                                                    ->where(array(
                                                        "pat.id" => $reserved->patient_id
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
                                }

                                if ($Body === "1" || $Body === "2" || $Body === "3") {
                                    log_message("error", "body $Body");
                                    $insert_data = array(
                                        "visit_name" => $reserved->visit_name,
                                        "patient_id" => $reserved->patient_id,
                                        "notify_voice" => $reserved->notify_voice,
                                        "notify_sms" => $reserved->notify_sms,
                                        "notify_email" => $reserved->notify_email,
                                        "visit_confirmed" => "Awaiting Confirmation",
                                        "confirm_visit_key" => $reserved->confirm_visit_key,
                                        "notify_status" => $reserved->notify_status,
                                        "notify_status_icon" => $reserved->notify_status_icon
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
                                    $referral_id = $this->db->select("c_ref.id")
                                                    ->from("clinic_referrals c_ref, referral_patient_info pat")
                                                    ->where(array(
                                                        "pat.id" => $reserved->patient_id
                                                    ))
                                                    ->where("c_ref.id", "pat.referral_id", false)
                                                    ->get()->result()[0]->id;

                                    $this->db->set("accepted_status_date", $reserved->created_datetime, false);
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

                                    log_message("error", "reserved is deactivated with " . $this->db->last_query());
                                }
                            } else {
                                $visit = $reserved;
                                $this->db->select('admin.id as clinic_id, '
                                        . 'CASE WHEN (pat.cell_phone = NULL OR pat.cell_phone = "") '
                                        . 'THEN "false" ELSE "true" END AS allow_sms, '
                                        . 'CASE WHEN (pat.email_id = NULL OR pat.email_id = "") '
                                        . 'THEN "false" ELSE "true" END AS allow_email, '
                                        . "admin.address,"
                                        . "pat.email_id, pat.cell_phone, pat.home_phone, pat.work_phone, "
                                        . "pat.fname, pat.lname, admin.clinic_institution_name, admin.call_address");
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

                                    //find asignable slots
                                    $new_visit_duration = 30;
                                    $this->load->model("referral_model");
                                    $response = $this->referral_model->assign_slots($new_visit_duration, $visit->patient_id);
                                    if ($response["result"] === "error") {
                                        $msg = "501 - Internal server error.";
                                    } else if ($response["result"] === "success") {
                                        $allocations = $response["data"];
                                        //make call with proper data
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
                                            "visit_expire_time" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT10M"))->format("Y-m-d H:i:s")
                                        );
                                        $this->db->where(array(
                                            "id" => $visit->id
                                        ))->update("records_patient_visit_reserved", $update_data);


                                        //send sms
                                        $start_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["start_time"]);
                                        $start_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["start_time"]);
                                        $start_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["start_time"]);

                                        $visit_datetime = array();
                                        $visit_datetime[] = array(
                                            "date" => $start_time1->format("l M jS"),
                                            "time" => $start_time1->format("g:ia")
                                        );
                                        $visit_datetime[] = array(
                                            "date" => $start_time2->format("l M jS"),
                                            "time" => $start_time2->format("g:ia")
                                        );
                                        $visit_datetime[] = array(
                                            "date" => $start_time3->format("l M jS"),
                                            "time" => $start_time3->format("g:ia")
                                        );

                                        $msg = "Hello <patient name>,\n"
                                                . "\n"
                                                . "This is an automated appointment booking message from <clinic name>. "
                                                . "Please select one of the following dates:\n"
                                                . "\n"
                                                . "<date1> at <time1> - reply with '1'\n"
                                                . "\n"
                                                . "<date2> at <time2> - reply with '2'\n"
                                                . "\n"
                                                . "<date3> at <time3> - reply with '3'\n"
                                                . "\n"
                                                . "If you would like the clinic to contact you directly, please reply with '0'.\n"
                                                . "\n"
                                                . "Please note - these dates will be reserved for the next 60 minutes.\n"
                                                . "\n"
                                                . "Thank-you.";

                                        $msg = str_replace("<patient name>", $patient_data->fname, $msg);
                                        $msg = str_replace("<date1>", $visit_datetime[0]["date"], $msg);
                                        $msg = str_replace("<time1>", $visit_datetime[0]["time"], $msg);
                                        $msg = str_replace("<date2>", $visit_datetime[1]["date"], $msg);
                                        $msg = str_replace("<time2>", $visit_datetime[1]["time"], $msg);
                                        $msg = str_replace("<date3>", $visit_datetime[2]["date"], $msg);
                                        $msg = str_replace("<time3>", $visit_datetime[2]["time"], $msg);
                                        $msg = str_replace("<clinic name>", $patient_data->clinic_institution_name, $msg);

                                        //set status in accepted_status
                                        $referral_id = $this->db->select("c_ref.id")
                                                        ->from("clinic_referrals c_ref, referral_patient_info pat")
                                                        ->where(array(
                                                            "pat.id" => $reserved->patient_id
                                                        ))
                                                        ->where("c_ref.id", "pat.referral_id", false)
                                                        ->get()->result()[0]->id;
                                        $this->db->set("accepted_status_date", $reserved->created_datetime, false);
                                        $this->db->where(array(
                                            "id" => $referral_id
                                        ))->update("clinic_referrals", array(
                                            "accepted_status" => "SMS",
                                            "accepted_status_icon" => "green"
                                        ));
                                    }
                                }
                            }
                        } else if ($visit->visit_confirmed === "N/A") {
                            //will check if status is "Awaiting Confirmation"  then will response for confirming date selected earlier
                            $reserved = $visit;
                            $scheduled_time = ($reserved->visit_date . " " . $reserved->visit_time);
                            if ($scheduled_time > date("Y-m-d H:i:s")) {
                                log_message("error", "able to confirm visit");

                                if ($Body === "1") {
                                    //1 to confirm this booking
                                    log_message("error", "body 111");

                                    $this->db->select('admin.id as clinic_id, '
                                            . 'CASE WHEN (pat.cell_phone = NULL OR pat.cell_phone = "") '
                                            . 'THEN "false" ELSE "true" END AS allow_sms, '
                                            . 'CASE WHEN (pat.email_id = NULL OR pat.email_id = "") '
                                            . 'THEN "false" ELSE "true" END AS allow_email, '
                                            . "admin.address,"
                                            . "pat.email_id, pat.cell_phone, pat.home_phone, pat.work_phone, "
                                            . "pat.fname, pat.lname, admin.clinic_institution_name, admin.call_address");
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

                                    $this->db->where(array(
                                        "active" => 1,
                                        "id" => $reserved->id
                                    ))->update("records_patient_visit", array(
                                        "visit_confirmed" => "Confirmed"
                                    ));

                                    $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $reserved->visit_date . " " . $reserved->visit_time);
                                    $date = $datetime->format("l M jS");
                                    $time = $datetime->format("g:ia");

                                    $msg = "Your appointment " . $reserved->visit_name . " with "
                                            . $patient_data->clinic_institution_name . " is coming up on $date at $time.";
                                } else if ($Body === "2") {
                                    //If this date does not work, please type 2 to alert the clinic staff
                                    log_message("error", "body 222");
                                    $this->db->where(array(
                                        "active" => 1,
                                        "id" => $visit->id
                                    ))->update("records_patient_visit", array(
                                        "visit_confirmed" => "Change required",
                                        "notify_status" => "Contact directly",
                                        "notify_status_icon" => "yellow"
                                    ));

                                    //set status in accepted_status
                                    $referral_id = $this->db->select("c_ref.id")
                                                    ->from("clinic_referrals c_ref, referral_patient_info pat")
                                                    ->where(array(
                                                        "pat.id" => $reserved->patient_id
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

                                    $msg = "Thank you. Staff from the clinic will be in touch shortly";
                                }
                            } else {
                                $msg = "Visit confirmation time expired";
                            }
                        }
                        echo "<Response><Sms>" . $msg . "</Sms></Response>";
                        exit();
                    }
                }
            }
        } catch (Exception $e) {
            log_message("error", "error to msg response = " . json_encode($e));
        }
        echo "<Response><Sms>" . "501 Internal Server Error" . "</Sms></Response>";
    }

}
