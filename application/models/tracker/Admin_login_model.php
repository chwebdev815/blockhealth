<?php

class Admin_login_model extends CI_Model {

    public function verify_admin_login_model() {
        $this->form_validation->set_rules('tracker_email', 'Email', 'required|valid_email');
        $this->form_validation->set_rules('tracker_password', 'Password', 'required');
        if ($this->form_validation->run()) {
            $data = $this->input->post();

            $email = $data["tracker_email"];
            $pass = $data["tracker_password"];

            $this->db->select("id, concat(first_name, ' ', last_name) as name, password");
            $this->db->from("user_tracker");
            $this->db->where(array(
                "email_id" => $email,
                "status" => 1
            ));
            $result = $this->db->get()->result();
            log_message("error", "last q = " . $this->db->last_query());
            $response = null;
            if ($result) {
                $info = $result[0];
                $password = $info->password;
                if (password_verify($pass, $password)) {
                    $id = intval($info->id);
                    $name = $info->name;
                    $this->session->set_userdata("username", $email);
                    $this->session->set_userdata("user_id", $id);
                    // $this->session->set_userdata("physician_name", "Dr. " . $name);
                    $this->session->set_userdata("physician_name", $name);
                    $this->session->set_userdata("login_role", "admin");
                    // log_message("error", "clinic admin ".$id.",".$name." logged in");
                    return true;
                }
            } else {
                $response = "Username or Password is incorrect";
            }
            return $response;
        } else {
            return validation_errors();
        }
    }

}
