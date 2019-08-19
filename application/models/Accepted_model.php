<?phpclass Accepted_model extends CI_Model {    public function ssp_accepted_model() {        $table = "accepted_dash";        $primaryKey = "id";        $columns = array(            array('db' => 'patient_name', 'dt' => 0, 'formatter' => function( $data, $row ) {                    return $data . " <span class='small-case'>" .                            convert_priority_to_display_name($row["priority"]) . "</span>";                }),            array('db' => 'referral_reason', 'dt' => 1),            array('db' => 'assigned_physician', 'dt' => 2),            array('db' => 'accepted_status', 'dt' => 3),            array('db' => 'accepted_status_icon', 'dt' => 4),            array('db' => 'id', 'dt' => 5),            array('db' => 'priority', 'dt' => 6),            array('db' => 'days_string', 'dt' => 7)        );        $sql_details = array(            'user' => $this->db->username,            'pass' => $this->db->password,            'db' => $this->db->database,            'host' => $this->db->hostname        );        $where = "1";        if ($this->session->userdata("login_role") == "clinic_admin")            $where = "clinic_admin=" . $this->session->userdata("user_id");        else if ($this->session->userdata("login_role") == "clinic_physician")            $where = "physician_id=" . $this->session->userdata("physician_id");        require('ssp.class.php');        return json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where));    }    public function get_referral_dash_info_model() {        $this->form_validation->set_rules('id', 'Referral Id', 'required');        if ($this->form_validation->run()) {            $data = $this->input->post();            $this->db->select(                    "`pat`.`fname` as pat_fname," .                    "`pat`.`lname` as pat_lname," .                    "`pat`.`dob` as pat_dob," .                    "`pat`.`cell_phone` as pat_cell_phone," .                    "`pat`.`home_phone` as pat_home_phone," .                    "`pat`.`work_phone` as pat_work_phone," .                    "`pat`.`email_id` as pat_email_id," .                    "`pat`.`ohip` as pat_ohip," .                    "`pat`.`address` as pat_address," .                    "`pat`.`gender` as pat_gender," .                    "md5(`pat`.`location_id`) as location_id," .                    "md5(`pat`.`custom_id`) as custom_id," .                    "pat.next_visit," .                    "if(`pat`.`dob`, DATE_FORMAT(`pat`.`dob`, ' (%b %d, %Y)'), '') AS pat_dob2," .                    "`dr`.`fname` as dr_fname," .                    "`dr`.`lname` as dr_lname," .                    "`dr`.`email` as dr_email_id," .                    "`dr`.`phone` as dr_phone_number," .                    "`dr`.`fax` as dr_fax," .                    "`dr`.`address` as dr_address," .                    "`dr`.`billing_num` as dr_billing_num," .                    "`c_ref`.`referral_reason`," .                    "if(isnull(c_dr.id),'empty', md5(c_dr.id)) as assigned_physician," .                    "r_tri.priority as priority," .                    "if(isnull(c_dr.id), 'Not Assigned', concat(c_dr.first_name, ' ', c_dr.last_name)) as assigned_physician_name");            $this->db->from("`efax_info` `efax`, referral_patient_info pat, referral_physician_info dr, referral_clinic_triage r_tri, `clinic_referrals` `c_ref` left join clinic_physician_info c_dr on ( c_dr.active = 1 and c_dr.id = `c_ref`.assigned_physician)");            $this->db->where(                    array(                        "efax.active" => 1,                        "dr.active" => 1,                        "r_tri.active" => 1,                        "pat.active" => 1,                        "c_ref.active" => 1,                        "md5(pat.id)" => $data["id"],                        "efax.to" => $this->session->userdata("user_id")                    )            );            $this->db->where("c_ref.efax_id", "efax.id", false);            $this->db->where("r_tri.patient_id", "pat.id", false);            $this->db->where("dr.patient_id", "pat.id", false);            $this->db->where("pat.referral_id", "`c_ref`.id", false);            $result = $this->db->get()->result(); //            //log_message("error", "working sql = " . $this->db->last_query());            $this->db->select("md5(chk.id) as id," .                    "chk.checklist_id, " .                    "chk.attached, " .                    "case " .                    "when (chk.checklist_id <> 0) then itm.name " .                    "else chk.checklist_name " .                    "end " .                    "as checklist_name");            $this->db->from("referral_checklist chk");            $this->db->join("clinic_referral_checklist_items itm", "chk.checklist_id = itm.id and itm.active=1", "left");            $this->db->where(array(                "md5(chk.patient_id)" => $data["id"],                "chk.active" => 1            ));            $this->db->or_group_start()                    ->where("itm.clinic_id", $this->session->userdata("user_id"))                    ->where("chk.checklist_type", "typed")                    ->group_end();            $result2 = $this->db->get()->result();            //log_message("error", "sql = " . $this->db->last_query());            //prepare clinical triage data ( diseases, tests, etc)            // get clinic triage id            $triage_data = null;            $this->db->select("r_tri.id");            $this->db->from("referral_clinic_triage r_tri");            $this->db->join("referral_patient_info pat", "r_tri.patient_id = pat.id", "left");            $this->db->where(array(                "r_tri.active" => 1,                "pat.active" => 1,                "md5(pat.id)" => $data["id"]            ));            $tmp_result = $this->db->get()->result();            if ($tmp_result) {                $triage_id = $tmp_result[0]->id;                //get details of diseases and other data                $this->db->select("disease");                $this->db->from("referral_clinic_triage_disease_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $diseases = $this->db->get()->result();                $this->db->select("drug");                $this->db->from("referral_clinic_triage_drugs_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $drugs = $this->db->get()->result();                $this->db->select("symptom");                $this->db->from("referral_clinic_triage_symptom_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $symptoms = $this->db->get()->result();                $this->db->select("test");                $this->db->from("referral_clinic_triage_tests_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $tests = $this->db->get()->result();                $this->db->select("device");                $this->db->from("referral_clinic_triage_devices_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $devices = $this->db->get()->result();                $triage_data = array(                    "diseases" => $diseases,                    "drugs" => $drugs,                    "symptoms" => $symptoms,                    "tests" => $tests,                    "devices" => $devices                );                $visit_timings = $this->db                                ->select("md5(id) as id, visit_type, visit_duration")                                ->from("clinic_visit_timings")                                ->where(array(                                    "active" => 1,                                    "clinic_id" => $this->session->userdata("user_id")                                ))->get()->result();            }            return array(                "dash_info" => $result,                "checklist_info" => $result2,                "triage_info" => $triage_data,                "visit_timings" => $visit_timings            );        } else {            return validation_errors();        }    }    public function new_referral_model() {        //log_message("error", "=========================================");        //log_message("error", "Tracking New Referral from accepted");        //log_message("error", "=========================================");        $this->form_validation->set_rules('pat_fname', 'Patient First Name', 'required');        $this->form_validation->set_rules('pat_lname', 'Patient Last Name', 'required');        $this->form_validation->set_rules('patient_location', 'Patient Location', 'required');//                if ($this->form_validation->run()) {            $data = $this->input->post();            //validate requirement of atleast 1 phone number out of 3            $invalid_number_count = false;            if (empty($data["pat_cell_phone"]) && empty($data["pat_home_phone"]) &&                    empty($data["pat_work_phone"])) {                $invalid_number_count = true;            }            if ($invalid_number_count) {                return array(false, "A contact number is required to add a patient into the scheduling queue");            }            try {                //log_message("error", "ref code = " . $referral_code);                $this->db->trans_start();                //create fake efax                $this->db->insert("efax_info", array(                    "to" => $this->session->userdata("user_id"),                    "file_name" => "333.pdf",                    "tiff_file_name" => "222.tif",                    "pages" => 3,                    "sender_fax_number" => "111",                    "referred" => TRUE                ));                $efax_id = $this->db->insert_id();                //log_message("error", "insert fake efax  = " . $this->db->last_query());                //add referral                $referral_code = $this->generate_referral_code();                $referral_reason = (isset($data["reasons"])) ? $data["reasons"][0] : "";                $assigned_physician = get_decrypted_id($data["assigned_physician"], "clinic_physician_info");                $first_status = "Accepted";                $insert_data = array(                    "efax_id" => $efax_id,                    "referral_code" => $referral_code,                    "referral_reason" => $referral_reason,                    "status" => $first_status,                    "assigned_physician" => $assigned_physician                );                //If clinic has only 1 physician account, then assign by default                 $physicians = $this->db->select("id")                        ->from("clinic_physician_info")                        ->where(array(                            "clinic_id" => $this->session->userdata("user_id")                        ))->get()->result();                if ($physicians && sizeof($physicians) === 1) {                    $insert_data["assigned_physician"] = $physicians[0]->id;                }                $this->db->insert("clinic_referrals", $insert_data);                //new referral record added                //log_message("error", "insert clinic referrals  = " . $this->db->last_query());                $referral_id = $this->db->insert_id();                $ohip = $data["pat_ohip"];                //store patient details                $patient_location = get_decrypted_id($data["patient_location"], "clinic_locations");                $custom = (isset($data["custom"]))?                        get_decrypted_id($data["custom"], "clinic_custom"):0;                $patient_data = array(                    "referral_id" => $referral_id,                    "fname" => $data["pat_fname"],                    "lname" => $data["pat_lname"],                    "dob" => $data["pat_dob_year"] . "-" . $data["pat_dob_month"] . "-" . $data["pat_dob_day"],                    "ohip" => str_replace(" ", "", str_replace("-", "", $ohip)),                    "cell_phone" => $data["pat_cell_phone"],                    "home_phone" => $data["pat_home_phone"],                    "work_phone" => $data["pat_work_phone"],                    "mrn" => $data["pat_mrn"],                    "gender" => "male",                    "next_visit" => "Initial consult",                    "location_id" => $patient_location,                    "custom_id" => $custom                );                $this->db->insert("referral_patient_info", $patient_data);                $patient_id = $this->db->insert_id();                //log_message("error", "insert patient = " . $this->db->last_query());//                //log_message("error", "dr_fax trimmed = " . $data["dr_fax"]);                //store referring physician data linked to patient id                $physician_data = array(                    "patient_id" => $patient_id                );                $this->db->insert("referral_physician_info", $physician_data);                //log_message("error", "insert physician  = " . $this->db->last_query());                //store clinical triage info linked to patient id                $clinical_triage_data = array(                    "patient_id" => $patient_id,                    "priority" => (!isset($data["priority"]) || $data["priority"] == null || empty($data["priority"])) ? "not_specified" : $data["priority"]                );                $this->db->insert("referral_clinic_triage", $clinical_triage_data);                $clinic_triage_id = $this->db->insert_id();                //log_message("error", "triage referral = " . $this->db->last_query());                //insert referral checklist//                if (isset($data["referral_checklist"]))//                    $referral_checklist = $data["referral_checklist"];//                else                $referral_checklist = array();                //log_message("error", "checklist array = " . json_encode($referral_checklist));                //insert default checklist info                $this->db->select("md5(id) as id, id as plain_id");                $this->db->from("clinic_referral_checklist_items");                $this->db->where(array(                    "active" => 1,                    "clinic_id" => $this->session->userdata("user_id")                ));                $default_checklist = $this->db->get()->result();                //log_message("error", "checklist query = " . $this->db->last_query());                //log_message("error", "default checklist = " . json_encode($default_checklist));                foreach ($default_checklist as $key => $value) {                    $exist = array_search($value->id, $referral_checklist);                    $checked = ($exist === false) ? "false" : "true";                    // //log_message("error", "val = " . $value->id . " and ref = " . json_encode($referral_checklist));                    $check_type = "stored";                    $this->db->insert("referral_checklist", array(                        "patient_id" => $patient_id,                        "checklist_type" => $check_type,                        "checklist_id" => $value->plain_id,                        "attached" => $checked                    ));                    //log_message("error", "insert for default = " . $this->db->last_query());                }                //insert new checlist info                //log_message("error", "at custome checklist");                $data["new_checklists"] = (isset($data["new_checklists"])) ? $data["new_checklists"] : "";                $new_checklist = explode(",", $data["new_checklists"]);                foreach ($new_checklist as $key => $value) {                    if ($value == "")                        continue;                    $exist = array_search($value, $referral_checklist);                    // //log_message("error", "val = " . $value . " and ref = " . json_encode($referral_checklist));                    $checked = ($exist === false) ? "false" : "true";                    $check_type = "typed";                    $this->db->insert("referral_checklist", array(                        "patient_id" => $patient_id,                        "checklist_type" => $check_type,                        "checklist_name" => $value,                        "attached" => $checked                    ));                    //log_message("error", "insert custom = " . $this->db->last_query());                }                $this->db->trans_complete();                $new_visit_duration = 30;                $this->load->model("referral_model");                $response = $this->referral_model                        ->create_patient_visit(md5($patient_id), "First visit", $new_visit_duration);                if ($response) {                    //log_message("error", "referral id => '$referral_id' => " . "accepted/referral_details/" . md5($referral_id));                    //log_message("error", "=========================================");                    //log_message("error", "=====   Referral created            ======");                    //log_message("error", "=========================================");                    //log_message("error", "=========================================");                    return true;                } else {                    return array(false, "Patient visit not scheduled");                }            } catch (Exception $exception) {                return array(false, "SQL Exception occured");            }        } else {            return array(false, validation_errors());        }    }    private function generate_referral_code() {        //log_message("error", "generate_referral_code called");        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';        $charactersLength = strlen($characters);        $referral_code = '';        $repeat = TRUE;        $length = 7; // length of referral code size        while ($repeat) {            //log_message("error", "repeate inside");            $referral_code = '';            for ($i = 0; $i < $length; $i++) {                $referral_code .= $characters[rand(0, $charactersLength - 1)];            }            //log_message("error", "code = " . $referral_code);            //check in db if such referral code already exist            $this->db->select("count(id) as repeat_count");            $this->db->from("clinic_referrals");            $this->db->where(array(                "referral_code" => $referral_code            ));            $result = $this->db->get()->result();            //log_message("error", "res = " . json_encode($result));            if ($result[0]->repeat_count == 0) {                //no duplicate code found                $repeat = false;            }        }        return $referral_code;    }}