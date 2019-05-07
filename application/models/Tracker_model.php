<?php

class Tracker_model extends CI_Model {

    public function signup_model() {
        $this->form_validation->set_rules('fname', 'First Name', 'required');
        $this->form_validation->set_rules('lname', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[referring_physicians.email_id]');
        $this->form_validation->set_rules('pass', 'Password', 'required');
        $this->form_validation->set_rules('confirm_pass', 'Confirm Password', 'required|matches[pass]');

        if ($this->form_validation->run()) {
            $data = $this->input->post();

            //get fax number from clinic
            $fax = "";
            $fax_result = $this->db->select("dr.fax")->from("referral_physician_info dr")
            ->join("referral_patient_info pat", "pat.id = dr.patient_id and pat.active = 1")
            ->join("clinic_referrals c_ref", "c_ref.id = pat.referral_id and c_ref.active = 1 and c_ref.referral_code = '".$this->session->userdata("referral_code")."'")
            ->where("dr.active = 1")
            ->get()->result();
            if($fax_result) {
                $fax = $fax_result[0]->fax;
            }

            log_message("error", "fetching fax = $fax =>" . $this->db->last_query());
            //rishabh@gmail.com

            $signup_data = array(
                "first_name" => $data["fname"],
                "last_name" => $data["lname"],
                "email_id" => $data["email"],
                "password" => $data["pass"],
                "address" => isset($data["address"])?$data["address"]:"",
                "phone_number" => isset($data["phone_number"])?$data["phone_number"]:"",
                "fax_number" => $fax,
                "billing_code" => isset($data["billing_code"])?$data["billing_code"]:"",
                "referral_code" => $this->session->userdata("referral_code")
            );

            $inserted = $this->db->insert("referring_physicians", $signup_data);
            if ($inserted) {
                // set all session data
                log_message("error", "should successfuly login with user_id");
                $this->session->set_userdata("user_id", $this->db->insert_id());
                $this->session->set_userdata("physician_name", $data["fname"] . ' ' . $data["lname"]);
                $this->session->set_userdata("signup_done", "yes");
                $this->session->set_userdata("fax_number", $fax);
            }

            //set signup flag
            if ($inserted)
                return $inserted;
        } else {
            return validation_errors();
        }
    }

    public function load_tracker_model() {
        $ref_code = $this->input->post("id");

        if ($this->referral_access($ref_code)) {
            $this->db->select("`c_usr`.`clinic_institution_name`, md5(c_usr.id) as id");
            $this->db->from("`clinic_referrals` `c_ref`,"
                    . "`efax_info` `efax`, `clinic_user_info` `c_usr`");
            $this->db->where(array(
                "c_ref.referral_code" => $ref_code,
                "c_ref.active" => 1,
                "efax.active" => 1,
                "c_usr.active" => 1
            ));
            $this->db->where("efax.id", "c_ref.efax_id", false);
            $this->db->where("c_usr.id", "efax.`to`", false);
            $result = $this->db->get()->result();
           log_message("error", "load tracker sql = " . $this->db->last_query());
            $clinic_institution_name = "";
            $clinic_id = "";
            if ($result) {
                $clinic_institution_name = $result[0]->clinic_institution_name;
                $clinic_id = $result[0]->id;
            } else {
                return array(
                    "result" => "error",
                    "msg" => "Clinic Name not found"
                );
            }

            // =========> fetch four stages faxes
            $stage1;
            $stage2;
            $stage3;
            $stage4;

            //stage 1
            $stage1 = "Referral has been received";

            //stage 2
            $this->db->select("if((c_ref.status = 'Referral Triage') and miss.id,"
                    . "'Referral has been triaged and missing items have been requested',"
                    . "if(c_ref.status = 'Referral Triage' OR c_ref.status = 'Admin Triage',"
                    . "'Referral is being triaged',"
                    . "if(c_ref.status = 'Cancelled' or c_ref.status = 'Declined',"
                    . "'Referral has been declined and returned',"
                    . "'Referral has been triaged and accepted'))) "
                    . "AS stage2");

            $this->db->from("`clinic_referrals` c_ref");
            $this->db->join("referral_patient_info pat", "pat.referral_id = c_ref.id and pat.active = 1");
            $this->db->join("referral_missing_item_request_info miss", "miss.active = 1 AND pat.id = miss.patient_id", "left");
            $this->db->where(array(
                "c_ref.referral_code" => $ref_code,
                "c_ref.`active`" => 1
            ));
            $this->db->group_by("miss.patient_id");

            $stage2 = $this->db->get()->result()[0]->stage2;
//            log_message("error", "res 2 = $stage2 => " . $this->db->last_query());
            //check if decline after stage 2.
            if ($stage2 != "Referral has been declined and returned" && $stage2 != "Referral has been triaged and missing items have been requested") {
                //stage 3
                $this->db->select("if(c_ref.status = 'Accepted', 'Patient visit has not yet been scheduled', "
                        . "if(c_ref.status = 'Scheduled', "
                        . "concat('Patient visit has been scheduled for ', "
                        . "lower(date_format(r_pv.visit_time, '%l%p')), ' on ', "
                        . "date_format(r_pv.visit_date, '%M %D')), '')) AS stage3");
                $this->db->from("(`clinic_referrals` `c_ref`, `referral_patient_info` `pat`)");
                $this->db->join("`records_patient_visit` `r_pv`", "`r_pv`.`active` = 1 AND pat.id = r_pv.patient_id", "left");
                $this->db->where(array(
                    "c_ref.referral_code" => $ref_code,
                    "c_ref.active" => 1,
                    "pat.active" => 1
                ));
                $this->db->where("c_ref.id", "pat.referral_id", false);
                $result = $this->db->get()->result();
//                log_message("error", "sql 3 = " . $this->db->last_query());
                if ($result) {
                    $stage3 = $result[0]->stage3;
//                    log_message("error", "res 3 = $stage3");
                    //check if decline after stage 3
                    if ($stage3 != "Patient visit has not yet been scheduled") {
                        //stage 4
                        $this->db->select("if(c_ref.status='Scheduled',if(r_pv.id, 'Visit has been confirmed', 'Waiting for patient confirmation'), '') as stage4");
                        $this->db->from("clinic_referrals c_ref");
                        $this->db->join("referral_patient_info pat", "pat.referral_id = c_ref.id");
                        $this->db->join("records_patient_visit r_pv", "r_pv.patient_id = pat.id and r_pv.visit_confirmed = 'Awaiting Confirmation' and r_pv.active = 1", "left");
                        $this->db->where(array(
                            "c_ref.referral_code" => $ref_code,
                            "c_ref.active" => 1,
                            "pat.active" => 1
                        ));
                        $this->db->group_by("r_pv.patient_id");
                        $result = $this->db->get()->result();
                        $stage4 = $result[0]->stage4;
//                        log_message("error", "res 4 = $stage4 => " . $this->db->last_query());
                        //all stages are fetched
                    } else {
                        $stage4 = "";
                    }
                } else {
                    $stage3 = "";
                    $stage4 = "";
                }
            } else {
                $stage3 = "";
                $stage4 = "";
            }

            //replace data in template
            $this->load->helper('file');
            $content = read_file("assets/templates/tracker.html");

            $content = str_replace("###time1###", "", $content);
            $content = str_replace("###time2###", "", $content);
            $content = str_replace("###time3###", "", $content);
            $content = str_replace("###time4###", "", $content);

            $content = str_replace("###text1###", $stage1, $content);
            $content = str_replace("###text2###", $stage2, $content);
            $content = str_replace("###text3###", $stage3, $content);
            $content = str_replace("###text4###", $stage4, $content);

            $content = str_replace("###physician_name###", $clinic_institution_name, $content);

            $this->db->select("pat.id")
                    ->from("clinic_referrals c_ref")
                    ->join("referral_patient_info pat", "pat.referral_id = c_ref.id and pat.active = 1")
                    ->join("referral_missing_item_request_info miss", "pat.id = miss.patient_id and miss.active = 1")
                    ->where(array(
                        "c_ref.referral_code" => $ref_code,
                        "c_ref.active" => 1
            ));
            $result = $this->db->get()->result();
            $missing_items = null;
            if ($result) {
                $patient_id = $result[0]->id;
                $missing_items = $this->db->select("if (chk.checklist_type = 'stored',itm.name, chk.checklist_name ) AS item_name, chk.id")
                        ->from("referral_checklist chk")
                        ->join("clinic_referral_checklist_items itm", "chk.checklist_id = itm.id AND itm.active = 1", "left")
                        ->where(array(
                            "chk.active" => 1,
                            "chk.patient_id" => $patient_id,
                            "chk.attached" => "false"
                ))->get()->result();
                log_message("error", "checklist at RP side = " . $this->db->last_query());
            }

            //set data
            $data = array(
                "content" => $content,
                "stage1" => $stage1,
                "stage2" => $stage2,
                "stage3" => $stage3,
                "stage4" => $stage4,
                "clinic_id" => $clinic_id,
                "missing_items" => $missing_items,
                "clinic_name" => $clinic_institution_name
            );
            //        log_message("error", "data = " . json_encode($data));
            return $data;
        } else {
            return "Referral is not accessible";
        }
    }

    private function referral_access($ref_code) {
        $user_id = $this->session->userdata("user_id");
        $this->db->select("c_ref.id");
        $this->db->from("clinic_referrals c_ref");
        $this->db->join("referral_patient_info pat", "c_ref.id = pat.referral_id and pat.active = 1");
        $this->db->join("referral_physician_info dr", "pat.id = dr.patient_id and dr.active = 1");
        $this->db->where(array(
            "c_ref.referral_code" => $ref_code,
            "c_ref.active" => 1,
            "dr.fax" => $this->session->userdata("fax_number")
        ));
        $result = $this->db->get()->result();
        if ($result) {
            return true;
        } else {
            return false;
        }
        if ($this->session->userdata("signup_done") == "no") {
            if ($this->session->userdata("referral_code") == $ref_code) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function upload_missing_items_model() {
        try {
            $data = $this->input->post();
            $this->db->trans_start();
            if (!empty($_FILES['missing_file']) && $_FILES['missing_file'] != "blob") {
                $target_dir = "./uploads/health_records/";
                $config = array();
                $config['upload_path'] = $target_dir;
                $config['max_size'] = '10000';
                $config['allowed_types'] = 'pdf';
                $config['overwrite'] = FALSE;
                $this->load->library('upload');
                $files = $_FILES["missing_file"];
                $names = $this->input->post("file_name");
                $referral_code = $data["id"];
                $patient_id = $this->get_patient_id_from_referral_code($data["id"]);

                // echo json_encode($data);
                $loop = 0;
                for ($loop = 0; $loop < sizeof($files['name']); $loop++) {
                    // echo json_encode($files) . "<br/><br/>";
                    $_FILES['sample']['name'] = $files['name'][$loop];
                    $_FILES['sample']['type'] = $files['type'][$loop];
                    $_FILES['sample']['tmp_name'] = $files['tmp_name'][$loop];
                    $_FILES['sample']['error'] = $files['error'][$loop];
                    $_FILES['sample']['size'] = $files['size'][$loop];
                    $file_name = generate_random_string();
                    $config['file_name'] = $file_name . ".pdf";
                    $this->upload->initialize($config);
                    // echo "File uploading $file_name <br/><br/>";
                    if ($this->upload->do_upload('sample')) {
                        // echo "File Uploaded $file_name <br/><br/>";
                    } else {
                        // echo $target_dir;
                        return $this->upload->display_errors();
                    }

                    //insert as health record of patient
                    $this->db->insert("records_clinic_notes", array(
                        "patient_id" => $patient_id,
                        "record_type" => "Missing Record",
                        "physician" => $this->session->userdata("physician_name"),
                        "description" => "Missing item uploaded from referring physician",
                        "record_file" => $file_name
                    ));

                    //insert as missing item
                    $record_id = $this->db->insert_id();
                    $this->db->insert("records_missing_items", array(
                        "referral_code" => $referral_code,
                        "patient_id" => $patient_id,
                        "record_id" => $record_id,
                        "description" => $names[$loop]
                    ));


                    //update missing status
                    $referral_id = $this->get_referral_id($patient_id); 
                    $this->db->where(array(
                        "id" => $referral_id
                    ));
                    $this->db->update("clinic_referrals", array(
                        "missing_item_status" => "Items uploaded for review"
                    ));
                }
                $this->db->trans_complete();
                return true;
            }
        } catch (Exception $e) {
            echo json_encode($e);
        }
    }

    public function get_upload_missing_item_info_model() {
        
        $this->form_validation->set_rules('id', 'Identification', 'required');

        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $ref_code = $data["id"];
            if ($this->referral_access($ref_code)) {
                $this->db->select("r_miss.id, r_miss.description, r_cn.record_file, "
                        . "date_format(r_cn.create_datetime, '%M %d, %Y') as record_date")
                        ->from("records_missing_items r_miss, records_clinic_notes r_cn")
                        ->where(array(
                            "r_cn.active" => 1,
                            "r_miss.active" => 1,
                            "r_miss.referral_code" => $ref_code
                ));
                $this->db->where("r_miss.record_id", "r_cn.id", false);
                
                return array(
                    "result" => "success",
                    "info" => $this->db->get()->result()
                );
            } else {
                return "Referral access denied";
            }
        } else {
            return validation_errors();
        }
    }

    private function get_patient_id_from_referral_code($referral_code) {
        $this->db->select("pat.id")->from("referral_patient_info pat")
                ->join("clinic_referrals c_ref", "pat.referral_id = c_ref.id and c_ref.active = 1 and c_ref.referral_code = '$referral_code'")
                ->where(array(
                    "pat.active" => 1
        ));
        $result = $this->db->get()->result();
        if ($result) {
            return $result[0]->id;
        } else {
            return 0;
        }
    }
    
    public function remove_missing_item_model() {
        $this->form_validation->set_rules('id', 'Identification', 'required');

        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $missing_id = $data["id"];
            log_message("error", "missing id = $missing_id");
            if (1) { //$this->referral_access($ref_code)) {
                $this->db->select("record_id");
                $this->db->from("records_missing_items");
                $this->db->where(array(
                    "id" => $missing_id
                ));
                $record_id = $this->db->get()->result()[0]->record_id;

                log_message("error", "record id = $record_id");
                        
                $this->db->where(array(
                    "id" => $record_id
                ));
                $this->db->update("records_admin_notes", array(
                    "active" => 0
                ));

            log_message("error", "remove 1 = " . $this->db->last_query());
                
                $this->db->where(array(
                    "id" => $missing_id
                ));
                $this->db->update("records_missing_items", array(
                    "active" => 0
                ));
            log_message("error", "remove 2 = " . $this->db->last_query());
                
                return array(
                    "result" => "success"
                );
            } else {
                return "Referral access denied";
            }
        } else {
            return validation_errors();
        }
    }

    private function get_referral_id($patient_id) {
        $this->db->select("referral_id");
        $this->db->from("referral_patient_info");
        $this->db->where(array(
            "active" => 1,
            "id" => $patient_id
        ));
        $result = $this->db->get()->result();
        $referral_id = $result[0]->referral_id;
        return $referral_id;
    }

}
