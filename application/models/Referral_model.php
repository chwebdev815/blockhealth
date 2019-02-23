<?php

class Referral_model extends CI_Model {

    public function fetch_dashboard_counts_model() {
        $match = "";
        $col = "";
        $clinic_id = $this->session->userdata("user_id");

        if ($this->session->userdata("login_role") == "clinic_admin") {
            $match = $this->session->userdata("user_id");
            $col = "clinic_admin";
            $where_for_task_count = "assigned_to = 0 and clinic_id = $clinic_id";
        } else if ($this->session->userdata("login_role") == "clinic_physician") {
            $match = $this->session->userdata("physician_id");
            $col = "physician_id";
            $where_for_task_count = "assigned_to = $match and clinic_id = $clinic_id";
        }
        $sql = "select ";
        $sql .= "(select count(*) from inbox_dash where clinic_id ='" . $clinic_id . "') as count_inbox,";
        $sql .= "(select count(*) from admin_triage_dash where clinic_admin ='" . $clinic_id . "') as count_admin,";
        $sql .= "(select count(*) from physician_triage_dash where clinic_admin ='" . $clinic_id . "') as count_physician,";
        $sql .= "(select count(*) from accepted_dash where " . $col . " ='" . $match . "' and  clinic_admin ='" . $clinic_id . "') as count_accepted,";
        $sql .= "(select count(r_pv.id) from 
            records_patient_visit r_pv, referral_patient_info pat, clinic_referrals c_ref, efax_info efax  
            where r_pv.patient_id = pat.id AND pat.referral_id = c_ref.id AND c_ref.status = 'Scheduled' AND
            concat(r_pv.visit_date, ' ', r_pv.visit_time) > now() and r_pv.active = 1 and c_ref.efax_id = efax.id and 
            pat.active = 1 and c_ref.active = 1 and efax.active = 1 and efax.to ='" . $clinic_id . "') as count_scheduled,";
        $sql .= "(select count(*) from view_my_tasks where $where_for_task_count) as count_my_tasks,";
        $sql .= "(select count(*) from view_completed_tasks where $where_for_task_count) as count_completed_tasks";


        //count_all_records
        $result = $this->db->query($sql)->result();
        // log_message("error", $this->db->last_query());
        return $result;
    }

    public function check_valid_referral_state_model($state, $state2 = "blank") {
        $md5_id = $this->uri->segment(3);
        $this->db->select("pat.id");
        $this->db->from("clinic_referrals c_ref, efax_info efax, referral_patient_info pat");
        $this->db->where(
                array(
                    "efax.active" => 1,
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "md5(pat.id)" => $md5_id,
                    "efax.to" => $this->session->userdata("user_id")
                )
        );
        $this->db->where("c_ref.efax_id", "efax.id", false);
        $this->db->where("pat.referral_id", "c_ref.id", false);
        if ($state2 == "blank") {
            $this->db->where(array(
                "c_ref.status" => $state
            ));
        } else {
            $this->db->where_in("c_ref.status", array($state, $state2));
        }
        $result = $this->db->get()->result();
        return ($result) ? true : false;
    }

    public function search_patient_model() {
        // $term = $this->input->get("term");
        // $this->db->select("concat(pat_fname, ' ', pat_lname, ' (', date_format(pat_dob,'%b %D, %Y'), ')' ) as label," .
        //         "md5(id) as id, " .
        //         "REPLACE(LOWER(status),' ','_') as value");
        // $this->db->from("clinic_referrals");
        // $this->db->where(
        //         array(
        //             "active" => 1
        //         )
        // );
        // $this->db->like("pat_fname", $term);
        // $this->db->or_like("pat_lname", $term);
        // $result = $this->db->get()->result();
        // return $result;
        return array();
    }

    public function update_patient_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('pat_fname', 'First Name', 'required');
        $this->form_validation->set_rules('pat_lname', 'Last Name', 'required');
        $this->form_validation->set_rules('dobday', 'Day - Date of Birth', 'required');
        $this->form_validation->set_rules('dobmonth', 'Month - Date of Birth', 'required');
        $this->form_validation->set_rules('dobyear', 'Year - Date of Birth', 'required');
        // $this->form_validation->set_rules('pat_ohip', 'Patient OHIP', 'callback_valid_ohip');
        $this->form_validation->set_rules('pat_email_id', 'Patient Email', 'valid_email');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                try {
                    $this->db->trans_start();
                    $this->db->where(array(
                        "active" => 1,
                        "md5(id)" => $data["id"]
                    ));
                    $new_data = array(
                        "fname" => $data["pat_fname"],
                        "lname" => $data["pat_lname"],
                        "dob" => $data["dobyear"] . "-" . $data["dobmonth"] . "-" . $data["dobday"],
                        "ohip" => $data["pat_ohip"],
                        "cell_phone" => $data["pat_cell_phone"],
                        "home_phone" => $data["pat_home_phone"],
                        "work_phone" => $data["pat_work_phone"],
                        "email_id" => $data["pat_email_id"],
                        "address" => $data["pat_address"],
                    );
                    $this->db->update("referral_patient_info", $new_data);
                    log_message("error", "updated rows = " . $this->db->affected_rows());
                    // log_message("error", "updated = " . $updated);
                    $this->db->select("referral_id");
                    $this->db->from("referral_patient_info");
                    $this->db->where(
                            array(
                                "active" => 1,
                                "md5(id)" => $data["id"]
                            )
                    );
                    $result = $this->db->get()->result();
                    $referral_id = $result[0]->referral_id;
                    $this->db->set("last_updated", "now()", false);
                    $this->db->where(array(
                        "active" => 1,
                        "id" => $referral_id
                    ));
                    $this->db->update("clinic_referrals", array());
                    $this->db->trans_complete();
                    return true;
                } catch (Exception $e) {
                    return "Failed to update patient information";
                }
            } else
                return "You are not authorized for such Operation";
        } else {
            return validation_errors();
        }
    }

    public function update_physician_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('dr_fname', 'First Name', 'required');
        $this->form_validation->set_rules('dr_lname', 'Last Name', 'required');
        // $this->form_validation->set_rules('dr_fax', 'Fax Number', 'required');
        $this->form_validation->set_rules('dr_email_id', 'Physician Email', 'valid_email');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                try {
                    $new_data = array(
                        "fname" => $data["dr_fname"],
                        "lname" => $data["dr_lname"],
                        "phone" => $data["dr_phone_number"],
                        "fax" => $data["dr_fax"],
                        "email" => $data["dr_email_id"],
                        "address" => $data["dr_address"],
                        "billing_num" => $data["dr_billing_num"]
                    );
                    $this->db->where(
                            array(
                                "active" => 1,
                                "md5(id)" => $data["id"]
                            )
                    );
                    $this->db->update("referral_physician_info", $new_data);
                    log_message("error", "updated rows = " . $this->db->affected_rows());
                    $updated = ($this->db->affected_rows() == 1) ? true : "Physician information remains same.";
                    log_message("error", "updated = " . $updated);
                    $this->db->select("referral_id");
                    $this->db->from("referral_patient_info");
                    $this->db->where(
                            array(
                                "active" => 1,
                                "md5(id)" => $data["id"]
                            )
                    );
                    $result = $this->db->get()->result();
                    $referral_id = $result[0]->referral_id;
                    $this->db->where(array(
                        "active" => 1,
                        "id" => $referral_id
                    ));
                    $this->db->set("last_updated", "now()", false);
                    $this->db->update("clinic_referrals", array());
                    return true;
                } catch (Exception $e) {
                    return "Failed to update physician information";
                }
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function cancel_referral_model() {
        $this->form_validation->set_rules('id', 'Referral Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                // get referral id based on patient id
                $this->db->select("referral_id");
                $this->db->from("referral_patient_info");
                $this->db->where(array(
                    "active" => 1,
                    "md5(id)" => $data["id"]
                        )
                );
                $result = $this->db->get()->result();
                $referral_id = $result[0]->referral_id;
                $this->db->where(
                        array(
                            "active" => 1,
                            "id" => $referral_id
                        )
                );
                $this->db->set("cancelled_datetime", "now()", false);
                $this->db->update("clinic_referrals", array("status" => "Cancelled"));
                return ($this->db->affected_rows() == 1) ? true : "Referral already Cancelled";
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function decline_referral_model() {
        $this->form_validation->set_rules('id', 'Referral Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                // get referral id based on patient id
                $referral_id = $this->get_referral_id($data["id"]);
                $this->db->where(
                        array(
                            "active" => 1,
                            "id" => $referral_id
                        )
                );
                $this->db->set("cancelled_datetime", "now()", false);
                $this->db->update("clinic_referrals", array("status" => "Declined"));
                $reply = ($this->db->affected_rows() == 1) ? true : "Referral already Declined";


                $this->db->select("c_usr.clinic_institution_name, date_format(c_ref.create_datetime, '%M %D') as referral_received, dr.fax, c_ref.referral_code");
                $this->db->from("clinic_user_info c_usr, efax_info efax, clinic_referrals c_ref, referral_patient_info pat, referral_physician_info dr");
                $this->db->where(array(
                    "c_ref.id" => $referral_id,
                    "efax.active" => 1,
                    "c_usr.active" => 1,
                    "c_ref.active" => 1,
                    "dr.active" => 1,
                    "pat.active" => 1
                ));
                $this->db->where("pat.id", "dr.patient_id", false);
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $this->db->where("efax.to", "c_usr.id", false);
                $this->db->where("efax.id", "c_ref.efax_id", false);
                $result = $this->db->get()->result()[0];

                $patient_id = $this->get_patient_id($data["id"]);
                $this->db->select("if( ref_c.checklist_type = 'stored', c_items.name , ref_c.checklist_name) as 'doc_name'");
                $this->db->from("referral_checklist ref_c");
                $this->db->join("clinic_referral_checklist_items c_items", "c_items.id = ref_c.checklist_id and c_items.active=1", "left");
                $this->db->where(array(
                    "ref_c.active" => 1,
                    "ref_c.attached" => "false",
                    "ref_c.patient_id" => $patient_id
                ));
                $checklist = $this->db->get()->result();
                log_message("error", "denied check = " . $this->db->last_query());
                log_message("error", "denied result = " . json_encode($checklist));

                $file_name = "referral_denied.html";
                $replace_stack = array(
                    "###clinic_name###" => $result->clinic_institution_name,
                    "###referral_code###" => $result->referral_code,
                    "###time1###" => $result->referral_received,
                    "###time2###" => date("F jS")
                );
                $fax_number = $result->fax;
                log_message("error", "sending fax");
                $this->load->model("referral_model");
                $response = $this->referral_model->send_status_fax($file_name, $checklist, $replace_stack, $fax_number, "New Referral");


                return $reply;
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function confirm_referral_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->join("referral_patient_info pat", "r_pv.patient_id = pat.id and pat.active = 1 and md5(pat.id) = '" . $data["id"] . "'", "left");
                $this->db->where(array(
                    "r_pv.active" => 1
                ));
                $this->db->update("records_patient_visit r_pv", array(
                    "r_pv.visit_confirmed" => "Confirmed"
                ));
                return true;
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function accept_admin_referral_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {

                $referral_id = $this->get_referral_id($data['id']);
                $this->db->where(array(
                    "active" => 1,
                    "id" => $referral_id
                ));
                $this->db->update("clinic_referrals", array("status" => "Physician Triage"));
                log_message("error", "accept q = " . $this->db->last_query());
                return ($this->db->affected_rows() == 1) ? true : "Referral already Accepted";
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function missing_items_details_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $patient_id = $this->get_decrypted_id($data['id'], "referral_patient_info");
                //get previous request details
                $this->db->select("DATE_FORMAT(max(miss.create_datetime), '%l %p')  AS last_request_time," .
                        "DATE_FORMAT(max(miss.create_datetime), '%M %D')  AS last_request_date," .
                        "dr.fax as dr_fax," .
                        "concat('Dr. ', dr.fname, ' ', dr.lname) as dr_name, miss.id");
                $this->db->from("`referral_missing_item_request_info` miss, referral_physician_info dr");
                $this->db->where(array(
                    "miss.`active`" => 1,
                    "dr.`active`" => 1,
                    "md5(miss.patient_id)" => $data['id'],
                    "md5(dr.patient_id)" => $data['id']
                ));
                $this->db->where("miss.requested_to", "dr.id", false);
                $this->db->group_by("miss.patient_id");
                $this->db->order_by("miss.create_datetime", "desc");
                $this->db->limit("1");

                $result = $this->db->get()->result();

                log_message("error", "last 1 = > " . $this->db->last_query());
                $alert_data = null; // data to be returned
                if ($result) {
                    // request has been previously sent for that referral
                    $alert_data = "Missing item request was previously sent to " .
                            $result[0]->dr_name . " at " . $result[0]->last_request_time . " on " . $result[0]->last_request_date . ". " .
                            "Are you sure you would like to send a missing item request again?";
                } else {
                    //first time sending request
                    $this->db->select("concat(dr.fname, ' ', dr.lname) as dr_name");
                    $this->db->from("referral_physician_info dr");
                    $this->db->where(array(
                        "md5(dr.patient_id)" => $data["id"],
                        "dr.active" => 1
                    ));
                    $result = $this->db->get()->result();

                    log_message("error", "dr_name = " . $this->db->last_query());

                    if (!$result)
                        $dr_name = "ABC";
                    else
                        $dr_name = $result[0]->dr_name;

                    $alert_data = "Are you sure you would like to send a missing item request to " . $dr_name;
                }

                //return result data 
                return array(
                    "result" => "success",
                    "data" => $alert_data
                );
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function request_missing_items_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                //send fax to request missing items
                //Send fax in following format, with clinic name, patient name, missing item list, and referral code dynamically added
                $this->db->trans_start();
                $this->db->select("if( ref_c.checklist_type = 'stored', c_items.name , ref_c.checklist_name) as 'doc_name'");
                $this->db->from("referral_checklist ref_c");
                $this->db->join("clinic_referral_checklist_items c_items", "c_items.id = ref_c.checklist_id and c_items.active=1", "left");
                $this->db->where(array(
                    "ref_c.active" => 1,
                    "ref_c.attached" => "false",
                    "md5(ref_c.patient_id)" => $data['id']
                ));
                $this->db->or_group_start()
                        ->where("c_items.clinic_id", $this->session->userdata("user_id"))
                        ->where("ref_c.checklist_type", "typed")
                        ->group_end();
                $checklist = $this->db->get()->result();

                $this->db->select("concat(pat.fname, ' ', pat.lname) as patient_name," .
                        "c_usr.clinic_institution_name," .
                        "c_ref.referral_code," .
                        "dr.fax, dr.id as dr_id,"
                        . "efax.from as efax_from,"
                        . "date_format(efax.create_datetime, '%M %D') as referral_received,"
                        . "date_format(c_ref.create_datetime, '%M %D') as referral_triaged,"
                        . "c_ref.status");
                $this->db->from("referral_patient_info pat, clinic_user_info c_usr, clinic_referrals c_ref, efax_info efax, referral_physician_info dr");
                $this->db->where(array(
                    "pat.active" => 1,
                    "c_usr.active" => 1,
                    "c_ref.active" => 1,
                    "efax.active" => 1,
                    "dr.active" => 1,
                    "md5(pat.id)" => $data['id'],
                    "md5(dr.patient_id)" => $data['id'],
                    "c_usr.id" => $this->session->userdata("user_id")
                ));
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $this->db->where("c_ref.efax_id", "efax.id", false);
                $info = $this->db->get()->result();

                $file_name = "referral_missing.html";
                $replace_stack = array(
                    "###clinic_name###" => $info[0]->clinic_institution_name,
                    "###referral_code###" => $info[0]->referral_code,
                    "###time1###" => $info[0]->referral_triaged,
                    "###time2###" => ""
                );

                $text2 = "<h2>Referral has been triaged and accepted</h2>";
                if ($info[0]->status === "Admin Triage" || $info[0]->status === "Physician Triage") {
                    $text2 = "<h2>Referral is being triaged</h2>";
                }
                $additional_replace = array(
                    "###text2###" => $text2
                );

                $fax_number = $info[0]->fax;

                $response = $this->send_status_fax($file_name, $checklist, $replace_stack, $fax_number, "Request Missing Items", $additional_replace);
                log_message("error", "file sent successfully");

                //store missing item request
                $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
                $result = $this->db->insert("referral_missing_item_request_info", array(
                    "patient_id" => $patient_id,
                    "requested_to" => $info[0]->dr_id
                ));

                //update missing status
                $referral_id = $this->get_referral_id($data["id"]);
                $this->db->where(array(
                    "id" => $referral_id
                ));
                $this->db->update("clinic_referrals", array(
                    "missing_item_status" => "Missing item requested"
                ));

                $this->db->trans_complete();
                if ($result) {
                    return true;
                    // return array(
                    //  "sender fax" => $info[0]->fax,
                    //  "referral_code" => $info[0]->referral_code
                    // );
                } else
                    return "Operation not completed";
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function send_status_fax($file_name, $checklist, $replace_stack, $fax_number, $reason, $additional_replace = array(), $timeout = 60, $clinic_id = "") {
//        send_status_fax($file_name, array(), $replace_stack, $fax_number, "Scheduled Referral", $clinic_id)
        log_message("error", "$file_name, $fax_number");

        $item_template = '<h3 style="margin-bottom: 0em; margin-top: 0em;  font-size: 16px;"> ###item_name###<br>';
        $tmp = "";
        foreach ($checklist as $key => $value) {
            $tmp .= str_replace("###item_name###", ($key + 1) . ". " . $value->doc_name, $item_template);
        }
        $replace_stack["###missing_items###"] = $tmp;
        log_message("error", "replace stack = " . json_encode($replace_stack));

        $content = "";
        $this->load->helper('file');
        $content = read_file("assets/templates/$file_name");
        foreach ($replace_stack as $key => $value) {
            log_message("error", "converting $key with $value");
            $content = str_replace($key, $value, $content);
        }

        foreach ($additional_replace as $key => $value) {
            $content = str_replace($key, $value, $content);
        }

        $tmp_file_name = $this->generate_random_string(10);
        $dest_file = "assets/fax_assets/" . $tmp_file_name . ".pdf";

        $fp = fopen($dest_file, 'w');
        $postData = array(
            "user-id" => "blockhealth",
            "api-key" => "6FQP7ct7wapUVDyvHph9W6wNGkPbY8SnVZnIczjX5I64erpM",
            "content" => $content,
            "format" => "PDF"
        );
        $url = "https://neutrinoapi.com/html5-render";

        set_time_limit(300);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($status != 200) {
            // handle API error...
            log_message("error", "API Error" . $status);
            return false;
        }


        $fax_content = "Blockhealth Notification Fax";
        $fax_success = $this->send_fax($fax_number, $fax_content, $dest_file, $reason, $clinic_id);
        log_message("error", "fax code completed" . $fax_success);
        unlink($dest_file);
        return true;
    }

    public function accept_physician_referral_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                //if physician assigned
                $this->db->select("c_ref.assigned_physician");
                $this->db->from("clinic_referrals c_ref, referral_patient_info pat");
                $this->db->where(array(
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "md5(pat.id)" => $data["id"]
                ));
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $tmp_result = $this->db->get()->result();
                $assigned_physician = $tmp_result[0]->assigned_physician;
                if ($assigned_physician == "0") {
                    return "Patient must be assigned before accepting.";
                }

                //can proceed moving to accepted state
                $referral_id = $this->get_referral_id($data['id']);
                $this->db->where(array(
                    "active" => 1,
                    "id" => $referral_id
                ));
                $this->db->update("clinic_referrals", array(
                    "status" => "Accepted",
                    "accepted_datetime" => date("Y-m-d H:i:s")
                ));

                $reply = ($this->db->affected_rows() == 1) ? true : "Referral already Accepted";

                $this->db->select("c_usr.clinic_institution_name, date_format(c_ref.create_datetime, '%M %D') as referral_received, dr.fax, c_ref.referral_code");
                $this->db->from("clinic_user_info c_usr, efax_info efax, clinic_referrals c_ref, referral_patient_info pat, referral_physician_info dr");
                $this->db->where(array(
                    "md5(pat.id)" => $data['id'],
                    "efax.active" => 1,
                    "c_usr.active" => 1,
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "dr.active" => 1,
                    "md5(dr.patient_id)" => $data["id"]
                ));

                $this->db->where("efax.to", "c_usr.id", false);
                $this->db->where("efax.id", "c_ref.efax_id", false);
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $result = $this->db->get()->result()[0];

                $file_name = "referral_accepted.html";
                $replace_stack = array(
                    "###clinic_name###" => $result->clinic_institution_name,
                    "###referral_code###" => $result->referral_code,
                    "###time1###" => $result->referral_received,
                    "###time2###" => date("F jS")
                );
                $fax_number = $result->fax;
                log_message("error", "sending fax");
                $this->load->model("referral_model");
                $response = $this->referral_model->send_status_fax($file_name, array(), $replace_stack, $fax_number, "Accept Referral");

                $this->db->insert("count_accepted_referrals", array(
                    "referral_id" => $referral_id,
                    "login_user_id" => $this->session->userdata("user_id"),
                    "login_role" => $this->session->userdata("login_role")
                ));

                return $reply;
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function complete_referral_model() {
        $this->form_validation->set_rules('id', 'Referral Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->where(
                        array(
                            "active" => 1,
                            "md5(id)" => $data["id"]
                        )
                );
                $this->db->set("completed_datetime", "now()", false);
                $this->db->update("clinic_referrals", array(
                    "status" => "Completed",
                    "completed_datetime" => date("Y-m-d H:i:s")
                ));
                return ($this->db->affected_rows() == 1) ? true : "Referral already Completed";
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function get_clinic_physicians_model() {
        $this->db->select(
                "md5(id) as id, " .
                "concat('Dr. ', first_name, ' ', last_name) as physician_name");
        $this->db->from("clinic_physician_info");
        $this->db->where(
                array(
                    "active" => 1,
                    "clinic_id" => $this->session->userdata("user_id")
                )
        );
        $result = $this->db->get()->result();
        return $result;
    }

    public function log_data_points_model() {
        $data = $this->input->post();
        $efax_id = $this->get_decrypted_id($data["efax_id"], "efax_info");
        $this->db->insert("count_data_points", array(
            "login_role" => $this->session->userdata("login_role"),
            "login_user_id" => $this->session->userdata("user_id"),
            "data_points" => $data["data_points"],
            "efax_id" => $efax_id,
            "api" => $data["api"]
        ));
    }

    public function assign_physician_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('target', 'Assign Physician', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                //auth and assign physician
                $authorized = $this->check_physician($data["target"]);
                if ($authorized !== false || $data["target"] == "unassign") {
                    $physician_id = $authorized;
                    if ($data["target"] == "unassign") {
                        $physician_id = 0; // means unassign
                    }
                    $referral_id = $this->get_referral_id($data['id']);
                    $this->db->where(
                            array(
                                "active" => 1,
                                "id" => $referral_id
                            )
                    );
                    $this->db->update("clinic_referrals", array(
                        "assigned_physician" => $physician_id
                    ));
                    return ($this->db->affected_rows() == 1) ? true : "Referral Not Assigned";
                }
            }
            return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function set_priority_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('target', 'Priority', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->where(
                        array(
                            "active" => 1,
                            "md5(patient_id)" => $data['id']
                        )
                );
                $this->db->update("referral_clinic_triage", array(
                    "priority" => $data['target']
                ));
                return ($this->db->affected_rows() == 1) ? true : "Failed to set Priority";
            }
            return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    //*********************************
    //  Record Management Functions
    //********************************
    public function add_health_record_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('record_type', 'Select Record', 'required');
        // $this->form_validation->set_rules('description', 'Description', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $file_name = "";
            log_message("error", "uploading" . json_encode($_FILES));
            log_message("error", json_encode($_FILES['asdqwe']['name']));
            if (!empty($_FILES['asdqwe']['name']) && $_FILES['asdqwe']['name'][0] != "blob") {
                $target_dir = "./uploads/health_records/";
                $config = array();
                $config['upload_path'] = $target_dir;
                $config['max_size'] = '10000';
                $config['allowed_types'] = 'pdf';
                $config['overwrite'] = FALSE;
                $this->load->library('upload');
                $files = $_FILES;
                $_FILES['asdqwe']['name'] = $files['asdqwe']['name'][0];
                $_FILES['asdqwe']['type'] = $files['asdqwe']['type'][0];
                $_FILES['asdqwe']['tmp_name'] = $files['asdqwe']['tmp_name'][0];
                $_FILES['asdqwe']['error'] = $files['asdqwe']['error'][0];
                $_FILES['asdqwe']['size'] = $files['asdqwe']['size'][0];
                $file_name = $this->generate_random_string();
                $config['file_name'] = $file_name;
                $this->upload->initialize($config);
                if ($this->upload->do_upload('asdqwe')) {
                    // log_message("error", "clinical record attachment uploaded");
                } else {
                    return $this->upload->display_errors();
                }
            }
            // authenticate
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
                if ($this->session->userdata("login_role") == "clinic_physician")
                    $physician_name = $this->session->userdata("physician_name");
                else if ($this->session->userdata("login_role") == "clinic_admin") {
                    $clinic_id = $this->session->userdata("user_id");
                    $this->db->select("clinic_institution_name");
                    $this->db->from("clinic_user_info");
                    $this->db->where(
                            array(
                                "id" => $clinic_id
                            )
                    );
                    $result = $this->db->get()->result();
                    $physician_name = $result[0]->clinic_institution_name;
                }
                $this->db->insert("records_clinic_notes", array(
                    "patient_id" => $patient_id,
                    "record_type" => $data["record_type"],
                    "description" => $data["description"],
                    "record_file" => $file_name,
                    "physician" => $physician_name
                        )
                );
                return true;
            } else {
                return "Unauthorized access attempt";
            }
        } else
            return validation_errors();
    }

    public function add_admin_note_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('note_type', 'Select Record', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            // authenticate
            $this->db->select("efax.id");
            $this->db->from("efax_info efax, clinic_referrals c_ref, referral_patient_info pat");
            $this->db->where(
                    array(
                        "efax.active" => 1,
                        "c_ref.active" => 1,
                        "pat.active" => 1,
                        "md5(pat.id)" => $data["id"],
                        "efax.to" => $this->session->userdata("user_id")
                    )
            );
            $this->db->where("c_ref.id", "pat.referral_id", false);
            $this->db->where("c_ref.efax_id", "efax.id", false);
            $result = $this->db->get()->result();
            if ($result) {
                $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
                $this->db->insert("records_admin_notes", array(
                    "patient_id" => $patient_id,
                    "note_type" => $data["note_type"],
                    "description" => $data["description"]
                ));
                return true;
            } else {
                return "Unauthorized for this Operation";
            }
        } else
            return validation_errors();
    }

    public function add_patient_visit_model() {
        log_message("error", "reaching right place");
        $this->form_validation->set_rules('id', 'Patient', 'required');
        // $this->form_validation->set_rules('visit_name', 'Visit Name', 'required|min_length[2]');
//        $this->form_validation->set_rules('visit_date', 'Date', 'required');
//        $this->form_validation->set_rules('visit_time', 'Time', 'required');
        $new_visit_duration = 30; // static
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                log_message("error", "inside add patient visit auth");
                $this->db->trans_start();
                $patient_id = $this->get_patient_id($data["id"]);


                //validate notifications if allowed or not
                $this->db->select('admin.id as clinic_id, c_ref.id as referral_id,'
                        . 'CASE WHEN ('
                        . '(pat.cell_phone = NULL OR pat.cell_phone = "") OR '
                        . '(pat.work_phone = NULL OR pat.work_phone = "") OR '
                        . '(pat.home_phone = NULL OR pat.home_phone = "")'
                        . ') THEN "false" ELSE "true" END AS allow_sms,'
                        . 'CASE WHEN (pat.email_id = NULL OR pat.email_id = "") THEN "false" ELSE "true" END AS allow_email, '
                        . "admin.address, pat.email_id, pat.cell_phone, pat.home_phone, pat.work_phone, "
                        . "pat.fname, pat.lname, admin.clinic_institution_name, admin.call_address");
                $this->db->from("clinic_referrals c_ref, referral_patient_info pat, efax_info efax, clinic_user_info admin");
                $this->db->where(array(
                    "efax.active" => 1,
                    "admin.active" => 1,
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "pat.id" => $patient_id
                ));
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $this->db->where("efax.to", "admin.id", false);
                $this->db->where("c_ref.efax_id", "efax.id", false);
                $result = $this->db->get()->result();

                log_message("error", "Add patient visit => " . json_encode($result));
//                echo $this->db->last_query();
                if ($result) {

                    $allow_sms = $result[0]->allow_sms;
                    $allow_email = $result[0]->allow_email;
                    if (!(isset($data["cell_phone"]) || isset($data["cell_phone_voice"])) && $allow_sms == "false") {
                        return "Please add phone number for patient first.";
                    }
                    if (isset($data["email"]) && $allow_email == "false") {
                        return "Please add email-id for patient first.";
                    }


                    $msg_data = $result[0];
                    $confirm_visit_key = generate_random_string(120);
//                    $weekdays = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");

                    $response = $this->assign_slots($new_visit_duration, $patient_id);
                    if ($response["result"] === "error") {
                        $response = false;
                    } else if ($response["result"] === "success") {
                        $allocations = $response["data"];
//                    echo "<br/> ****************** <br/>" . "slots assigned = " . json_encode($allocations) . "<br/><br/>";
//                    exit();
                        $start_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["start_time"]);
                        $end_time1 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[0]["end_time"]);
                        $start_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["start_time"]);
                        $end_time2 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[1]["end_time"]);
                        $start_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["start_time"]);
                        $end_time3 = DateTime::createFromFormat('Y-m-d H:i:s', $allocations[2]["end_time"]);

                        $call_immediately = false;
                        $contact_number = $msg_data->cell_phone;
                        if ($msg_data->home_phone != "") {
                            //home number
                            $contact_number = $msg_data->home_phone;
                            $call_immediately = true;
                        } else if ($msg_data->work_phone != "") {
                            //work number
                            $contact_number = $msg_data->work_phone;
                            $call_immediately = true;
                        }

                        if ($call_immediately) {
                            $expire_minutes = "10";
                        } else {
                            $expire_minutes = "60";
                        }

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
                        //insert for temp storage for 60 min sms response
                        $insert_data = array(
                            "patient_id" => $patient_id,
                            "visit_name" => $data["visit_name"],
                            "visit_date1" => $start_time1->format("Y-m-d"),
                            "visit_start_time1" => $start_time1->format("H:i:s"),
                            "visit_end_time1" => $end_time1->format("H:i:s"),
                            "visit_date2" => $start_time2->format("Y-m-d"),
                            "visit_start_time2" => $start_time2->format("H:i:s"),
                            "visit_end_time2" => $end_time2->format("H:i:s"),
                            "visit_date3" => $start_time3->format("Y-m-d"),
                            "visit_start_time3" => $start_time3->format("H:i:s"),
                            "visit_end_time3" => $end_time3->format("H:i:s"),
                            "visit_expire_time" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT" . $expire_minutes . "M"))->format("Y-m-d H:i:s"),
                            "notify_type" => ($call_immediately) ? "call" : "sms",
                            "notify_voice" => 1,
                            "notify_sms" => 1,
                            "notify_email" => 1,
                            //                        "reminder_1h" => ($call_immediately) ? null : (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT1H"))->format("Y-m-d H:i:s"),
                            //                        "reminder_24h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("P1D"))->format("Y-m-d H:i:s"),
                            //                        "reminder_48h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("P2D"))->format("Y-m-d H:i:s"),
                            //                        "reminder_72h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("P3D"))->format("Y-m-d H:i:s"),
                            "reminder_1h" => ($call_immediately) ? null : (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT10M"))->format("Y-m-d H:i:s"),
                            "reminder_24h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT20M"))->format("Y-m-d H:i:s"),
                            "reminder_48h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT30M"))->format("Y-m-d H:i:s"),
                            "reminder_72h" => (new DateTime(date("Y-m-d H:i:s")))->add(new DateInterval("PT40M"))->format("Y-m-d H:i:s"),
                            "confirm_visit_key" => $confirm_visit_key,
                            "visit_confirmed" => "N/A"
                        );

                        //                    echo "call/sms => " . (($call_immediately) ? "call" : "sms");
                        //                    echo "date reserved = " . json_encode($insert_data) . "<br/>";

                        $this->db->insert("records_patient_visit_reserved", $insert_data);

                        $insert_id = $this->db->insert_id();

                        if ($call_immediately) {
                            $post_arr = array(
                                'defaultContactFormName' => $msg_data->fname,
                                "patient_lname" => $msg_data->lname,
                                "defaultContactFormName2" => $data["visit_name"],
                                'defaultContactFormName3' => $msg_data->clinic_institution_name,
                                'defaultContactFormName4' => "ddd",
                                'defaultContactFormName5' => "ttt",
                                'defaultContactFormName6' => $contact_number,
                                'address' => $msg_data->call_address,
                                'clinic_id' => $msg_data->clinic_id,
                                'type' => 'first_call',
                                "patient_id" => $patient_id,
                                "notify_voice" => 1,
                                "notify_sms" => 1,
                                "notify_email" => 1,
                                "reserved_id" => $insert_id
                            );


                            //change accepted status to "SMS"
                            $this->db->where(array(
                                "id" => $msg_data->referral_id
                            ))->update("clinic_referrals", array(
                                "accepted_status" => "Call1",
                                "accepted_status_icon" => "green"
                            ));


                            log_message("error", "data for start call = " . json_encode($post_arr));
                            //                        log_message("error", "Call should start now");
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
                            log_message("error", "<br/> call response = " . $resp . "<br/>");
                            log_message("error", "Call completed " . json_encode($resp));
                        } else {
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

                            $msg = str_replace("<patient name>", $msg_data->fname, $msg);
                            $msg = str_replace("<date1>", $visit_datetime[0]["date"], $msg);
                            $msg = str_replace("<time1>", $visit_datetime[0]["time"], $msg);
                            $msg = str_replace("<date2>", $visit_datetime[1]["date"], $msg);
                            $msg = str_replace("<time2>", $visit_datetime[1]["time"], $msg);
                            $msg = str_replace("<date3>", $visit_datetime[2]["date"], $msg);
                            $msg = str_replace("<time3>", $visit_datetime[2]["time"], $msg);
                            $msg = str_replace("<clinic name>", $msg_data->clinic_institution_name, $msg);

                            $this->send_sms($msg_data->cell_phone, $msg);

                            //change accepted status to "SMS"
                            $this->db->where(array(
                                "id" => $msg_data->referral_id
                            ))->update("clinic_referrals", array(
                                "accepted_status" => "SMS",
                                "accepted_status_icon" => "green"
                            ));
                        }
                        $response = true;
                    }
                } else {
                    $response = false;
                }
                $this->db->trans_complete();
                return $response;
            } else {
                return "You are not authorized for such Operation";
            }
        } else {
            return validation_errors();
        }
    }

    public function move_from_accepted_to_scheduled($patient_id, $clinic_id = "") {
        //change patient referral status to scheduled
        $this->db->select("referral_id");
        $this->db->from("referral_patient_info");
        $this->db->where(array("active" => 1, "id" => $patient_id));
        $result = $this->db->get()->result();
        $referral_id = $result[0]->referral_id;
        $this->db->where(array(
            "id" => $referral_id,
            "active" => 1
        ));
        $this->db->update("clinic_referrals", array(
            "status" => "Scheduled",
            "scheduled_datetime" => date("Y-m-d H:i:s")
        ));

        //send status fax
        $this->db->select("c_usr.clinic_institution_name, date_format(c_ref.create_datetime, '%M %D') as referral_received, dr.fax, c_ref.referral_code");
        $this->db->from("clinic_user_info c_usr, efax_info efax, clinic_referrals c_ref, referral_patient_info pat, referral_physician_info dr");
        $this->db->where(array(
            "pat.id" => $patient_id,
            "efax.active" => 1,
            "c_usr.active" => 1,
            "c_ref.active" => 1,
            "pat.active" => 1,
            "dr.active" => 1,
            "dr.patient_id" => $patient_id
        ));
        $this->db->where("efax.to", "c_usr.id", false);
        $this->db->where("efax.id", "c_ref.efax_id", false);
        $this->db->where("pat.referral_id", "c_ref.id", false);
        $result = $this->db->get()->result()[0];

        $file_name = "referral_scheduled.html";
        $replace_stack = array(
            "###clinic_name###" => $result->clinic_institution_name,
            "###referral_code###" => $result->referral_code,
            "###time1###" => $result->referral_received,
            "###time2###" => date("F jS")
        );
        $fax_number = $result->fax;
        log_message("error", "sending fax");
        $this->load->model("referral_model");
        $response = $this->referral_model->send_status_fax($file_name, array(), $replace_stack, $fax_number, "Scheduled Referral", array(), 60, $clinic_id);

        log_message("error", "Last query = " . $this->db->last_query());
    }

    public function confirm_visit_key_model() {
        $key = $this->uri->segment(3);
        // echo $key;
        $this->db->where(array(
            "confirm_visit_key" => $key,
            "active" => 1
        ));
        $updated = $this->db->update("records_patient_visit", array(
            "visit_confirmed" => "Confirmed"
        ));
        if ($updated) {
            echo "<h1> Your visit has been successfully confirmed. Thanks</h1>";
        } else {
            echo "<h1> Visit confirmation failed. May be visit confirmed earlier or something has gone wrong";
        }
    }

    public function cancel_patient_visit_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Patient Visit', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->trans_start();
                $patient_visit_id = $this->get_decrypted_id($data["target"], "records_patient_visit");
                //authenticate patient visit with referral
                $this->db->select("pat.cell_phone, pat.email_id, r_cv.notify_sms, r_cv.notify_email");
                $this->db->from("records_patient_visit r_cv, clinic_referrals c_ref, `referral_patient_info` `pat`");
                $this->db->where(array(
                    "c_ref.active" => 1,
                    "r_cv.active" => 1,
                    "pat.active" => 1,
                    "r_cv.id" => $patient_visit_id
                ));
                $this->db->where("r_cv.patient_id", "pat.id", false);
                $result = $this->db->get()->result();
                if ($result) {
                    $this->db->where(array(
                        "id" => $patient_visit_id
                    ));
                    $this->db->update("records_patient_visit", array(
                        "active" => 0
                    ));
                    //notify them with sms and email
                    // if ($result[0]->notify_sms == "1") {
                    //     $msg = "Your visit with physician has been cancelled.";
                    //     $this->send_sms($result[0]->cell_phone, $msg);
                    // }
                    // //send email
                    // if ($result[0]->notify_email == "1") {
                    //     $msg = "Your visit with physician has been deteled.";
                    //     $template = $msg;
                    //     $this->load->library('email');
                    //     $this->email->from($this->email->smtp_user, "Blockhealth");
                    //     $this->email->to($result[0]->email_id);
                    //     $this->email->subject("Visit Cancelled");
                    //     $this->email->message($template);
                    //     $this->email->send();
                    // }
                    $this->db->trans_complete();
                    return true;
                } else
                    return "You are not authorized for such Operation";
            } else
                return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function update_patient_visit_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Patient Visit', 'required');
        // $this->form_validation->set_rules('visit_name', 'Visit Name', 'required');
        $this->form_validation->set_rules('visit_date', 'Date', 'required');
        $this->form_validation->set_rules('visit_time', 'Time', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->trans_start();
                $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
                $patient_visit_id = $this->get_decrypted_id($data["target"], "records_patient_visit");
                $my_date = date_create_from_format('j F Y', $data["visit_date"]);
                $this->db->where(array(
                    "id" => $patient_visit_id,
                    "patient_id" => $patient_id,
                    "active" => 1
                ));
                $this->db->update("records_patient_visit", array(
                    "visit_name" => $data["visit_name"],
                    "visit_date" => $my_date->format('Y-m-d'),
                    "visit_time" => $data["visit_time"],
                    "notify_sms" => (isset($data["cell_phone"])) ? 1 : 0,
                    "notify_email" => (isset($data["email"])) ? 1 : 0,
                    "notify_voice" => (isset($data["cell_phone_voice"])) ? 1 : 0,
                    "visit_confirmed" => "N/A"
                ));
                $this->db->select("c_pv.visit_name, c_pv.confirm_visit_key, date_format(`c_pv`.`visit_date`,'%M %D, %Y') as visit_date, " .
                        "date_format(`c_pv`.`visit_time`, '%I:%i %p') as visit_time, c_pv.notify_sms, c_pv.notify_email, admin.address," .
                        "pat.email_id, pat.cell_phone, pat.fname, admin.clinic_institution_name");
                $this->db->from("`records_patient_visit` `c_pv`, efax_info efax, clinic_user_info admin, `clinic_referrals` `c_ref`, referral_patient_info pat");
                $this->db->join("clinic_physician_info c_dr", "c_ref.assigned_physician = c_dr.id and c_dr.active = 1", "left");
                $this->db->where(array(
                    "c_pv.active" => 1,
                    "efax.active" => 1,
                    "admin.active" => 1,
                    "c_ref.active" => 1,
                    "pat.active" => 1,
                    "c_pv.id" => $patient_visit_id
                ));
                $this->db->where("c_pv.patient_id", "pat.id", false);
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $this->db->where("efax.`to`", "admin.id", false);
                $this->db->where("`c_ref`.efax_id", "efax.id", false);
                $result = $this->db->get()->result();
//                 log_message("error", "send sms sql = " . $this->db->last_query());
                // log_message("error", "send sms sql = " . json_encode($result));
                if ($result) {
                    $msg_data = $result[0];
                    //send patient visit booked sms
                    $visit_name = (empty($msg_data->visit_name)) ? "" : " '$msg_data->visit_name'";
                    if ($msg_data->notify_sms == "1") {

                        $msg = "Hello <patient name>,\n" .
                                "\n" .
                                "Your appointment<patient visit name> with <clinic name> has been booked for <date> at <time>.\n" .
                                "\n" .
                                "The address is:\n" .
                                "<Address>\n" .
                                "\n" .
                                "Please type 1 to confirm this booking. If this date does not work, please type 2 to alert the clinic staff.\n";
                        $msg = str_replace("<patient name>", $msg_data->fname, $msg);
                        $msg = str_replace("<date>", $msg_data->visit_date, $msg);
                        $msg = str_replace("<time>", $msg_data->visit_time, $msg);
                        $msg = str_replace("<patient visit name>", $visit_name, $msg);
                        $msg = str_replace("<clinic name>", $msg_data->clinic_institution_name, $msg);
                        $msg = str_replace("<Address>", $msg_data->address, $msg);
                        //send sms
                        $this->send_sms($msg_data->cell_phone, $msg);
                    }
                    //send patient visit booked email
                    if ($msg_data->notify_email == "1") {
                        //template implement starts
                        $template = file_get_contents("assets/templates/email_visit_changed.html");
                        $template = str_replace("<patientVisitName/>", $visit_name, $template);
                        $template = str_replace("<clinicName/>", $msg_data->clinic_institution_name, $template);
                        $template = str_replace("<clinicAddress/>", $msg_data->address, $template);
                        $template = str_replace("<date/>", $msg_data->visit_date, $template);
                        $template = str_replace("<time/>", $msg_data->visit_time, $template);
                        $template = str_replace("<name/>", $msg_data->fname, $template);
                        $template = str_replace("###confirm_link###", base_url() . "referral/confirm_visit_key/" . $msg_data->confirm_visit_key, $template);

                        //template implement ends
                        //send mail informing ticket raised
                        // old mail code starts
//                        $this->load->library('email');
//                        $this->email->from($this->email->smtp_user, "Blockhealth");
//                        $this->email->to($msg_data->email_id);
//                        $this->email->subject($msg_data->clinic_institution_name . " : Appointment Alert");
//                        $this->email->message($template);
//                        $this->email->send();
                        // old mail code ends
                        $response = send_mail("", "BlockHealth", $msg_data->email_id, "", $msg_data->clinic_institution_name . ": Appointment Alert", $template);
                    }
                }
                $this->db->trans_complete();
                return true;
            }
            return "You are not authorized for such Operation";
        } else
            return validation_errors();
    }

    public function ssp_health_records_model() {
        $referral_id = $this->uri->segment(3);
        $table = "health_records_dash";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'record_type', 'dt' => 0),
            array('db' => 'description', 'dt' => 1),
            array('db' => 'create_datetime', 'dt' => 2),
            array('db' => 'id', 'dt' => 3)
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        require('ssp.class.php');
        return json_encode(
                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null
                        , "clinic_admin=" . $this->session->userdata("user_id") .
                        " and referral_id='" . $referral_id . "'")
        );
    }

    public function ssp_admin_notes_model() {
        $patient_id = $this->uri->segment(3);
        $table = "admin_notes_dash";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'note_type', 'dt' => 0),
            array('db' => 'description', 'dt' => 1),
            array('db' => 'create_datetime', 'dt' => 2),
            array('db' => 'id', 'dt' => 3)
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        require('ssp.class.php');
        return json_encode(
                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null
                        , "clinic_admin=" . $this->session->userdata("user_id") .
                        " and patient_id='" . $patient_id . "'")
        );
    }

    public function ssp_patient_visits_model() {
        $patient_id = $this->uri->segment(3);
        $table = "patient_visit_dash";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'visit_name', 'dt' => 0),
            array('db' => 'create_datetime', 'dt' => 1),
            array('db' => 'accepted_status', 'dt' => 2),
            array('db' => 'visit_confirmed', 'dt' => 3),
            array('db' => 'accepted_status_icon', 'dt' => 4),
            array('db' => 'id', 'dt' => 5)
        );
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        require('ssp.class.php');
        return json_encode(
                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null
                        , "clinic_admin=" . $this->session->userdata("user_id") .
                        " and patient_id='" . $patient_id . "'")
        );
    }

    public function get_health_record_info_model() {
        $this->form_validation->set_rules('id', 'Patient Id', 'required');
        $this->form_validation->set_rules('target', 'Health Record', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->select("r_cn.record_type, r_cn.description, r_cn.record_file");
                $this->db->from("records_clinic_notes r_cn, clinic_referrals c_ref, referral_patient_info pat");
                $this->db->where(array(
                    "md5(pat.id)" => $data["id"],
                    "md5(r_cn.id)" => $data["target"],
                    "pat.active" => 1,
                    "r_cn.active" => 1,
                    "c_ref.active" => 1
                ));
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $result = $this->db->get()->result();
                log_message("error", "ref health record sql = " . $this->db->last_query());
                if ($result)
                    return $result;
                else
                    return "No Data Found";
            }
            return "Unauthorized Access Attempt";
        } else
            return validation_errors();
    }

    public function get_admin_notes_info_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Admin Note', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->select("r_an.note_type, r_an.description");
                $this->db->from("records_admin_notes r_an, clinic_referrals c_ref, referral_patient_info pat");
                $this->db->where(
                        array(
                            "md5(pat.id)" => $data["id"],
                            "md5(r_an.id)" => $data["target"]
                        )
                );
                $this->db->where("r_an.patient_id", "pat.id", false);
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $result = $this->db->get()->result();
                if ($result)
                    return $result;
                else
                    return "No Data Found";
            }
            return "Unauthorized Access Attempt";
        } else
            return validation_errors();
    }

    public function get_patient_visit_info_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Patient Visit', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->select("md5(r_pv.id) as id, r_pv.visit_name, r_pv.visit_date, " .
                        "r_pv.visit_time, r_pv.notify_sms, r_pv.notify_email");
                $this->db->from("records_patient_visit r_pv , clinic_referrals c_ref, referral_patient_info pat");
                $this->db->where(
                        array(
                            "md5(pat.id)" => $data["id"],
                            "md5(r_pv.id)" => $data["target"]
                        )
                );
                $this->db->where("r_pv.patient_id", "pat.id", false);
                $this->db->where("pat.referral_id", "c_ref.id", false);
                $result = $this->db->get()->result();
                log_message("error", "false q = " . $this->db->last_query());
                if ($result)
                    return $result;
                else
                    return "No Data Found";
            }
            return "Unauthorized Access Attempt";
        } else
            return validation_errors();
    }

    public function update_checklist_item_model() {
        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('target', 'Checklist Item', 'required');
        $this->form_validation->set_rules('param', 'Checklist Item Status', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $authorized = $this->check_authentication($data["id"]);
            if ($authorized) {
                $this->db->where(
                        array(
                            "md5(patient_id)" => $data["id"],
                            "md5(id)" => $data["target"],
                            "active" => 1
                        )
                );
                $this->db->update("referral_checklist", array(
                    "attached" => $data["param"]
                        )
                );

                //if all checked, change status to ""

                return true;
            } else {
                return "Unauthorized Access Attempt";
            }
        } else
            return validation_errors();
    }

    //*******************************
    // Private functions
    //*******************************
    private function get_patient_id($md5_id) {
        $this->db->select("id");
        $this->db->from("referral_patient_info");
        $this->db->where(
                array(
                    "active" => 1,
                    "md5(id)" => $md5_id
                )
        );
        $result = $this->db->get()->result();
        if ($result)
            return $result[0]->id;
        else
            return false;
    }

    private function get_referral_id($md5_patient_id) {
        $this->db->select("referral_id");
        $this->db->from("referral_patient_info");
        $this->db->where(array(
            "active" => 1,
            "md5(id)" => $md5_patient_id
                )
        );
        $result = $this->db->get()->result();
        $referral_id = $result[0]->referral_id;
        return $referral_id;
    }

    private function check_physician($md5_id) {
        $this->db->select("id");
        $this->db->from("clinic_physician_info");
        $this->db->where(
                array(
                    "active" => 1,
                    "clinic_id" => $this->session->userdata("user_id"),
                    "md5(id)" => $md5_id
                )
        );
        $result = $this->db->get()->result();
        if ($result)
            return $result[0]->id;
        else
            return false;
    }

    private function check_authentication($md5_id) {
        $this->db->select("pat.id");
        $this->db->from("clinic_referrals c_ref, efax_info efax, referral_patient_info pat");
        $this->db->where(
                array(
                    "c_ref.active" => 1,
                    "efax.active" => 1,
                    "pat.active" => 1,
                    "efax.to" => $this->session->userdata("user_id"),
                    "md5(pat.id)" => $md5_id
                )
        );
        $this->db->where("c_ref.efax_id", "efax.id", false);
        $this->db->where("c_ref.id", "pat.referral_id", false);
        $result = $this->db->get()->result();
//        log_message("error", "ref auth sql = " . $this->db->last_query());
        return ($result) ? true : false;
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

    private function get_decrypted_id($md5_id, $table_name) {
        $this->db->select("id");
        $this->db->from($table_name);
        $this->db->where(array("md5(id)" => $md5_id));
        $result = $this->db->get()->result();
        log_message("error", "get decrypted sql = " . $this->db->last_query());
        return ($result) ? $result[0]->id : 0;
    }

    public function send_sms($cell_phone_number, $msg) {
        //us country code automation
        $cell_phone_number = "+1" . $cell_phone_number;
        $ac_sid = "AC2da3b84b65b63ccf4f05c27ac1713060";
        $auth_token = "342a214ee959d16bf97ea87579016762";
        $twilio_number = "+13658000973"; //(365) 800-0973

        $msgarr = array(
            'To' => $cell_phone_number,
            'From' => $twilio_number,
            'Body' => $msg
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, "https://api.twilio.com/2010-04-01/Accounts/" . $ac_sid .
                "/Messages.json");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $ac_sid . ":" . $auth_token);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($msgarr));
        $resp = curl_exec($ch);
        if (curl_errno($ch)) {
            return array(
                'status' => 'error',
                'content' => curl_error($ch)
            );
        }
        curl_close($ch);
        log_message("error", "sms sent to " . $cell_phone_number);
        log_message("error", json_encode($resp));
        return true;
    }

    private function send_fax($fax_num, $fax_text_content, $file, $reason, $clinic_id = "") {
        if ($clinic_id == "") {
            $clinic_id = $this->session->userdata("user_id");
        }
        if (strlen($fax_num) == 10) {
            $fax_num = "1" . $fax_num;
        }
        log_message("error", "missing item request to fax = $fax_num");
        $faxnumber = $fax_num; //"16474981226"; 
        $cpsubject = $fax_text_content;
        $cpcomments = $fax_text_content;
//        $files = array($data["txt_form_name"]);

        $this->db->select("id, srfax_number, srfax_email, srfax_pass, srfax_account_num");
        $this->db->from("clinic_user_info");
        $this->db->where(array(
            "active" => 1,
            "id" => $clinic_id
        ));
        $clinic = $this->db->get()->result()[0];
        if ($clinic) {

            $clinic_id = $clinic->id;
            $access_id = $clinic->srfax_account_num;
            $access_pwd = $clinic->srfax_pass;
            $caller_id = $clinic->srfax_number;
            $sender_mail = $clinic->srfax_email;

            $postdata = array(
                'action' => 'Queue_Fax',
                'access_id' => $access_id,
                'access_pwd' => $access_pwd,
                'sCallerID' => $caller_id,
                'sSenderEmail' => $sender_mail,
                'sFaxType' => 'SINGLE',
                'sToFaxNumber' => $faxnumber,
                'sCoverPage' => 'Basic',
                'sCPSubject' => $cpsubject,
                'sCPComments' => $cpcomments,
                'sFileName_1' => "demo.pdf",
                'sFileContent_1' => base64_encode(file_get_contents($file))
                    //            'sFileContent_1' => base64_encode(file_get_contents("uploads/demo.pdf"))
            );

            $curlDefaults = array(
                CURLOPT_POST => 1,
                CURLOPT_HEADER => 0,
                CURLOPT_URL => 'https://www.srfax.com/SRF_SecWebSvc.php',
                CURLOPT_FRESH_CONNECT => 1,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_FORBID_REUSE => 1,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_SSL_VERIFYPEER => TRUE,
                //            CURLOPT_SSL_VEFIFYHOST => 2,
                CURLOPT_POSTFIELDS => http_build_query($postdata)
            );
            $ch = curl_init();
            curl_setopt_array($ch, $curlDefaults);
            $result = curl_exec($ch);
            if (curl_errno($ch)) {
                log_message("error", "Fax Error – " . curl_error($ch));
                return false;
            } else {
                log_message("error", "Fax Result:" . json_encode($result));
                add_fax_count($faxnumber, $clinic->srfax_number, $clinic->id, $reason, "Admin");
                return true;
            }
        } else {
            echo "clinic id = " . $clinic_id;
        }
    }

    /* Code added by ARUN */
    /* public function call_confirm($to_number,$pname,$pvname,$cname,$aDate,$aTime,$address) {

      $sid = 'AC2da3b84b65b63ccf4f05c27ac1713060';
      $token = '342a214ee959d16bf97ea87579016762';
      $twilio_number = "+13658000973";
      $to_number = "+919876907251";
      $url = "http://dev.blockhealth.co/adi-dev/bh_fax/twiml/callhandle.php?pname=".$pname."&pvname=".$pvname."&cname=".$cname."&aDate=".$aDate."&aTime=".$aTime."&address=".$address
      $uri = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Calls.json';
      $auth = $sid . ':' . $token;
      $fields =
      '&Url=' .  urlencode( $url ) .
      '&To=' . urlencode( $to_number ) .
      '&From=' . urlencode( $twilio_number );
      $res = curl_init();
      curl_setopt( $res, CURLOPT_URL, $uri );
      curl_setopt( $res, CURLOPT_POST, 3 );
      curl_setopt( $res, CURLOPT_POSTFIELDS, $fields );
      curl_setopt( $res, CURLOPT_USERPWD, $auth );
      curl_setopt( $res, CURLOPT_RETURNTRANSFER, true );
      $result = curl_exec( $res );
      $resp = json_decode($result);
      $status = curl_getinfo($res, CURLINFO_HTTP_CODE);

      if (curl_errno($res)) {
      log_message("error", " Error – " . curl_error($res));
      return false;
      } else {
      log_message("error", "Calling");
      return true;
      }
      } */

    private function filter_reserved($unfiltered_visits, $next_day) {
//        echo "filtering now <br/>";
        $filtered = array();
        foreach ($unfiltered_visits as $key => $value) {
//            echo "loop ===> " . json_encode($value) . "<br/>";
            if (substr($value->visit_start_time1, 0, 10) >= $next_day) {
                $filtered[] = array(
                    "visit_start_time" => $value->visit_start_time1,
                    "visit_end_time" => $value->visit_end_time1
                );
            }
            if (substr($value->visit_start_time2, 0, 10) >= $next_day) {
                $filtered[] = array(
                    "visit_start_time" => $value->visit_start_time2,
                    "visit_end_time" => $value->visit_end_time2
                );
            }
            if (substr($value->visit_start_time3, 0, 10) >= $next_day) {
                $filtered[] = array(
                    "visit_start_time" => $value->visit_start_time3,
                    "visit_end_time" => $value->visit_end_time3
                );
            }
        }
//        echo "after filtering <br/>";
//        echo json_encode($filtered) . "<br/>";
        return $filtered;
    }

    public function assign_slots($new_visit_duration, $patient_id) {

        //get physician assigned to patient
        $assigned = $this->db->select("c_ref.assigned_physician")
                        ->from("referral_patient_info pat, clinic_referrals c_ref")
                        ->where(array(
                            "pat.active" => 1,
                            "c_ref.active" => 1,
                            "pat.id" => $patient_id
                        ))
                        ->where("pat.referral_id", "c_ref.id", false)
                        ->get()->result();

        if ($assigned) {
            $assigned_physician = $assigned[0]->assigned_physician;

            $next_day = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("+1 day")));

            $visits_booked = $this->db
                            ->select(
                                    "concat(r_pv.visit_date, ' ', r_pv.visit_time) as visit_start_time, "
                                    . "concat(r_pv.visit_date, ' ', r_pv.visit_end_time) as visit_end_time")
                            ->from("records_patient_visit r_pv, referral_patient_info pat, "
                                    . "clinic_referrals c_ref")
                            ->where(array(
                                "r_pv.active" => 1,
                                "pat.active" => 1,
                                "c_ref.active" => 1,
                                "r_pv.visit_date >= " => $next_day->format('Y-m-d'),
                                "c_ref.assigned_physician" => $assigned_physician
                            ))
                            ->where("r_pv.patient_id", "pat.id", false)
                            ->where("pat.referral_id", "c_ref.id", false)
                            ->order_by("1")->get()->result();

            log_message("error", "visits booked = " . json_encode($visits_booked));
            log_message("error", "visits booked = " . $this->db->last_query());
//        echo "visits booked = " . json_encode($visits_booked) . "<br/>";
//        echo "visits booked = " . $this->db->last_query() . "<br/>";

            $visits_reserved = $this->db
                            ->select(
                                    "concat(r_pvr.visit_date1, ' ', r_pvr.visit_start_time1) as visit_start_time1, "
                                    . "concat(r_pvr.visit_date1, ' ', r_pvr.visit_end_time1) as visit_end_time1,"
                                    . "concat(r_pvr.visit_date2, ' ', r_pvr.visit_start_time2) as visit_start_time2, "
                                    . "concat(r_pvr.visit_date2, ' ', r_pvr.visit_end_time2) as visit_end_time2,"
                                    . "concat(r_pvr.visit_date3, ' ', r_pvr.visit_start_time3) as visit_start_time3, "
                                    . "concat(r_pvr.visit_date3, ' ', r_pvr.visit_end_time3) as visit_end_time3")
                            ->from("records_patient_visit_reserved r_pvr, referral_patient_info pat, "
                                    . "clinic_referrals c_ref")
                            ->where(array(
                                "r_pvr.active" => 1,
                                "pat.active" => 1,
                                "c_ref.active" => 1,
                                "c_ref.assigned_physician" => $assigned_physician,
                                "r_pvr.`visit_expire_time` > " => date("Y-m-d H:i:s")
                            ))->group_start()
                            ->where("r_pvr.visit_date1 >= ", $next_day->format('Y-m-d'))
                            ->or_where("r_pvr.visit_date2 >= ", $next_day->format('Y-m-d'))
                            ->or_where("r_pvr.visit_date3 >= ", $next_day->format('Y-m-d'))
                            ->group_end()
                            ->where("r_pvr.patient_id", "pat.id", false)
                            ->where("pat.referral_id", "c_ref.id", false)
                            ->order_by("1")->get()->result();


            log_message("error", "visits booked = " . json_encode($visits_reserved));
            log_message("error", "visits booked = " . $this->db->last_query());

            $visits_reserved = $this->filter_reserved($visits_reserved, $next_day->format('Y-m-d'));
//        echo "visits booked = " . json_encode($visits_booked) . "<br/>";
//        echo "visits reserved = " . json_encode($visits_reserved) . "<br/>";

            $all_visits = array_merge($visits_booked, $visits_reserved);
            //sort by date
            $all_visits = json_decode(json_encode($all_visits));
            usort($all_visits, array($this, "sort_visits_by_date"));

//        echo "<br/><br/>all visits = " . json_encode($all_visits) . "<br/><br/>";
//        echo $this->db->last_query() . "<br/><br/>";

            $visits_booked = $all_visits;
            $available_visit_slots = array();

            $day = $next_day;
            $counter = 0;
            do {
                //for each day
                echo "*** day = " . json_encode($day) . "<br/>";
                $scheduling_day = $this->check_day_availability($day, $assigned_physician);
                echo " [][][][][][] => checking availablility for day for pv to be created = " . json_encode($day->format("Y-m-d")) . "<br/>";
                $day_assigned = false;
//                echo "availability checked fine";

                if ($scheduling_day["available"]) {
                    echo "is available <br/>";
                    $day_start_time = $scheduling_day["day_start_time"];
                    $day_end_time = $scheduling_day["day_end_time"];

                    echo "day times = $day_start_time and $day_end_time <br/>";

                    $processed_keys = 0;
                    $time1 = $scheduling_day["day"] . " " . $day_start_time;

                    $visits_booked_for_day = $this->get_visit_booked_for_day($day, $visits_booked);
                    echo "visits_booked_for_day = " . json_encode($visits_booked_for_day) . "<br/>";
                    if (sizeof($visits_booked_for_day) != 0) {
                        echo "visits_booked_for_day has visits <br/>";

                        for ($key = 0; $key < sizeof($visits_booked_for_day) && !$day_assigned; $key++) {
                            echo "inside for loop <br/>";
                            $processed_keys = $key;
                            $visit_start_time = null;
                            $visit_end_time = null;
                            if (isset($visits_booked_for_day[$key]->visit_start_time)) {
                                $visit_start_time = $visits_booked_for_day[$key]->visit_start_time;
                                $visit_end_time = $visits_booked_for_day[$key]->visit_start_time;
                                $last_visit_end_time = $visits_booked_for_day[sizeof($visits_booked_for_day) - 1]->visit_end_time;
                            } else {
                                $visit_start_time = $visits_booked_for_day[$key]["visit_start_time"];
                                $visit_end_time = $visits_booked_for_day[$key]["visit_end_time"];
                                $last_visit_end_time = $visits_booked_for_day[sizeof($visits_booked_for_day) - 1]["visit_end_time"];
                            }

                            $time2 = $visit_start_time;

                            echo "################ check between " . $time1 . " to " . $time2 . " <br/>";
                            $slot_response = $this->time_slot_available($time1, $time2, $new_visit_duration);
                            echo "1. response from slot = " . json_encode($slot_response) . "<br/>";
                            if ($slot_response["available"]) {
                                $new_visit = array(
                                    "start_time" => $slot_response["start_time"],
                                    "end_time" => $slot_response["end_time"]
                                );
                                $available_visit_slots[] = $new_visit;
                                echo " =====> assigned to " . json_encode($new_visit) . "<br/>";
                                $day_assigned = true;
                            } else {
                                //check for next visit
                                echo "setting time1 to visit end time <br/>";
                                $time1 = $visit_end_time;
                            }
                        }
                        //check for day start time to visit 1
                        if (!$day_assigned) {
                            $time1 = $last_visit_end_time;
                            $time2 = $scheduling_day["day"] . " " . $day_end_time;
                            echo "at end of day <br/>";
                            echo "################ check between " . $time1 . " to " . $time2 . " <br/>";
                            $slot_response = $this->time_slot_available($time1, $time2, $new_visit_duration);
                            echo "2. response from slot = " . json_encode($slot_response) . "<br/>";
                            if ($slot_response["available"]) {
                                $new_visit = array(
                                    "start_time" => $slot_response["start_time"],
                                    "end_time" => $slot_response["end_time"]
                                );
                                $available_visit_slots[] = $new_visit;
                                echo " =====> assigned to " . json_encode($new_visit) . "<br/>";
                                $day_assigned = true;
                            }
                        }

//                    $time2 = 
                        echo "should check for visit slot <br/>";
                    } else {
                        echo "visits_booked_for_day has no visits <br/>";
                        $time2 = $scheduling_day["day"] . " " . $day_end_time;

                        $slot_response = $this->time_slot_available($time1, $time2, $new_visit_duration);
                        echo "response from slot = " . json_encode($slot_response) . "<br/>";
                        echo "2. response from slot = " . json_encode($slot_response) . "<br/>";
                        if ($slot_response["available"]) {
                            $new_visit = array(
                                "start_time" => $slot_response["start_time"],
                                "end_time" => $slot_response["end_time"]
                            );
                            $available_visit_slots[] = $new_visit;
                            echo " =====> assigned to " . json_encode($new_visit) . "<br/>";
                            $day_assigned = true;
                        }
                    }
                } else {
                    echo "is not available <br/>";
                }
                $day = $day->modify('+1 day');
                echo "moving to " . $day->format("Y-m-d") . "<br/>";
                $counter++;
            } while (sizeof($available_visit_slots) < 3 && $counter < 100);


            echo "<br/> ============================================================= <br/>";
            return array(
                "result" => "success",
                "data" => $available_visit_slots
            );
        } else {
            return array(
                "result" => "error",
                "message" => "Assign physician to patient before scheduling"
            );
        }
//        $clinic_id = 1;
    }

    private function sort_visits_by_date($a, $b) {
        return ($a->visit_start_time > $b->visit_start_time);
    }

    private function time_slot_available($time1, $time2, $new_visit_duration) {
        //echo "### called time_slot_available" . "<br/>";
        //echo json_encode($time1) . "<br/>";
        //echo json_encode($time2) . "<br/>";

        $datetime1 = DateTime::createFromFormat('Y-m-d H:i:s', $time1);
        $datetime2 = DateTime::createFromFormat('Y-m-d H:i:s', $time2);


        $gap = $datetime1->diff($datetime2);
        //echo "gap = " . json_encode($gap) . "<br/>";
        $gap_in_minutes = ($gap->h * 60) + $gap->i;

        if ($gap_in_minutes > $new_visit_duration) {
            $response = array(
                "available" => true,
                "start_time" => $datetime1->format("Y-m-d H:i:s"),
                "end_time" => $datetime1->add(new DateInterval("PT" . $new_visit_duration . "M"))->format("Y-m-d H:i:s")
            );
        } else {
            $response = array(
                "available" => false
            );
        }
        return $response;
    }

    private function day_of($visit) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $visit->visit_start_time)->format("Y-m-d");
        return $date;
    }

    private function get_visit_booked_for_day($day, $visits_booked) {
//        echo json_encode($visits_booked) . "<br/>" . json_encode($day);
        //echo "### called get_visit_booked_for_day <br/>";
        $visits_booked_for_day = array();
//        echo "<br/>CHECK HERE<br/>";
        foreach ($visits_booked as $key => $value) {
//            echo json_encode($value);
            if (isset($value->visit_start_time)) {
                $visit_day = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_start_time)->format("Y-m-d");
                if ($visit_day === $day->format("Y-m-d")) {
                    $visits_booked_for_day[] = $value;
//                    echo "##### valuEEE = " . json_encode($value) . "<br/>";
                }
            } else if (isset($value->visit_start_time1)) {
                // reserved visit
                $visit_start_time = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_start_time1);
                if ($visit_start_time->format("Y-m-d") === $day->format("Y-m-d")) {
                    $visit_end_time = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_end_time1);
                    $visits_booked_for_day[] = array(
                        "visit_start_time" => $visit_start_time->format("Y-m-d H:i:s"),
                        "visit_end_time" => $visit_end_time->format("Y-m-d H:i:s")
                    );

                    $visit_start_time = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_start_time2);
                    $visit_end_time = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_end_time2);
                    $visits_booked_for_day[] = array(
                        "visit_start_time" => $visit_start_time->format("Y-m-d H:i:s"),
                        "visit_end_time" => $visit_end_time->format("Y-m-d H:i:s")
                    );

                    $visit_start_time = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_start_time3);
                    $visit_end_time = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_end_time3);
                    $visits_booked_for_day[] = array(
                        "visit_start_time" => $visit_start_time->format("Y-m-d H:i:s"),
                        "visit_end_time" => $visit_end_time->format("Y-m-d H:i:s")
                    );
                }
            }
        }
        //echo json_encode($visits_booked_for_day) . "<br/>";
        return $visits_booked_for_day;
    }

    private function check_day_availability($day, $assigned_physician) {
        echo "checking day availability. here now<br/>";
        if ($this->check_for_specific_leaves($day)) {
            echo "checking availability of day ".json_encode($day) . " <br/>";
            echo "dr = " . $assigned_physician;
            $availability_response = $this->check_for_weekend_days($day, $assigned_physician);
            echo "called wekend function.";
//            if ($availability_response["available"]) {
//                return $availability_response;
//            }
        }
        echo "specific leave false . <br/>";
        return array(
            "available" => false
        );
    }

    private function check_for_weekend_days($day, $assigned_physician) {
        //convert day to weekday name
        echo "### called check_for_weekend_days <br/>";
        echo "day = " . json_encode($day) . "<br/>";
        
//        $weekday_name = strtolower($day->format('D'));
//        $day = strtolower($day->format('Y-m-d'));
//        $data = $this->db->select("$weekday_name as available, start_time, end_time")
//                        ->from("schedule_visit_settings")->where(array(
//                    "clinic_physician_id" => $assigned_physician, //convert to session then
//                    "active" => "yes"
//                ))->get()->result();
//
//        echo json_encode($day) . "<br/>";
//        log_message("error", "check_for_weekend_days = " . $this->db->last_query());
//
//
//        if ($data) {
//            if ($data[0]->available === "yes") {
//                $response = array(
//                    "available" => true,
//                    "day_start_time" => $data[0]->start_time,
//                    "day_end_time" => $data[0]->end_time,
//                    "day" => $day
//                );
//            } else {
//                $response = array(
//                    "available" => false
//                );
//            }
//        } else {
//            $response = array(
//                "available" => false
//            );
//        }
//        echo "response = " . json_encode($response) . "<br/>";
//        return $response;
    }

    private function check_for_specific_leaves($day) {
        return true;
    }

}
