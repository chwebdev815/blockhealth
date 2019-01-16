<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function index() {
        if (!clinic_login() || !referring_physician_login()) {
            $this->load->view("signin_master");
        } else {
            redirect("/");
        }
    }

    public function verify_login() {
        $this->load->model("login_model");
        $response = $this->login_model->verify_login_model();
        if ($response === true) {
            redirect("/");
        } else {
            $data["validation_errors"] = $response;
            $this->load->view("signin_master", $data, null);
        }
    }

    public function logout() {
        if (!$this->session->userdata('username')) {
            log_message("debug", $this->session->userdata("username") . " logged out." .
                    " id = " . $this->session->userdata("user_id"));
        }
        $this->session->sess_destroy();
        redirect("/");
    }

    public function verify_new_physician_account() {
        $this->load->model("login_model");
        $this->login_model->verify_new_physician_account_model();
    }

    public function new_physician() {
        if ($this->session->userdata("login_role") && $this->session->userdata("login_role") == "verify_clinic_physician") {
            $data['page_content'] = $this->load->view('physician_first_login', NULL, TRUE);
            $data['page_title'] = "Physician credentials";
            $data['jquery'] = $this->load->view('scripts/physician_first_login_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }

    public function store_physician_creds() {
        if ($this->session->userdata("login_role") && $this->session->userdata("login_role") == "verify_clinic_physician") {
            $this->load->model("login_model");
            $response = $this->login_model->store_physician_creds_model();
        } else {
            $response = "Invalid attempt";
        }
        echo json_encode($response);
    }

    public function verify_referral_code() {
        $this->load->model("login_model");
        $response = $this->login_model->verify_referral_code_model();
        echo json_encode($response);
    }

}
