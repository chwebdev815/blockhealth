<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Schedule_settings extends CI_Controller {

    public function index() {
        if (clinic_login()) {
            $data['page_content'] = $this->load->view('schedule_settings_master', NULL, TRUE);
            $data['page_title'] = "Schedule Settings";
            $data['jquery'] = $this->load->view('scripts/schedule_settings_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }

}
