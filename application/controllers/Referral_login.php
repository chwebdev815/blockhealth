<?php

class Referral_login extends CI_Controller {

    public function index() {
        if (!clinic_login() || !referring_physician_login()) {
            $this->load->view("referral_login_master");
        } else {
            redirect("/");
        }
    }

}
