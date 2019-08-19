<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

    public function index() {
        echo "Login controller";
//        if (!admin_login()) {
//            $this->load->view("login_master");
//        } else {
//            redirect("dashboard");
//        }
    }

    public function verify_admin_login() {
        $this->load->model("login_model");
        $response = $this->login_model->verify_admin_login_model();
        if ($response === true) {
            redirect("dashboard");
        } else {
            $data["validation_errors"] = $response;
            $this->load->view("login_master", $data, null);
        }
    }

    public function logout() {
        if (!$this->session->userdata('username')) {
            //log_message("debug", $this->session->userdata("username") . " logged out." .
                   // " id = " . $this->session->userdata("user_id"));
        }
        $this->session->sess_destroy();
        redirect("dashboard");
    }

}
