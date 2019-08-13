<?php

class Login_model extends CI_Model {

    public function verify_login_model() {
        $this->form_validation->set_rules('signup-email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('signup-pw', 'Password', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();

            if ($data["login_type"] == "c") {
                $this->db->select("id, concat(first_name, ' ', last_name) as name, password, emr_pathway");
                $this->db->from("clinic_user_info");
                $this->db->where(array(
                    "email_id" => $data["signup-email"],
                    "active" => 1
                ));
                $result = $this->db->get()->result();
                // log_message("error", "mail res = " . json_encode($result));
                if ($result) {
                    $password = $result[0]->password;
                    if (password_verify($data["signup-pw"], $password)) {
                        $id = intval($result[0]->id);
                        $name = $result[0]->name;
                        $this->session->set_userdata("username", $data["signup-email"]);
                        $this->session->set_userdata("user_id", $id);
                        // $this->session->set_userdata("physician_name", "Dr. " . $name);
                        $this->session->set_userdata("physician_name", $name);
                        $this->session->set_userdata("login_role", "clinic_admin");
                        // log_message("error", "clinic admin ".$id.",".$name." logged in");
                        //set emr pathway
                        $this->session->set_userdata("emr_pathway", $result[0]->emr_pathway);

                        return true;
                    }
                }
                //look in specialist 
                $this->db->select("id, concat(first_name, ' ', last_name) as name, password, clinic_id, ");
                $this->db->from("clinic_physician_info");
                $this->db->where(array(
                    "email_id" => $data["signup-email"],
                    "active" => 1
                ));

                $result = $this->db->get()->result();
                // log_message("error", "mail res2 = " . json_encode($result));
                if ($result) {
                    $password = $result[0]->password;
                    // log_message("error", "pass = " . $password . " and  ". $result[0]->password);
                    if (password_verify($data["signup-pw"], $password)) {
                        $id = intval($result[0]->id);
                        $name = $result[0]->name;
                        $this->session->set_userdata("username", $data["signup-email"]);
                        $this->session->set_userdata("physician_id", $id);
                        $this->session->set_userdata("physician_name", "Dr. " . $name);
                        $this->session->set_userdata("login_role", "clinic_physician");
                        $this->session->set_userdata("user_id", $result[0]->clinic_id);
                        // log_message("debug", "clinic physician ".$id.",".$name." logged in");
                        $response = true;
                    } else {
                        $response = "Username or Password is incorrect";
                    }
                } else {
                    $response = "Username or Password is incorrect";
                }
                return $response;
            } else if ($data["login_type"] == "rp") {
                $this->db->select("id, concat(first_name, ' ', last_name) as name, password, fax_number");
                $this->db->from("referring_physicians");
                $this->db->where(array(
                    "email_id" => $data["signup-email"],
                    "active" => 1
                ));
                $result = $this->db->get()->result();
                log_message("error", "result res = " . json_encode($result));
                log_message("error", "res = " . json_encode($this->db->last_query()));
                if ($result) {
                    $password = $result[0]->password;
                    if ($data["signup-pw"] === $password) {
//                    if (password_verify($data["signup-pw"], $password)) {
                        $id = intval($result[0]->id);
                        $name = $result[0]->name;
                        $this->session->set_userdata("username", $data["signup-email"]);
                        $this->session->set_userdata("rp_user_id", $id);
                        $this->session->set_userdata("physician_name", "Dr. " . $name);
                        $this->session->set_userdata("login_role", "referring_physician");
                        $this->session->set_userdata("fax_number", $result[0]->fax_number);
                        log_message("error", "referring_physician " . $id . "," . $name . " logged in");
                        return true;
                    } else {
                        $response = "Username or Password is incorrect";
                    }
                } else {
                    $response = "Username or Password is incorrect";
                }
                return $response;
            }
        }
        return validation_errors();
    }

    public function verify_new_physician_account_model() {
        $login_key = $this->uri->segment(3);
        $this->db->select("id, create_datetime, active");
        $this->db->from("clinic_physician_info");
        $this->db->where(array(
            "login_key" => $login_key
        ));
        $result = $this->db->get()->result();
        log_message("error", "choose dr active = " . $this->db->last_query());
        log_message("error", "result = " . json_encode($result));
        log_message("error", "active = " . $result[0]->active);
        
        if ($result) {
            log_message("error", "inside if");
            log_message("error", "active = " . $result[0]->active);
            //check if time is out (1 day)
            $created_at = DateTime::createFromFormat('Y-m-d H:i:s', $result[0]->create_datetime);
//            $expire_time = $created_at->add(new DateInterval('P1D'))->format("Y-m-d H:i:s");
            $expire_time = $created_at->add(new DateInterval('PT5M'))->format("Y-m-d H:i:s");
            $current_time = date("Y-m-d H:i:s");
            if ($expire_time < $current_time) {
                //link is expired, need to resend it
                log_message("error", "link is expired - $expire_time comp $current_time");
                show_error("This link is no longer active", 200, "This link is no longer active");
                exit();
            }//
            //check if link is already activated or used
            if ($result[0]->active === "1") {
                //link is used
                log_message("error", "link is used , " . json_encode($result));
                show_error("This link is no longer active", 200, "This link is no longer active");
                exit();
            }

            log_message("error", "going for activation of physician");

//            $this->db->select("DATE_FORMAT(CURDATE(), 'Active (%b %D %Y)') as status");
//            $status = $this->db->get()->result()[0]->status;
            $status = "Expired"; // till the password is not set
            $this->db->where(array(
                "id" => $result[0]->id,
                "active" => 0
            ));
            $this->db->update("clinic_physician_info", array(
                "status" => $status
            ));
            log_message("error", "make dr active = " . $this->db->last_query());

            $this->db->select("email_id, id, concat(first_name, ' ', last_name) as physician_name, clinic_id");
            $this->db->from("clinic_physician_info");
            $this->db->where(array(
                "login_key" => $login_key,
                "active" => 0
            ));
            $result = $this->db->get()->result();
            if ($result) {
                $this->session->set_userdata("email", $result[0]->email_id);
                $this->session->set_userdata("physician_id", $result[0]->id);
                $this->session->set_userdata("physician_name", "Dr. " . $result[0]->physician_name);
                $this->session->set_userdata("login_role", "verify_clinic_physician");
                $this->session->set_userdata("user_id", $result[0]->clinic_id);
                log_message("error", "new physician login data = " . json_encode($result));
                redirect("login/new_physician");
            } else {
                redirect("login");
            }
        } else {
            log_message("error", "Invalid link");
            show_error("This link is not valid", 200, "This link is not valid");
            exit();
        }
    }

    public function store_physician_creds_model() {
        $this->form_validation->set_rules('new_password', 'Password', 'required');
        $this->form_validation->set_rules('repeat_new_password', 'Confirm Password', 'required|matches[new_password]');
        if ($this->form_validation->run()) {
            $data = $this->input->post();
            
            //status field 
            $this->db->select("DATE_FORMAT(CURDATE(), 'Active (%b %D %Y)') as status");
            $status = $this->db->get()->result()[0]->status;
            
            //set activation data
            $this->db->where(array(
                "id" => $this->session->userdata("physician_id"),
                "active" => 0,
                "status" => "Expired"
            ));
            $this->db->update("clinic_physician_info", array(
                "password" => password_hash($data["new_password"], PASSWORD_BCRYPT),
                "active" => 1,
                "status" => $status
            ));
            if ($this->db->affected_rows() > 0) {
                $this->session->set_userdata("login_role", "clinic_physician");
                $this->session->set_userdata("username", $this->session->userdata("email"));
                $this->session->unset_userdata("email");
                return true;
            } else {
                return "Failed to store password.";
            }
        } else {
            return validation_errors();
        }
    }

    public function verify_referral_code_model() {
        $this->form_validation->set_rules('referral_code', 'Reference Code', 'required');

        if ($this->form_validation->run()) {
            $data = $this->input->post();
            $ref_code = $data['referral_code'];
            log_message("error", "code = " . $ref_code);

            $this->db->select("dr.fax, dr.id, concat(dr.fname, ' ', dr.lname) as physician_name");
            $this->db->from("clinic_referrals c_ref, referral_patient_info pat, referral_physician_info dr");
            $this->db->where(array(
                "c_ref.referral_code" => $ref_code,
                "c_ref.active" => 1,
                "pat.active" => 1,
                "dr.active" => 1
            ));
            $this->db->where("c_ref.id", "pat.referral_id", false);
            $this->db->where("pat.id", "dr.patient_id", false);
            $result = $this->db->get()->result();
            log_message("error", "check sql 1 = " . $this->db->last_query());
            if ($result) {
                //referral code exist
                $sender_fax_number = $result[0]->fax;

                $this->session->set_userdata("fax_number", $sender_fax_number);
                log_message("error", "session fax_number = " . $sender_fax_number);
                // $physician_id = $result[0]->id;
                // $physician_name = $result[0]->physician_name;

                $rp_result = $this->db->select("concat(r_dr.first_name, ' ', r_dr.last_name) as physician_name, r_dr.id")
                                ->from("referring_physicians r_dr")
                                ->where(array(
                                    "fax_number" => $sender_fax_number
                                        )
                                )->get()->result();


                if ($rp_result) {
                    $user_id = $rp_result[0]->id;
                    $physician_name = $rp_result[0]->physician_name;
                    $this->session->set_userdata("signup_done", "yes");
                    $this->session->set_userdata("rp_user_id", $user_id);
                    $this->session->set_userdata("physician_name", $physician_name);
                    $this->session->set_userdata("login_role", "referring_physician");
                    log_message("error", "session signup_done = yes");
                    log_message("error", "session user_id = $user_id");
                } else {
                    $this->session->set_userdata("signup_done", "no");
                    log_message("error", "session signup_done = no");
                    $this->session->set_userdata("referral_code", $ref_code);
                    log_message("error", "session referral_code = " . $ref_code);
                }
                return array(
                    "response" => "success",
                    "ref_code" => $ref_code
                );
            } else {
                return array(
                    "response" => "error",
                    "desc" => "Referral code is incorrect."
                );
            }
        } else {
            return array(
                "response" => "error",
                "desc" => validation_errors()
            );
        }
    }

}
