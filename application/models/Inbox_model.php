<?php

class Inbox_model extends CI_Model {

    public function ssp_inbox_model() {
        $table = "inbox_dash";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'act_date', 'dt' => 0),
            array('db' => 'act_time', 'dt' => 1),
            array('db' => 'fax_number', 'dt' => 2),
            array('db' => 'pages', 'dt' => 3),
            array('db' => 'id', 'dt' => 4),
            array('db' => 'tif_file_name', 'dt' => 5),
            array('db' => 'pdf_file_name', 'dt' => 6)
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
                        , "clinic_id =" . $this->session->userdata("user_id"))
        );
    }

    public function ssp_clinic_patients_model() {
        $table = "clinic_patients_dashboard";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'patient_name', 'dt' => 0),
            array('db' => 'dob', 'dt' => 1),
            array('db' => 'ohip', 'dt' => 2),
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
                        , "clinic_id =" . $this->session->userdata("user_id"))
        );
    }

    public function delete_referral_model() {
        $this->form_validation->set_rules('id', 'Efax Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $this->db->where(array(
                "md5(id)" => $data["id"],
                "to" => $this->session->userdata("user_id")
            ));
            $this->db->update("efax_info", array(
                "active" => 0
            ));
            return ($this->db->affected_rows() == 1) ? true : "Unable to Delete Efax";
        } else
            return validation_errors();
    }

    public function save_task_model() {
//        $this->form_validation->set_rules('id', 'Patient', 'required');
        $this->form_validation->set_rules('efax_id', 'Efax Id', 'required');
        $this->form_validation->set_rules('record_type', 'Record Type', 'required');
//        $this->form_validation->set_rules('description', 'Description', 'required');
//        $this->form_validation->set_rules('assign_physician', 'Physician', 'required');
//        $this->form_validation->set_rules('record_type', 'Record Type', 'required');
//        $this->form_validation->set_rules('pat_gender', 'Sex', 'required');
//        $this->form_validation->set_rules('pat_dob_day', 'DOB Day', 'required');
//        $this->form_validation->set_rules('pat_dob_month', 'DOB Month', 'required');
//        $this->form_validation->set_rules('pat_dob_year', 'DOB Year', 'required');
//        $this->form_validation->set_rules('pat_fname', 'First Name', 'required');
//        $this->form_validation->set_rules('pat_lname', 'Last Name', 'required');

        if ($this->form_validation->run()) {
            $this->db->trans_start();
            $data = $this->input->post();
            $efax_id = $this->get_decrypted_id($data["efax_id"], "efax_info");
            $efax_info = $this->db->select("file_name, tiff_file_name, pages, "
                            . "create_datetime, sender_fax_number")->from("efax_info")->where(array(
                        "active" => 1,
                        "referred" => 0,
                        "id" => $efax_id,
                        "to" => $this->session->userdata("user_id")
                    ))->get()->result();
            log_message("error", "efax q = " . $this->db->last_query());

            $this->db->where(array(
                "id" => $efax_id
            ));
            $this->db->update("efax_info", array(
                "referred" => TRUE
            ));
            log_message("error", "setting referred => " . $this->db->last_query());

            $new_file_name = generate_random_string(32);

//            $id = $this->get_decrypted_id($data["id"], "referral_patient_info");
            $physician_id = ((isset($data["assign_physician"])) ?
                    $this->get_decrypted_id($data["assign_physician"], "clinic_physician_info") : 0);
//            $patient_id = ((isset($data["id"])) ? ($this->get_decrypted_id($data["id"], "referral_patient_info")) : 0);
//            $patient_id = 2;
            $this->db->insert("referral_patient_info", array(
                "fname" => $data["pat_fname"],
                "lname" => $data["pat_lname"],
                "dob" => $data["pat_dob_year"] . "-" . $data["pat_dob_month"] . "-" . $data["pat_dob_day"],
                "ohip" => $data["pat_ohip"],
                "gender" => $data["pat_gender"]
            ));

            $patient_id = $this->db->insert_id();
            $clinic_id = md5($this->session->userdata("user_id"));

            $inserted = $this->db->insert("clinic_physician_tasks", array(
                "clinic_id" => $this->session->userdata("user_id"),
                "assigned_to" => $physician_id,
                "patient_id" => $patient_id,
                "record_type" => $data["record_type"],
                "notes" => $data["description"],
                "pdf_file" => $new_file_name . ".pdf",
                "tiff_file" => $new_file_name . ".tif",
                "page_count" => $efax_info[0]->pages,
                "fax_date_time" => $efax_info[0]->create_datetime,
                "sender_fax_number" => $efax_info[0]->sender_fax_number
            ));
            log_message("error", "insert = " . $this->db->last_query());
            $task_id = $this->db->insert_id();

            if (!file_exists("./" . files_dir() . "$clinic_id")) {
                log_message("error", "creating clinic folder =>" . "./" . files_dir() . "$clinic_id");
                mkdir("./" . files_dir() . "$clinic_id");
            }
            if (!file_exists("./" . files_dir() . "$clinic_id/" . md5($patient_id))) {
                log_message("error", "creating patient folder =>" . "./" . files_dir() . "$clinic_id/" . md5($patient_id));
                mkdir("./" . files_dir() . "$clinic_id/" . md5($patient_id));
            }
            if ((isset($data["id"])) && $data["id"] != "") {

                $inserted = $this->db->insert("records_clinic_notes", array(
                    "patient_id" => $this->get_decrypted_id($data["id"], "referral_patient_info"),
                    "physician" => "Admin",
                    "record_type" => $data["record_type"],
                    "description" => $data["description"],
                    "record_file" => $new_file_name
                ));

                copy("./uploads/efax/" . $efax_info[0]->file_name . ".pdf", files_dir() . "$clinic_id/" . md5($patient_id) . "/" . $new_file_name . ".pdf");
                log_message("error", "patient record => " . $efax_id . " => ./uploads/efax/" . $efax_info[0]->file_name . ".pdf to ./" . files_dir() . "$clinic_id/" . md5($patient_id) . "/" . $new_file_name . ".pdf");
            }


//            log_message("error", $efax_id . "./uploads/efax/" . $efax_info[0]->file_name . ".pdf to ./uploads/physician_tasks/pdf/" . $new_file_name . ".pdf");
//            log_message("error", $efax_id . "./uploads/efax_tiff/" . $efax_info[0]->tiff_file_name . " to ./uploads/physician_tasks/tiff/" . $new_file_name . ".tif");

            rename("./uploads/efax/" . $efax_info[0]->file_name . ".pdf", "./" . files_dir() . "$clinic_id/" . md5($patient_id) . "/" . $new_file_name . ".pdf");
            rename("./uploads/efax_tiff/" . $efax_info[0]->tiff_file_name, "./" . files_dir() . "$clinic_id/" . md5($patient_id) . "/" . $new_file_name . ".tif");


            //only trigger RPA events (table entry + doc upload API) if pathway name is AccuroCitrix
            //
            $clinic = $this->db->select("first_name, integration_type, emr_pathway, emr_uname_1, emr_pwd_1")
                            ->from("clinic_user_info")
                            ->where("id", $this->session->userdata("user_id"))
                            ->get()->result();

            if ($clinic) {
                $clinic = $clinic[0];

                if ($clinic->emr_pathway === "OscarEMR") {
                    //save to json file for API integration
                    $data_object = array(
                        "api_type" => "save",
                        "api_num" => 3,
                        "date" => date("Y-m-d"),
                        "time" => date("H:i:s"),
                        "status" => "NEW",
                        "first_name" => $data["pat_fname"],
                        "last_name" => $data["pat_lname"],
                        "dob_day" => make_two_digit($data["pat_dob_day"]),
                        "dob_month" => make_two_digit($data["pat_dob_month"]),
                        "dob_year" => $data["pat_dob_year"],
                        "hin" => $data["pat_ohip"],
                        "pdf_name" => "$new_file_name.pdf",
                        "pdf_location" => base_url() . "uploads/clinics/$clinic_id/" . md5($patient_id) . "/" . $new_file_name . ".pdf",
                        "pdf_type" => "Documents",
                        "active" => 1
                    );
                    save_json($this->session->userdata("user_id"), $data_object);
                }
                if ($clinic->emr_pathway === "AccuroCitrix") {
                    //save entry in rpa_integration table
                    $this->db->insert("rpa_integration", array(
                        "api_type" => "save",
                        "api_num" => 3,
                        "fk_id" => $task_id,
                        "date" => date("Y-m-d"),
                        "time" => date("H:i:s"),
                        "status" => "NEW",
                        "pathway" => $clinic->emr_pathway,
                        "clinic_name" => $clinic->first_name,
                        "username" => $clinic->emr_uname_1,
                        "password" => $clinic->emr_pwd_1,
                        "first_name" => $data["pat_fname"],
                        "last_name" => $data["pat_lname"],
                        "dob_day" => make_two_digit($data["pat_dob_day"]),
                        "dob_month" => make_two_digit($data["pat_dob_month"]),
                        "dob_year" => $data["pat_dob_year"],
                        "hin" => $data["pat_ohip"],
                        "pdf_name" => "$new_file_name.pdf",
                        "pdf_location" => base_url() . "uploads/clinics/$clinic_id/" . md5($patient_id) . "/" . $new_file_name . ".pdf",
                        "pdf_type" => "Documents",
                        "assigned_provider" => "Arianna Muskat",
                        "active" => 1
                    ));
                    log_message("error", "inserted to rpa");
                    log_message("error", $this->db->last_query());

                    //send to RPA nitegration
                    $request = curl_init('http://52.237.12.245/api/v1/patients/upload-documents');

                    curl_setopt($request, CURLOPT_POST, true);
                    curl_setopt($request, CURLOPT_POSTFIELDS, array(
                        "pathwayName" => "AccuroCitrix",
                        "username" => "hahmed",
                        "password" => "Blockhealth19",
                        "ClinicName" => "TCN",
                        "source" => "remote",
                        "PDFName" => "$new_file_name.pdf",
                        "PDFRemote" => base_url() . "uploads/clinics/$clinic_id/" . md5($patient_id) . "/" . $new_file_name . ".pdf"
                    ));
                    curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
                    $response = curl_exec($request);
                    curl_close($request);

                    log_message("error", "curl log = " . json_encode($response));
                }
            }

//            log_message("error", "insert = " . $this->db->last_query());

            $this->db->trans_complete();
            log_message("error", "transactions saved");
            if ($inserted) {
                return array(
                    "result" => "success"
                );
            } else {
                return array(
                    "result" => "error",
                    "msg" => "Failed to save patient record"
                );
            }
        } else {
            return array(
                "result" => "error",
                "msg" => validation_errors()
            );
        }
    }

    public function save_patient_record_model() {
        $this->form_validation->set_rules('id', 'Efax Id', 'required');
        $this->form_validation->set_rules('record_type', 'Record Type', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        if ($this->form_validation->run()) {
            $this->db->trans_start();
            $data = $this->input->post();
            $efax_id = $this->get_decrypted_id($data["efax_id"], "efax_info");
            $efax_info = $this->db->select("file_name, tiff_file_name")->from("efax_info")->where(array(
                        "active" => 1,
                        "referred" => 0,
                        "id" => $efax_id,
                        "to" => $this->session->userdata("user_id")
                    ))->get()->result();
            log_message("error", "efax q = " . $this->db->last_query());

            $this->db->where(array(
                "id" => $efax_id
            ));
            $this->db->update("efax_info", array(
                "referred" => TRUE
            ));
            log_message("error", "setting referred => " . $this->db->last_query());

            $new_file_name = generate_random_string(32);

            $patient_id = $this->get_decrypted_id($data["id"], "referral_patient_info");
            $clinic_id = $this->session->userdata("user_id");
            rename("./uploads/efax/" . $efax_info[0]->file_name . ".pdf", files_dir() . "$clinic_id/" . md5($patient_id) . "/" . $new_file_name . ".pdf");
            unlink("./uploads/efax_tiff/" . $efax_info[0]->tiff_file_name);

            $inserted = $this->db->insert("records_clinic_notes", array(
                "patient_id" => $patient_id,
                "record_type" => $data["record_type"],
                "description" => $data["description"],
                "record_file" => $new_file_name
            ));
            log_message("error", "insert = " . $this->db->last_query());

            if ($inserted) {
                return array(
                    "result" => "success"
                );
            } else {
                return array(
                    "result" => "error",
                    "msg" => "Failed to save patient record"
                );
            }
        } else {
            return array(
                "result" => "error",
                "msg" => validation_errors()
            );
        }
    }

    public function patient_autocomplete_model() {
        $term = $this->input->get("term");
        $this->db->select("md5(pat.id) as id, concat(pat.fname, ' ', pat.lname, ' (', pat.dob, ' )') as label, pat.fname, pat.lname, pat.dob, pat.ohip")
                ->from("referral_patient_info pat, clinic_referrals c_ref, efax_info efax")->where(array(
                    "pat.active" => 1,
                    "c_ref.active" => 1,
                    "efax.active" => 1,
                    "efax.to" => $this->session->userdata("user_id")
                ))->group_start()
                ->like("fname", $term)
                ->or_like("lname", $term)
                ->group_end();
        $this->db->where("pat.referral_id", "c_ref.id", false);
        $this->db->where("c_ref.efax_id", "efax.id", false);
        $result = $this->db->get()->result();
        //[{"id":"Ficedula hypoleuca","label":"Eurasian Pied Flycatcher","value":"Eurasian Pied Flycatcher"}]
        $data = array();
        foreach ($result as $key => $value) {
            $data[] = array(
                "id" => $value->id,
                "label" => $value->label,
                "value" => $value->label,
                "fname" => $value->fname,
                "lname" => $value->lname,
                "dob" => $value->dob,
                "ohip" => $value->ohip
            );
        }
        return $data;
    }

    public function check_physician_data_model() {
        $this->form_validation->set_rules('id', 'Efax Id', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $last_name = strtolower($data["dr_lname"]);
            $first_name = strtolower($data["dr_fname"]);
            $phone = $data["dr_phone_number"];
            $fax = str_replace("-", "", $data["dr_fax"]);

            $db_predict = $this->load->database('predictions', TRUE);
            $db_predict->select("ID, lower(`LAST_NAME`) as LAST_NAME_LOWERCASE, lower(`FIRST_NAME`) as FIRST_NAME_LOWERCASE, CPSO, LAST_NAME, FIRST_NAME, ADDRESS_1, PHONE_1, FAX_1, ADDRESS_2, PHONE_2, FAX_2, ADDRESS_3, PHONE_3, FAX_3, ADDRESS_4, PHONE_4, FAX_4, ADDRESS_5, PHONE_5, FAX_5");
            $db_predict->from("stored_physicians");
            $db_predict->where(
                    "lower(LAST_NAME) = lower('$last_name')"
            );
            $result = $db_predict->get()->result();
            log_message("error", "matching physician detail with sql = " . $db_predict->last_query());
            //find doctor match for last name, along with either one of first name, phone, and fax
            $matched = false;
            $matched_data = array();
            foreach ($result as $key => $dr) {
                log_message("error", "checking with" . json_encode($dr));
                $db_first_names = explode(" ", $dr->FIRST_NAME_LOWERCASE);

                if (in_array($first_name, $db_first_names) || (!empty($fax) && $dr->FAX_1 == $fax) || (!empty($phone) && $dr->PHONE_1 == $phone)) {
                    $matched = true;
                    $matched_data["id"] = $dr->ID;
                    $matched_data["first_name"] = $dr->FIRST_NAME;
                    $matched_data["last_name"] = $dr->LAST_NAME;
                    $matched_data["phone"] = $dr->PHONE_1;
                    $matched_data["fax"] = $dr->FAX_1;
                    $matched_data["cpso"] = $dr->CPSO;
                    $matched_data["address"] = $dr->ADDRESS_1;
                    log_message("error", "matched in first");
                    break;
                }

                if ((!empty($fax) && $dr->FAX_2 == $fax) || (!empty($phone) && $dr->PHONE_2 == $phone)) {
                    $matched = true;
                    $matched_data["id"] = $dr->ID;
                    $matched_data["first_name"] = $dr->FIRST_NAME;
                    $matched_data["last_name"] = $dr->LAST_NAME;
                    $matched_data["phone"] = $dr->PHONE_2;
                    $matched_data["fax"] = $dr->FAX_2;
                    $matched_data["cpso"] = $dr->CPSO;
                    $matched_data["address"] = $dr->ADDRESS_2;
                    log_message("error", "matched in second");
                    // $this->session->set_userdata("physician_match_id", $dr->ID);
                    break;
                }

                if ((!empty($fax) && $dr->FAX_3 == $fax) || (!empty($phone) && $dr->PHONE_3 == $phone)) {
                    $matched = true;
                    $matched_data["id"] = $dr->ID;
                    $matched_data["first_name"] = $dr->FIRST_NAME;
                    $matched_data["last_name"] = $dr->LAST_NAME;
                    $matched_data["phone"] = $dr->PHONE_3;
                    $matched_data["fax"] = $dr->FAX_3;
                    $matched_data["cpso"] = $dr->CPSO;
                    $matched_data["address"] = $dr->ADDRESS_3;
                    log_message("error", "matched in first");
                    // $this->session->set_userdata("physician_match_id", $dr->ID);
                    break;
                }

                if ((!empty($fax) && $dr->FAX_4 == $fax) || (!empty($phone) && $dr->PHONE_4 == $phone)) {
                    $matched = true;
                    $matched_data["id"] = $dr->ID;
                    $matched_data["first_name"] = $dr->FIRST_NAME;
                    $matched_data["last_name"] = $dr->LAST_NAME;
                    $matched_data["phone"] = $dr->PHONE_4;
                    $matched_data["fax"] = $dr->FAX_4;
                    $matched_data["cpso"] = $dr->CPSO;
                    $matched_data["address"] = $dr->ADDRESS_4;
                    log_message("error", "matched in fourth");
                    // $this->session->set_userdata("physician_match_id", $dr->ID);
                    break;
                }

                if ((!empty($fax) && $dr->FAX_5 == $fax) || (!empty($phone) && $dr->PHONE_5 == $phone)) {
                    $matched = true;
                    $matched_data["id"] = $dr->ID;
                    $matched_data["first_name"] = $dr->FIRST_NAME;
                    $matched_data["last_name"] = $dr->LAST_NAME;
                    $matched_data["phone"] = $dr->PHONE_5;
                    $matched_data["fax"] = $dr->FAX_5;
                    $matched_data["cpso"] = $dr->CPSO;
                    $matched_data["address"] = $dr->ADDRESS_5;
                    log_message("error", "matched in fifth");
                    // $this->session->set_userdata("physician_match_id", $dr->ID);
                    break;
                }
            }
            //If a doctor match found - show match first name, last name with CPSO (confirmation popup)
            if ($matched) {
                return array(
                    "result" => "success",
                    "data" => $matched_data
                );
            }
            // If a doctor match found - show match first name, last name w/ CPSO (confirmation popup)
            // For match - if extraction fax same as cpso fax  --> that is sender fax #
            // For match - If extraction match not same as cpso or if cpso fax empty --> use extracted/enetered fax as sender fax #
            // If no fax entered/extracted, and exists in CPSO --> use CPSO as sender fax #
            // If no doctor match found --> proceed without match (popup with message)
            else {
                return array(
                    "result" => "error",
                    "msg" => "Physician match not found using entered details"
                );
            }
        } else
            return validation_errors();
    }

    public function check_patient_data_model() {
        // $this->form_validation->set_rules('id', 'Efax Id', 'required');
        // if ($this->form_validation->run()) {
        log_message("error", "===========================> check_patient_data_model checking");
        $data = $this->input->post();
        $match_found = false;
        $match_ids = array();
        $match_id = null;
        if (isset($data["pat_ohip"])) {
            //check OHIP matches
            log_message("error", "ohip checking");
            $ohip = $data["pat_ohip"];
            $ohip = str_replace(" ", "", str_replace("-", "", $ohip));
            $ohip_numbers = substr($ohip, 0, 10); //DDDD*DDD*DDD
            $this->db->select("pat.id, pat.fname, pat.lname, pat.dob, pat.ohip")->from("referral_patient_info pat, clinic_referrals c_ref, efax_info efax")->where(array(
                "pat.active" => 1,
                "left(pat.ohip, 10) = " => $ohip_numbers,
                "pat.ohip <>" => "",
                "c_ref.active" => 1,
                "efax.active" => 1,
                "efax.to" => $this->session->userdata("user_id")
            ));
            $this->db->where("pat.referral_id", "c_ref.id", false);
            $this->db->where("c_ref.efax_id", "efax.id", false);
            $matched_ohip = $this->db->get()->result();

            log_message("error", "search = " . $this->db->last_query());

            if ($matched_ohip) {
                log_message("error", "found ohip match");
                $match_found = true;
                // $match_ids[] = array(
                //     "id" => $matched_ohip[0]->id,
                //     "name" => $matched_ohip[0]->fname . " " . $matched_ohip[0]->lname,
                //     "dob" => $matched_ohip[0]->dob,
                //     "ohip" => $matched_ohip[0]->ohip
                // );
                foreach ($matched_ohip as $key => $value) {
                    $match_ids[] = array(
                        "id" => md5($value->id),
                        "name" => $value->fname . " " . $value->lname,
                        "dob" => $value->dob,
                        "ohip" => $value->ohip
                    );
                }
            }
            log_message("error", "checking for dob => " . $match_found);
            log_message("error", "checking for pat_dob_year => " . isset($data["pat_dob_year"]));
            log_message("error", "checking for pat_dob_month => " . isset($data["pat_dob_month"]));
            log_message("error", "checking for pat_dob_day => " . isset($data["pat_dob_day"]));
            log_message("error", "checking for pat_lname => " . isset($data["pat_lname"]));
        }
        if (!$match_found && isset($data["pat_dob_year"]) && isset($data["pat_dob_month"]) && isset($data["pat_dob_day"]) && isset($data["pat_lname"])) {
            //check last name and DOB matches
            $dob = $data["pat_dob_year"] . "-" . $data["pat_dob_month"] . "-" . $data["pat_dob_day"];
            $last_name = $data["pat_lname"];
            log_message("error", "checking dob and lname");

            $this->db->select("pat.id, pat.fname, pat.lname, pat.dob, pat.ohip")->from("referral_patient_info pat, clinic_referrals c_ref, efax_info efax")->where(array(
                "pat.active" => 1,
                "c_ref.active" => 1,
                "efax.active" => 1,
                "efax.to" => $this->session->userdata("user_id")
            ))->like("dob", $dob)->like("lname", $last_name);
            $this->db->where("pat.referral_id", "c_ref.id", false);
            $this->db->where("c_ref.efax_id", "efax.id", false);

            $matched_dob_last_name = $this->db->get()->result();
            log_message("error", "result " . $this->db->last_query());

            if ($matched_dob_last_name) {
                log_message("error", "found matched_dob_last_name match");
                $match_found = true;
                foreach ($matched_dob_last_name as $key => $value) {
                    $match_ids[] = array(
                        "id" => md5($value->id),
                        "name" => $value->fname . " " . $value->lname,
                        "dob" => $value->dob,
                        "ohip" => $value->ohip
                    );
                }
            }
        }

        if ($match_found) {
            return array(
                "result" => "success",
                "data" => json_encode($match_ids)
            );
        } else {
            return array(
                "result" => "error",
                "msg" => "Patient match not found. Please try again"
            );
        }
    }

    public function new_referral_model() {
        log_message("error", "=========================================");
        log_message("error", "Tracking New Referral ");
        log_message("error", "=========================================");
        $this->form_validation->set_rules('id', 'Efax Id', 'required');
        // $this->form_validation->set_rules('diagnosis', 'Diagnosis', 'required');
        // $this->form_validation->set_rules('referral_reason', 'Reason for Referral', 'required');

        $this->form_validation->set_rules('pat_fname', 'Patient First Name', 'required');
        $this->form_validation->set_rules('pat_lname', 'Patient Last Name', 'required');
//        $this->form_validation->set_rules('pat_dob_day', 'Day - Date of Birth', 'required');
//        $this->form_validation->set_rules('pat_dob_month', 'Month - Date of Birth', 'required');
//        $this->form_validation->set_rules('pat_dob_year', 'Year - Date of Birth', 'required');
        $this->form_validation->set_rules('pat_email', 'Patient Email', 'valid_email');
//        $this->form_validation->set_rules('dr_fname', 'Physician First Name', 'required');
//        $this->form_validation->set_rules('dr_lname', 'Physician Last Name', 'required');
//        $this->form_validation->set_rules('priority', 'Priority', 'required');
        // $this->form_validation->set_rules('dr_fax', 'Physician Fax', 'required');
        $this->form_validation->set_rules('dr_email', 'Physician Email', 'valid_email');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            //check if referral already created for this fax. 
//            $result = $this->db->select("c_ref.id")
//                            ->from("clinic_referrals c_ref, efax_info efax")
//                            ->where(array(
//                                "efax.active" => 1,
//                                "c_ref.active" => 1,
//                                "md5(efax.id)" => $data["id"]
//                            ))->where("c_ref.efax_id", "efax.id", false)->get()->result();
//            if ($result) {
//                return array(false, "Referral already created for this fax");
//            }

            //check efax authenticity
            $this->db->select("id, file_name, to");
            $this->db->from("efax_info");
            $this->db->where(array(
                "md5(id)" => $data["id"],
                "to" => $this->session->userdata("user_id")
            ));
            $result = $this->db->get()->result();
            if ($result) {
                try {
                    $referral_code = $this->generate_referral_code();
                    log_message("error", "ref code = " . $referral_code);
                    $this->db->trans_start();
                    //add referral
                    $efax_id = $result[0]->id;
                    $efax_file = $result[0]->file_name;
                    $referral_reason = (isset($data["reasons"])) ? $data["reasons"][0] : "";
                    // $first_status = "Admin Triage";
                    $first_status = "Referral Triage";
                    $this->db->set("last_updated", "now()", false);

                    $insert_data = array(
                        "efax_id" => $efax_id,
                        "referral_code" => $referral_code,
                        "referral_reason" => $referral_reason,
                        "status" => $first_status
                    );
                    //If clinic has only 1 physician account, then assign by default 
                    $physicians = $this->db->select("id")
                                    ->from("clinic_physician_info")
                                    ->where(array(
                                        "clinic_id" => $this->session->userdata("user_id")
                                    ))->get()->result();
                    if ($physicians && sizeof($physicians) === 1) {
                        $insert_data["assigned_physician"] = $physicians[0]->id;
                    }
                    $this->db->insert("clinic_referrals", $insert_data);
                    //new referral record added


                    log_message("error", "update status  = " . $this->db->last_query());
                    $referral_id = $this->db->insert_id();
                    //remove from inbox by status referred true
                    $this->db->where(array(
                        "md5(id)" => $data["id"]
                    ));
                    $this->db->update("efax_info", array(
                        "referred" => TRUE
                    ));
                    log_message("error", "update referred efax = " . $this->db->last_query());

                    $ohip = $data["pat_ohip"];
                    //store patient details
                    $patient_data = array(
                        "referral_id" => $referral_id,
                        "fname" => $data["pat_fname"],
                        "lname" => $data["pat_lname"],
                        "dob" => $data["pat_dob_year"] . "-" . $data["pat_dob_month"] . "-" . $data["pat_dob_day"],
                        "ohip" => str_replace(" ", "", str_replace("-", "", $ohip)),
                        "gender" => $data["pat_gender"],
                        "cell_phone" => $data["pat_cell_phone"],
                        "home_phone" => $data["pat_home_phone"],
                        "work_phone" => $data["pat_work_phone"],
                        "email_id" => $data["pat_email"],
                        "address" => $data["pat_address"]
                    );
                    $this->db->insert("referral_patient_info", $patient_data);
                    $patient_id = $this->db->insert_id();
                    log_message("error", "insert patient = " . $this->db->last_query());

                    $data["dr_fax"] = preg_replace("/[^0-9]/", "", $data["dr_fax"]);
                    log_message("error", "dr_fax trimmed = " . $data["dr_fax"]);
                    //store referring physician data linked to patient id
                    $physician_data = array(
                        "patient_id" => $patient_id,
                        "fname" => $data["dr_fname"],
                        "lname" => $data["dr_lname"],
                        "phone" => $data["dr_phone_number"],
                        "fax" => $data["dr_fax"],
                        "email" => $data["dr_email"],
                        "address" => $data["dr_address"],
                        "billing_num" => $data["dr_billing_num"]
                    );
                    $this->db->insert("referral_physician_info", $physician_data);
                    log_message("error", "insert physician  = " . $this->db->last_query());

                    //store clinical triage info linked to patient id
                    $clinical_triage_data = array(
                        "patient_id" => $patient_id,
                        "priority" => (!isset($data["priority"]) ||
                        $data["priority"] == null || empty($data["priority"])) ?
                        "not_specified" : $data["priority"]
                    );
                    $this->db->insert("referral_clinic_triage", $clinical_triage_data);
                    $clinic_triage_id = $this->db->insert_id();
                    log_message("error", "triage referral = " . $this->db->last_query());

                    //store all diseases (using loop) patient diseases linked to referral_clinic_triage->id
                    if (isset($data["diseases"])) {
                        $diseases = $data["diseases"];
                        foreach ($diseases as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_disease_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "disease" => $value
                                ));
                                log_message("error", "disease q = " . $this->db->last_query());
                            }
                        }
                    }

                    //store all symptoms (using loop) patient symptoms linked to referral_clinic_triage->id
                    if (isset($data["symptoms"])) {
                        $symptoms = $data["symptoms"];
                        foreach ($symptoms as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_symptom_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "symptom" => $value
                                ));
                                log_message("error", "symptom q = " . $this->db->last_query());
                            }
                        }
                    }

                    //store all lab tests (using loop) patient tests linked to referral_clinic_triage->id
                    if (isset($data["tests"])) {
                        $tests = $data["tests"];
                        foreach ($tests as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_tests_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "test" => $value
                                ));
                                log_message("error", "test q = " . $this->db->last_query());
                            }
                        }
                    }

                    //store all medications (using loop) patient tests linked to referral_clinic_triage->id
                    if (isset($data["medications"])) {
                        $drugs = $data["medications"];
                        foreach ($drugs as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_drugs_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "drug" => $value
                                ));
                                log_message("error", "drug q = " . $this->db->last_query());
                            }
                        }
                    }

                    //store all procedure and devices (using loop) patient tests linked to referral_clinic_triage->id
                    if (isset($data["devices"])) {
                        $devices = $data["devices"];
                        foreach ($devices as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_devices_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "device" => $value
                                ));
                                log_message("error", "device q = " . $this->db->last_query());
                            }
                        }
                    }
                    $referral_checklist = array();
                    //insert referral checklist
                    if (isset($data["referral_checklist"])) {
                        $referral_checklist = $data["referral_checklist"];
                    } else {
                        $referral_checklist = array();
                    }

                    log_message("error", "checklist array = " . json_encode($referral_checklist));
                    //insert default checklist info
                    $this->db->select("md5(id) as id, id as plain_id");
                    $this->db->from("clinic_referral_checklist_items");
                    $this->db->where(array(
                        "active" => 1,
                        "clinic_id" => $this->session->userdata("user_id")
                    ));
                    $default_checklist = $this->db->get()->result();
                    log_message("error", "checklist query = " . $this->db->last_query());
                    log_message("error", "default checklist = " . json_encode($default_checklist));

                    foreach ($default_checklist as $key => $value) {
                        $exist = array_search($value->id, $referral_checklist);
                        $checked = ($exist === false) ? "false" : "true";
                        // log_message("error", "val = " . $value->id . " and ref = " . json_encode($referral_checklist));
                        $check_type = "stored";
                        $this->db->insert("referral_checklist", array(
                            "patient_id" => $patient_id,
                            "checklist_type" => $check_type,
                            "checklist_id" => $value->plain_id,
                            "attached" => $checked
                        ));
                        log_message("error", "insert for default = " . $this->db->last_query());
                    }

                    //insert new checlist info
                    log_message("error", "at custome checklist");
                    $new_checklist = explode(",", $data["new_checklists"]);
                    foreach ($new_checklist as $key => $value) {
                        if ($value == "")
                            continue;
                        $exist = array_search($value, $referral_checklist);
                        // log_message("error", "val = " . $value . " and ref = " . json_encode($referral_checklist));
                        $checked = ($exist === false) ? "false" : "true";
                        $check_type = "typed";
                        $this->db->insert("referral_checklist", array(
                            "patient_id" => $patient_id,
                            "checklist_type" => $check_type,
                            "checklist_name" => $value,
                            "attached" => $checked
                        ));
                        log_message("error", "insert custom = " . $this->db->last_query());
                    }


                    //create default clinical note
                    $clinic_id = $result[0]->to;
                    $source_dir = "./uploads/efax/";
                    $file_old_name = $efax_file . ".pdf";
                    $clinic_dir = "./uploads/clinics";
                    if (!file_exists($clinic_dir)) {
                        mkdir($clinic_dir);
                    }
                    $clinic_dir = files_dir() . "" . md5($clinic_id);
                    if (!file_exists($clinic_dir)) {
                        mkdir($clinic_dir);
                    }
                    $patient_dir = $clinic_dir . "/" . md5($patient_id);
                    if (!file_exists($patient_dir)) {
                        mkdir($patient_dir);
                    }
                    $target_dir = $patient_dir . "/";
                    $file_new_name = $this->generate_random_string(32);
                    rename($source_dir . $file_old_name, $target_dir . $file_new_name . ".pdf");

//                    $target_dir = files_dir() . "".md5($clinic_id)."/health_records/";
//                    $file_new_name = $this->generate_random_string(32);
//                    rename($source_dir . $file_old_name, $target_dir . $file_new_name . ".pdf");
                    $this->db->insert("records_clinic_notes", array(
                        "efax_id" => $efax_id,
                        "patient_id" => $patient_id,
                        "record_type" => "Referral Letter",
                        "physician" => $this->session->userdata("physician_name"),
                        "description" => "Faxed referral package",
                        "record_file" => $file_new_name
                    ));
                    log_message("error", "file transfered from " . $source_dir . $file_old_name . " to " . $target_dir . $file_new_name . ".pdf");

                    //send referral code as fax to family physician
                    // $subject = "Referral code = " . $referral_code;
                    //                $this->send_efax("15554567890", $subject);  
                    // $this->send_efax($data["dr_fax"], $subject);
                    log_message("error", "referral id => '$referral_id' => " . "admin_triage/referral_details/" . md5($referral_id));
                    log_message("error", "=========================================");
                    log_message("error", "=====   Referral created            ======");
                    log_message("error", "=========================================");
                    log_message("error", "=========================================");

                    $this->db->select("c_usr.clinic_institution_name, "
                            . "date_format(c_ref.create_datetime, '%M %D') as referral_received, dr.fax");
                    $this->db->from("clinic_user_info c_usr, efax_info efax, "
                            . "clinic_referrals c_ref, referral_physician_info dr");
                    $this->db->where(array(
                        "efax.id" => $efax_id,
                        "efax.active" => 1,
                        "c_usr.active" => 1,
                        "c_ref.active" => 1,
                        "dr.active" => 1,
                        "dr.patient_id" => $patient_id
                    ));
                    $this->db->where("efax.to", "c_usr.id", false);
                    $this->db->where("efax.id", "c_ref.efax_id", false);
                    $result = $this->db->get()->result()[0];

                    $this->db->select("if( ref_c.checklist_type = 'stored', "
                            . "c_items.name , ref_c.checklist_name) as 'doc_name'");
                    $this->db->from("referral_checklist ref_c");
                    $this->db->join("clinic_referral_checklist_items c_items", "c_items.id = ref_c.checklist_id and c_items.active=1", "left");
                    $this->db->where(array(
                        "ref_c.active" => 1,
                        "ref_c.attached" => "false",
                        "md5(ref_c.patient_id)" => $patient_id
                    ));
                    $checklist = $this->db->get()->result();

                    $file_name = "referral_received.html";
                    $replace_stack = array(
                        "###clinic_name###" => $result->clinic_institution_name,
                        "###referral_code###" => $referral_code,
                        "###time1###" => $result->referral_received,
                        "###time2###" => ""
                    );
                    $fax_number = $result->fax;

                    log_message("error", "sending fax");
                    $this->load->model("referral_model");
                    log_message("error", "$file_name, checklist, replace, $fax_number");
                    $response = $this->referral_model->send_status_fax($file_name, $checklist, $replace_stack, $fax_number, "New Referral");

                    log_message("error", "completed fax send");



                    //only trigger RPA events (table entry + doc upload API) if pathway name is AccuroCitrix
                    //
                    
                    $clinic = $this->db->select("first_name, integration_type, "
                                            . "emr_pathway, emr_uname_1, emr_pwd_1")
                                    ->from("clinic_user_info")
                                    ->where("id", $this->session->userdata("user_id"))
                                    ->get()->result();
                    log_message("error", "only trigger RPA events = > " . $this->db->last_query());

                    if ($clinic) {
                        $clinic = $clinic[0];
                        if ($clinic->emr_pathway === "OscarEMR") {
                            //save to json file for API integration
                            $data_object = array(
                                "patient_id" => md5($patient_id),
                                "api_type" => "new referral",
                                "api_num" => 2,
                                "date" => date("Y-m-d"),
                                "time" => date("H:i:s"),
                                "status" => "NEW",
                                "first_name" => $data["pat_fname"],
                                "last_name" => $data["pat_lname"],
                                "dob_day" => make_two_digit($data["pat_dob_day"]),
                                "dob_month" => make_two_digit($data["pat_dob_month"]),
                                "dob_year" => $data["pat_dob_year"],
                                "hin" => $ohip,
                                "email_id" => $data["pat_email"],
                                "cell_phone" => $data["pat_cell_phone"],
                                "home_phone" => $data["pat_home_phone"],
                                "work_phone" => $data["pat_work_phone"],
                                "address" => $data["pat_address"],
                                "pdf_location" => base_url() . "uploads/clinics/" .
                                md5($clinic_id) . "/" . md5($patient_id) . "/" . $file_new_name . ".pdf",
                                "pdf_name" => "$file_new_name.pdf",
                                "pdf_type" => "Documents",
                                "active" => 1
                            );
                            save_json($this->session->userdata("user_id"), $data_object);
                        }
                        if ($clinic->emr_pathway === "AccuroCitrix") {
                            //save entry in rpa_integration table
                            $rpa_data = array(
                                "api_type" => "new referral",
                                "api_num" => 2,
                                "date" => date("Y-m-d"),
                                "time" => date("H:i:s"),
                                "status" => "NEW",
                                "fk_id" => $referral_id,
                                "pathway" => "AccuroCitrix",
                                "clinic_name" => "TCN",
                                "username" => "hahmed",
                                "password" => "Blockhealth19",
                                "first_name" => $data["pat_fname"],
                                "last_name" => $data["pat_lname"],
                                "dob_day" => make_two_digit($data["pat_dob_day"]),
                                "dob_month" => make_two_digit($data["pat_dob_month"]),
                                "dob_year" => $data["pat_dob_year"],
                                "hin" => $ohip,
                                "email_id" => $data["pat_email"],
                                "cell_phone" => $data["pat_cell_phone"],
                                "home_phone" => $data["pat_home_phone"],
                                "work_phone" => $data["pat_work_phone"],
                                "address" => $data["pat_address"],
                                "pdf_location" => base_url() . "uploads/clinics/" .
                                md5($clinic_id) . "/" . md5($patient_id) . "/" . $file_new_name . ".pdf",
                                "pdf_name" => "$file_new_name.pdf",
                                "pdf_type" => "Documents",
                                "assigned_provider" => "Arianna Muskat",
                                "rp_first_name" => $data["dr_fname"],
                                "rp_last_name" => $data["dr_lname"],
                                "rp_number" => $data["dr_billing_num"],
                                "active" => 1
                            );

                            $drugs = $data["medications"];
                            foreach ($drugs as $key => $value) {
                                $pos = strpos($value, ",");
                                $value = ($pos) ? substr($value, 0, $pos) . ")" : $value;
                                $rpa_data["medication" . ($key + 1)] = $value;
                            }

                            $this->db->insert("rpa_integration", $rpa_data);

                            log_message("error", "inserted to rpa");
                            log_message("error", $this->db->last_query());


                            $request = curl_init('http://52.237.12.245/api/v1/patients/upload-documents');
                            curl_setopt($request, CURLOPT_POST, true);
                            curl_setopt($request, CURLOPT_POSTFIELDS, array(
                                "pathwayName" => "AccuroCitrix",
                                "username" => "hahmed",
                                "password" => "Blockhealth19",
                                "ClinicName" => "TCN",
                                "source" => "remote",
                                "PDFName" => "$file_new_name.pdf",
                                "PDFRemote" => base_url() . "uploads/clinics/" .
                                md5($clinic_id) . "/" . md5($patient_id) . "/" . $file_new_name . ".pdf"
                            ));

                            curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($request);
                            curl_close($request);

                            log_message("error", "curl log = " . json_encode($response));
                        }
                    }
                    $this->db->trans_complete();
                    log_message("error", "transactions saved");

                    // return array(true, base_url() . "admin_triage/referral_details/" . md5($referral_id));
                    return array(true, base_url() . "referral_triage/referral_details/" . md5($patient_id));
                } catch (Exception $exception) {
                    return array(false, "SQL Exception occured");
                }
            } else {
                return array(false, "Unauthorized Attempt");
            }
        } else
            return array(false, validation_errors());
    }

    public function save_referral_model() {
        $this->form_validation->set_rules('id', 'Efax', 'required');
        $this->form_validation->set_rules('target', 'Patient', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            //check if referral and patient belongs to that clinic
            $efax = $data["id"];
            $patient = $data["target"];
            $sql = "select * from efax_info where md5(id) = '" . $efax . "'";
            $count_efax = $this->db->query($sql)->num_rows();
            $sql = "select * from clinic_patient_info where md5(id) = '" . $patient . "'";
            $count_patient = $this->db->query($sql)->num_rows();
            if ($count_efax == 1 && $count_patient == 1) {
                $result = $this->db->insert("clinic_referrals", array(
                    "efax_id" => $efax,
                    "patient_id" => $patient,
                    "status" => "Admin Triage"
                        )
                );
                return $result;
            } else
                return "Unauthorized Attempt";
        } else
            return validation_errors();
    }

    public function add_health_record_model() {
        $this->form_validation->set_rules('id', 'Efax Id', 'required');
        $this->form_validation->set_rules('target', 'Patient Id', 'required');
        $this->form_validation->set_rules('record_type', 'Select Record', 'required');
        $this->form_validation->set_rules('description', 'Description', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $this->db->trans_start();
            //add efax as health record
            $efax_id = $this->get_decrypted_id($data["id"], "efax_info");
            $patient_id = $this->get_decrypted_id($data["target"], "referral_patient_info");
            $this->db->select("file_name");
            $this->db->from("efax_info");
            $this->db->where(
                    array(
                        "active" => 1,
                        "id" => $efax_id
                    )
            );
            $result = $this->db->get()->result();
            if ($result) {
                //move file efax to health record for selected patient
                $source_dir = "./uploads/efax/";
                $file_old_name = $result[0]->file_name;
                $target_dir = "./uploads/health_records/";
                $file_new_name = $this->generate_random_string(32);
                rename($source_dir . $file_old_name, $target_dir . $file_new_name);
                //insert in db
                $this->db->insert("records_clinic_notes", array(
                    "efax_id" => $efax_id,
                    "patient_id" => $patient_id,
                    "record_type" => $data["record_type"],
                    "description" => $data["description"],
                    "record_file" => $file_new_name
                ));
                //set referred status true for efax
                $this->db->where(array(
                    "id" => $efax_id,
                    "active" => 1
                ));
                $this->db->update("efax_info", array(
                    "referred" => true
                        )
                );
                $this->db->trans_complete();
                return true;
            } else
                return "Efax not found.";
        } else
            return validation_errors();
    }

    public function get_referral_checklist_model() {
        $this->db->select("md5(id) as id, name");
        $this->db->from("clinic_referral_checklist_items");
        $this->db->where(array(
            "clinic_id" => $this->session->userdata("user_id")
        ));
        return $this->db->get()->result();
    }

    public function get_physician_list_save_patient_model() {
        $this->db->select("md5(dr.id) as id, concat(dr.first_name, ' ', dr.last_name) as name");
        $this->db->from("clinic_physician_info dr");
        $this->db->where(array(
            "clinic_id" => $this->session->userdata("user_id"),
            "dr.active" => 1
        ));
        return $this->db->get()->result();
    }

    public function get_patient_list_save_patient_model() {
        $this->db->select("concat(pat.fname, ' ', pat.lname) as name, md5(pat.id) as id");
        $this->db->from("clinic_referrals c_ref, referral_patient_info pat, "
                . "efax_info efax, clinic_user_info c_usr");
        $this->db->where(array(
            "c_ref.active" => 1,
            "pat.active" => 1,
            "efax.active" => 1,
            "c_usr.active" => 1,
            "c_ref.status" => "Referral Triage",
            "c_usr.id" => $this->session->userdata("user_id")
        ));
        $this->db->where("c_ref.id", "pat.referral_id", false);
        $this->db->where("efax.id", "c_ref.efax_id", false);
        $this->db->where("c_usr.id", "efax.to", false);
        
        log_message("error", "get patient list = > " . $this->db->last_query());
        return $this->db->get()->result();
    }

    public function save_data_points_predict_model() {
        $data = $this->input->post();
        $this->db->insert("data_points_predict", $data);
    }

    public function save_data_points_drug_model() {
        $data = $this->input->post();
        if (isset($data["disease_words"])) {
            $diseases = json_decode($data["disease_words"]);
            if (sizeof($diseases) > 0) {
                $this->db->insert_batch("data_points_drug_diseases", $diseases);
                log_message("error", $this->db->last_query());
            } else {
                log_message("error", "No disease data found to insert_btach");
            }
        }
        if (isset($data["sign_and_synd_words"])) {
            $sign_and_synd_words = json_decode($data["sign_and_synd_words"]);
            if (sizeof($sign_and_synd_words) > 0) {
                $this->db->insert_batch("data_points_drug_sign", $sign_and_synd_words);
                log_message("error", $this->db->last_query());
            } else {
                log_message("error", "No sign_and_synd_words data found to insert_btach");
            }
        }
        if (isset($data["devices_and_procedures"])) {
            $devices_and_procedures = json_decode($data["devices_and_procedures"]);
            if (sizeof($devices_and_procedures) > 0) {
                $this->db->insert_batch("data_points_drug_devices", $devices_and_procedures);
                log_message("error", $this->db->last_query());
            } else {
                log_message("error", "No devices_and_procedures data found to insert_btach");
            }
        }
        if (isset($data["pharmacologic_substance"])) {
            $pharmacologic_substance = json_decode($data["pharmacologic_substance"]);
            if (sizeof($pharmacologic_substance) > 0) {
                $this->db->insert_batch("data_points_drug_medications", $pharmacologic_substance);
                log_message("error", $this->db->last_query());
            } else {
                log_message("error", "No pharmacologic_substance data found to insert_btach");
            }
        }
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

    private function send_efax($fax_number, $subject = "Referral code for physician", $comment = "Blockhealth Fax") {
        if (strlen($fax_number) == 10) {
            $fax_number = "1" . $fax_number;
        }
        log_message("error", "SrFax => sending subject = " . $subject . " to f.physician fax" . $fax_number);

        $this->db->select("srfax_number, srfax_email, srfax_pass, srfax_account_num");
        $this->db->from("clinic_user_info");
        $this->db->where(array(
            "active" => 1,
            "id" => $this->session->userdata("user_id")
        ));
        $result = $this->db->get()->result();
        //dynamic
        $access_id = $result[0]->srfax_account_num;
        $access_pwd = $result[0]->srfax_pass;
        $caller_id = $result[0]->srfax_number;
        $sender_mail = $result[0]->srfax_email;

        $postVariables = array(
            "action" => "Queue_Fax",
            "access_id" => $access_id,
            "access_pwd" => $access_pwd,
            "sCallerID" => $caller_id,
            "sSenderEmail" => $sender_mail,
            "sFaxType" => "SINGLE",
            "sToFaxNumber" => $fax_number,
            "sCoverPage" => "Basic",
            "sCPSubject" => $subject,
            "sCPComments" => $comment,
//            "sFileName_1" => "MyFax.txt",
//            "sFileContent_1" => base64_encode(file_get_contents("Files/MyFax.txt")),
        );
        $curlDefaults = array(
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_URL => "https://www.srfax.com/SRF_SecWebSvc.php",
            CURLOPT_FRESH_CONNECT => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_POSTFIELDS => http_build_query($postVariables),
        );
        $ch = curl_init();
        curl_setopt_array($ch, $curlDefaults);
        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            log_message("error", "Error – " . json_encode(curl_error($ch)));
            return array("error", curl_error($ch));
        } else {
            log_message("error", "Result:" . json_encode($result));
            return array("success", $result);
        }
    }

    private function generate_referral_code() {
        log_message("error", "generate_referral_code called");
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $charactersLength = strlen($characters);
        $referral_code = '';
        $repeat = TRUE;
        $length = 7; // length of referral code size
        while ($repeat) {
            log_message("error", "repeate inside");
            $referral_code = '';
            for ($i = 0; $i < $length; $i++) {
                $referral_code .= $characters[rand(0, $charactersLength - 1)];
            }
            log_message("error", "code = " . $referral_code);
            //check in db if such referral code already exist
            $this->db->select("count(id) as repeat_count");
            $this->db->from("clinic_referrals");
            $this->db->where(array(
                "referral_code" => $referral_code
            ));
            $result = $this->db->get()->result();
            log_message("error", "res = " . json_encode($result));
            if ($result[0]->repeat_count == 0) {
                //no duplicate code found
                $repeat = false;
            }
        }
        return $referral_code;
    }

    public function missing_items_details_model() {
        $data = $this->input->post();
        $dr_name = $data["dr_fname"] . " " . $data["dr_lname"];
        $alert_data = "Are you sure you would like to send a missing item request to " . $dr_name;

        //return result data 
        return array(
            "result" => "success",
            "data" => $alert_data
        );
    }

    public function request_missing_items_model() {
        $this->form_validation->set_rules('dr_fax', 'Physician Fax Number', 'required|min_length[10]|numeric');

        if ($this->form_validation->run()) {
            $data = $this->input->post();
            //send fax to request missing items
            //Send fax in following format, with clinic name, patient name, missing item list, and referral code dynamically added
            //check efax authenticity
            $this->db->select("id, file_name, to");
            $this->db->from("efax_info");
            $this->db->where(array(
                "md5(id)" => $data["id"],
                "to" => $this->session->userdata("user_id")
            ));
            $result = $this->db->get()->result();

            if ($result) {
                try {
                    $referral_code = $this->generate_referral_code();
                    log_message("error", "ref code = " . $referral_code);
                    $this->db->trans_start();
                    //add referral
                    $efax_id = $result[0]->id;
                    $efax_file = $result[0]->file_name;
                    $referral_reason = (isset($data["reasons"])) ? $data["reasons"][0] : "";
                    // $first_status = "Admin Triage";
                    $first_status = "Referral Triage";

                    $insert_data = array(
                        "efax_id" => $efax_id,
                        "referral_code" => $referral_code,
                        "referral_reason" => $referral_reason,
                        "status" => $first_status
                    );

                    //If clinic has only 1 physician account, then assign by default 
                    $physicians = $this->db->select("id")
                                    ->from("clinic_physician_info")
                                    ->where(array(
                                        "clinic_id" => $this->session->userdata("user_id")
                                    ))->get()->result();
                    if ($physicians && sizeof($physicians) === 1) {
                        $insert_data["assigned_physician"] = $physicians[0]->id;
                    }
                    $this->db->insert("clinic_referrals", $insert_data);

                    //new referral record added
                    log_message("error", "update status  = " . $this->db->last_query());
                    $referral_id = $this->db->insert_id();
                    $ohip = $data["pat_ohip"];
                    //store patient details
                    $patient_data = array(
                        "referral_id" => $referral_id,
                        "fname" => $data["pat_fname"],
                        "lname" => $data["pat_lname"],
                        "dob" => $data["pat_dob_year"] . "-" . $data["pat_dob_month"] . "-" . $data["pat_dob_day"],
                        "ohip" => str_replace(" ", "", str_replace("-", "", $ohip)),
                        "gender" => $data["pat_gender"],
                        "cell_phone" => $data["pat_cell_phone"],
                        "home_phone" => $data["pat_home_phone"],
                        "work_phone" => $data["pat_work_phone"],
                        "email_id" => $data["pat_email"],
                        "address" => $data["pat_address"]
                    );
                    $this->db->insert("referral_patient_info", $patient_data);
                    $patient_id = $this->db->insert_id();
                    log_message("error", "insert patient = " . $this->db->last_query());

                    $data["dr_fax"] = preg_replace("/[^0-9]/", "", $data["dr_fax"]);
                    log_message("error", "dr_fax trimmed = " . $data["dr_fax"]);
                    //store referring physician data linked to patient id
                    $physician_data = array(
                        "patient_id" => $patient_id,
                        "fname" => $data["dr_fname"],
                        "lname" => $data["dr_lname"],
                        "phone" => $data["dr_phone_number"],
                        "fax" => $data["dr_fax"],
                        "email" => $data["dr_email"],
                        "address" => $data["dr_address"],
                        "billing_num" => $data["dr_billing_num"]
                    );
                    $this->db->insert("referral_physician_info", $physician_data);
                    log_message("error", "insert physician  = " . $this->db->last_query());

                    //store clinical triage info linked to patient id
                    $clinical_triage_data = array(
                        "patient_id" => $patient_id,
                        "priority" => (!isset($data["priority"]) ||
                        $data["priority"] == null || empty($data["priority"])) ?
                        "not_specified" : $data["priority"]
                    );
                    $this->db->insert("referral_clinic_triage", $clinical_triage_data);
                    $clinic_triage_id = $this->db->insert_id();
                    log_message("error", "triage referral = " . $this->db->last_query());

                    //store all diseases (using loop) patient diseases linked to referral_clinic_triage->id
                    if (isset($data["diseases"])) {
                        $diseases = $data["diseases"];
                        foreach ($diseases as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_disease_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "disease" => $value
                                ));
                                log_message("error", "disease q = " . $this->db->last_query());
                            }
                        }
                    }

                    //store all symptoms (using loop) patient symptoms linked to referral_clinic_triage->id
                    if (isset($data["symptoms"])) {
                        $symptoms = $data["symptoms"];
                        foreach ($symptoms as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_symptom_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "symptom" => $value
                                ));
                                log_message("error", "symptom q = " . $this->db->last_query());
                            }
                        }
                    }

                    //store all lab tests (using loop) patient tests linked to referral_clinic_triage->id
                    if (isset($data["tests"])) {
                        $tests = $data["tests"];
                        foreach ($tests as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_tests_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "test" => $value
                                ));
                                log_message("error", "test q = " . $this->db->last_query());
                            }
                        }
                    }

                    //store all medications (using loop) patient tests linked to referral_clinic_triage->id
                    if (isset($data["medications"])) {
                        $drugs = $data["medications"];
                        foreach ($drugs as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_drugs_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "drug" => $value
                                ));
                                log_message("error", "drug q = " . $this->db->last_query());
                            }
                        }
                    }

                    //store all procedure and devices (using loop) patient tests linked to referral_clinic_triage->id
                    if (isset($data["devices"])) {
                        $devices = $data["devices"];
                        foreach ($devices as $key => $value) {
                            if ($value != "") {
                                //insert if not empty
                                $this->db->insert("referral_clinic_triage_devices_info", array(
                                    "clinic_triage_id" => $clinic_triage_id,
                                    "device" => $value
                                ));
                                log_message("error", "device q = " . $this->db->last_query());
                            }
                        }
                    }
                    $referral_checklist = array();
                    //insert referral checklist
                    if (isset($data["referral_checklist"])) {
                        $referral_checklist = $data["referral_checklist"];
                    } else {
                        $referral_checklist = array();
                    }


                    log_message("error", "checklist array = " . json_encode($referral_checklist));
                    //insert default checklist info
                    $this->db->select("md5(id) as id, id as plain_id");
                    $this->db->from("clinic_referral_checklist_items");
                    $this->db->where(array(
                        "active" => 1,
                        "clinic_id" => $this->session->userdata("user_id")
                    ));
                    $default_checklist = $this->db->get()->result();
                    log_message("error", "checklist query = " . $this->db->last_query());
                    log_message("error", "default checklist = " . json_encode($default_checklist));

                    foreach ($default_checklist as $key => $value) {
                        $exist = array_search($value->id, $referral_checklist);
                        $checked = ($exist === false) ? "false" : "true";
                        // log_message("error", "val = " . $value->id . " and ref = " . json_encode($referral_checklist));
                        $check_type = "stored";
                        $this->db->insert("referral_checklist", array(
                            "patient_id" => $patient_id,
                            "checklist_type" => $check_type,
                            "checklist_id" => $value->plain_id,
                            "attached" => $checked
                        ));
                        log_message("error", "insert for default = " . $this->db->last_query());
                    }


                    //insert new checklist info
                    log_message("error", "at custome checklist");
                    $new_checklist = explode(",", $data["new_checklists"]);
                    foreach ($new_checklist as $key => $value) {
                        if ($value == "")
                            continue;
                        $exist = array_search($value, $referral_checklist);
                        // log_message("error", "val = " . $value . " and ref = " . json_encode($referral_checklist));
                        $checked = ($exist === false) ? "false" : "true";
                        $check_type = "typed";
                        $this->db->insert("referral_checklist", array(
                            "patient_id" => $patient_id,
                            "checklist_type" => $check_type,
                            "checklist_name" => $value,
                            "attached" => $checked
                        ));
                        log_message("error", "insert custom = " . $this->db->last_query());
                    }

                    //create default clinical note
                    $clinic_id = $result[0]->to;
                    $source_dir = "./uploads/efax/";
                    $file_old_name = $efax_file . ".pdf";
                    $clinic_dir = "./uploads/clinics";
                    if (!file_exists($clinic_dir)) {
                        mkdir($clinic_dir);
                    }
                    $clinic_dir = files_dir() . "" . md5($clinic_id);
                    if (!file_exists($clinic_dir)) {
                        mkdir($clinic_dir);
                    }
                    $patient_dir = $clinic_dir . "/" . md5($patient_id);
                    if (!file_exists($patient_dir)) {
                        mkdir($patient_dir);
                    }
                    $target_dir = $patient_dir . "/";
                    $file_new_name = $this->generate_random_string(32);
                    //copy instead of rename
                    copy($source_dir . $file_old_name, $target_dir . $file_new_name . ".pdf");

                    $this->db->insert("records_clinic_notes", array(
                        "efax_id" => $efax_id,
                        "patient_id" => $patient_id,
                        "record_type" => "Referral Letter",
                        "physician" => $this->session->userdata("physician_name"),
                        "description" => "Faxed referral package",
                        "record_file" => $file_new_name
                    ));
                    log_message("error", "file copied from " . $source_dir . $file_old_name . " to " . $target_dir . $file_new_name . ".pdf");



                    // now send fax for request missing item

                    $checklist = array();
                    foreach ($data["missing_item"] as $key => $value) {
                        $checklist[] = array(
                            "doc_name" => $value
                        );
                    }
                    log_message("error", "checklist prepared = " . json_encode($checklist));

                    $this->db->select("c_usr.clinic_institution_name, c_usr.srfax_number");
                    $this->db->from("clinic_user_info c_usr");
                    $this->db->where(array(
                        "c_usr.active" => 1,
                        "c_usr.id" => $this->session->userdata("user_id")
                    ));
                    $info = $this->db->get()->result();

                    $file_name = "referral_missing_from_inbox.html";
                    $srfax_number = $info[0]->srfax_number;
                    log_message("error", "srfax = " . $srfax_number);
                    if (strlen($srfax_number) === 10) {
                        $srfax_number = substr($srfax_number, 0, 3) . "-" .
                                substr($srfax_number, 3, 3) . "-" . substr($srfax_number, 6, 4);
                        log_message("error", " 10 = srfax = " . $srfax_number);
                    } else if (strlen($srfax_number) === 11) {
                        $srfax_number = substr($srfax_number, 0, 1) . "-" . substr($srfax_number, 1, 3) . "-" .
                                substr($srfax_number, 4, 3) . "-" . substr($srfax_number, 7, 4);
                        log_message("error", " 11 = srfax = " . $srfax_number);
                    }
                    $pat_dob = "";
                    if (!empty($data["pat_dob_day"]) && !empty($data["pat_dob_month"]) && !empty($data["pat_dob_year"])) {
                        $pat_dob = "({$data["pat_dob_month"]}-{$data["pat_dob_day"]}-{$data["pat_dob_year"]})";
                    }

                    $replace_stack = array(
                        "###clinic_name###" => $info[0]->clinic_institution_name,
                        "###pat_fname###" => $data["pat_fname"],
                        "###pat_lname###" => $data["pat_lname"],
                        "###pat_dob###" => $pat_dob,
                        "###fax_number###" => $srfax_number,
                        "###time1###" => "",
                        "###time2###" => ""
                    );

                    $text2 = "<h2>Referral is incomplete</h2>";
                    $additional_replace = array(
                        "###text2###" => $text2
                    );

                    $fax_number = $data["dr_fax"];
                    $this->load->model("referral_model");
                    $response = $this->referral_model->send_status_fax2($file_name, $checklist, $replace_stack, $fax_number, "Request Missing Items", $additional_replace);
                    log_message("error", "file sent successfully");

                    //store missing item request
                    $result = $this->db->insert("referral_missing_item_request_info", array(
                        "patient_id" => 0,
                        "requested_to" => 0
                    ));

                    $this->db->trans_complete();
                } catch (Exception $exception) {
                    return array(false, "SQL Exception occured");
                }
            } else {
                return array(false, "Unauthorized Attempt");
            }



            if ($result) {
                return true;
                // return array(
                //  "sender fax" => $info[0]->fax,
                //  "referral_code" => $info[0]->referral_code
                // );
            } else {
                return "Operation not completed";
            }
        } else {
            return validation_errors();
        }
    }

}
