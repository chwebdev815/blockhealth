<?phpclass Referral_triage_model extends CI_Model {    public function ssp_referral_triage_model() {        $table = "view_dash_referral_triage";        if ($this->session->userdata("emr_pathway") === "AccuroCitrix") {            $table = "view_dash_referral_triage_rpa";        }        if ($this->session->userdata("emr_pathway") === "OscarEMR") {            $table = "view_dash_referral_triage_oscar";            $oscar_table = $this->db->select("id, status, first_name, last_name")                            ->from("oscar_integration")                            ->order_by("id", "desc")                            ->limit("10")->get()->result();            log_message("error", "OSCAR TABLE  = " . json_encode($oscar_table));        }        $primaryKey = "id";        $columns = array(            array('db' => 'patient_name', 'dt' => 0, 'formatter' => function( $data, $row ) {                    return $data . " <span class='small-case'>" .                            convert_priority_to_display_name($row["priority"]) . "</span>";                }),            array('db' => 'referral_reason', 'dt' => 1),            array('db' => 'assigned_physician', 'dt' => 2),            array('db' => 'missing_status', 'dt' => 3),            array('db' => 'id', 'dt' => 4),            array('db' => 'priority', 'dt' => 5)        );        $sql_details = array(            'user' => $this->db->username,            'pass' => $this->db->password,            'db' => $this->db->database,            'host' => $this->db->hostname        );        require('ssp.class.php');        return json_encode(                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null                        , "clinic_admin=" . $this->session->userdata("user_id"))        );    }    public function get_referral_dash_info_model() {        $this->form_validation->set_rules('id', 'Referral Id', 'required');        if ($this->form_validation->run()) {            $data = $this->input->post();            $this->db->select(                    "`pat`.`fname` as pat_fname," .                    "`pat`.`lname` as pat_lname," .                    "`pat`.`dob` as pat_dob," .                    "`pat`.`cell_phone` as pat_cell_phone," .                    "`pat`.`home_phone` as pat_home_phone," .                    "`pat`.`work_phone` as pat_work_phone," .                    "`pat`.`email_id` as pat_email_id," .                    "`pat`.`ohip` as pat_ohip," .                    "`pat`.`address` as pat_address," .                    "`pat`.`gender` as pat_gender," .                    "md5(`pat`.`location_id`) as location_id," .                    "md5(`pat`.`custom_id`) as custom_id," .                    "pat.next_visit," .                    "if(`pat`.`dob`, DATE_FORMAT(`pat`.`dob`, ' (%b %d, %Y)'), '') AS pat_dob2," .                    "`dr`.`fname` as dr_fname," .                    "`dr`.`lname` as dr_lname," .                    "`dr`.`email` as dr_email_id," .                    "`dr`.`phone` as dr_phone_number," .                    "`dr`.`fax` as dr_fax," .                    "`dr`.`address` as dr_address," .                    "`dr`.`billing_num` as dr_billing_num," .                    "`c_ref`.`referral_reason`," .                    "if(isnull(c_dr.id),'empty', md5(c_dr.id)) as assigned_physician," .                    "r_tri.priority as priority," .                    "if(isnull(c_dr.id), 'Not Assigned', concat(c_dr.first_name, ' ', c_dr.last_name)) as assigned_physician_name");            $this->db->from("`efax_info` `efax`, referral_patient_info pat, referral_physician_info dr, referral_clinic_triage r_tri, `clinic_referrals` `c_ref` left join clinic_physician_info c_dr on ( c_dr.active = 1 and c_dr.id = `c_ref`.assigned_physician)");            $this->db->where(                    array(                        "efax.active" => 1,                        "dr.active" => 1,                        "r_tri.active" => 1,                        "pat.active" => 1,                        "c_ref.active" => 1,                        "md5(pat.id)" => $data["id"],                        "efax.to" => $this->session->userdata("user_id")                    )            );            $this->db->where("c_ref.efax_id", "efax.id", false);            $this->db->where("r_tri.patient_id", "pat.id", false);            $this->db->where("dr.patient_id", "pat.id", false);            $this->db->where("pat.referral_id", "`c_ref`.id", false);            $result = $this->db->get()->result();//            log_message("error", "working sql = " . $this->db->last_query());            $this->db->select("md5(chk.id) as id," .                    "chk.checklist_id, " .                    "chk.attached, " .                    "case " .                    "when (chk.checklist_id <> 0) then itm.name " .                    "else chk.checklist_name " .                    "end " .                    "as checklist_name");            $this->db->from("referral_checklist chk");            $this->db->join("clinic_referral_checklist_items itm", "chk.checklist_id = itm.id and itm.active=1", "left");            $this->db->where(array(                "md5(chk.patient_id)" => $data["id"],                "chk.active" => 1            ));            $this->db->or_group_start()                    ->where("itm.clinic_id", $this->session->userdata("user_id"))                    ->where("chk.checklist_type", "typed")                    ->group_end();            $result2 = $this->db->get()->result();            log_message("error", "sql = " . $this->db->last_query());            //prepare clinical triage data ( diseases, tests, etc)            // get clinic triage id            $triage_data = null;            $this->db->select("r_tri.id");            $this->db->from("referral_clinic_triage r_tri");            $this->db->join("referral_patient_info pat", "r_tri.patient_id = pat.id", "left");            $this->db->where(array(                "r_tri.active" => 1,                "pat.active" => 1,                "md5(pat.id)" => $data["id"]            ));            $tmp_result = $this->db->get()->result();            if ($tmp_result) {                $triage_id = $tmp_result[0]->id;                //get details of diseases and other data                $this->db->select("disease");                $this->db->from("referral_clinic_triage_disease_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $diseases = $this->db->get()->result();                $this->db->select("drug");                $this->db->from("referral_clinic_triage_drugs_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $drugs = $this->db->get()->result();                $this->db->select("symptom");                $this->db->from("referral_clinic_triage_symptom_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $symptoms = $this->db->get()->result();                $this->db->select("test");                $this->db->from("referral_clinic_triage_tests_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $tests = $this->db->get()->result();                $this->db->select("device");                $this->db->from("referral_clinic_triage_devices_info");                $this->db->where(array("clinic_triage_id" => $triage_id, "active" => 1));                $devices = $this->db->get()->result();                $triage_data = array(                    "diseases" => $diseases,                    "drugs" => $drugs,                    "symptoms" => $symptoms,                    "tests" => $tests,                    "devices" => $devices                );            }            $visit_timings = $this->db                            ->select("md5(id) as id, visit_type, visit_duration")                            ->from("clinic_visit_timings")                            ->where(array(                                "active" => 1,                                "clinic_id" => $this->session->userdata("user_id")                            ))->get()->result();            return array(                "dash_info" => $result,                "checklist_info" => $result2,                "triage_info" => $triage_data,                "visit_timings" => $visit_timings            );        } else {            return validation_errors();        }    }}