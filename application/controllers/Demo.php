<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Demo extends CI_Controller {

    public function index() {
        $this->session->set_userdata("username", "demo@blockhealth.co");
        $this->session->set_userdata("user_id", 8);
        // $this->session->set_userdata("physician_name", "Dr. " . $name);
        $this->session->set_userdata("physician_name", "Demo");
        $this->session->set_userdata("login_role", "clinic_admin");
        // //log_message("error", "clinic admin ".$id.",".$name." logged in");
        //set emr pathway
        $this->session->set_userdata("emr_pathway", "");
        redirect("/");
    }

}
