<?php

class Tracker extends CI_Controller {

    public function referral() {
        if (referring_physician_login() || $this->session->userdata("signup_done") == "yes") {
            $data['page_content'] = $this->load->view('tracker_view', NULL, TRUE);
            $data['page_title'] = "Patient Page";
            $data['jquery'] = $this->load->view('scripts/tracker_script', NULL, TRUE);
            $this->load->view('rp_main', $data);
        } else if ($this->session->userdata("signup_done") == "no") {
            if($this->session->userdata("referral_code") == $this->uri->segment(3)) {
                $data['page_content'] = $this->load->view('tracker_view', NULL, TRUE);
                $data['page_title'] = "Patient Page";
                $data['jquery'] = $this->load->view('scripts/tracker_script', NULL, TRUE);
                $this->load->view('rp_main', $data);
            } 
            else if($this->session->userdata("referral_code")) {
                redirect("tracker/referral/" . $this->session->userdata("referral_code"));
            } else {
                redirect(base_url());
            }
        } else {
            redirect(base_url());
        }
    }

    public function signup() {
        if ($this->session->userdata("signup_done") == "no") {
            $this->load->model("tracker_model");
            $response = $this->tracker_model->signup_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function load_tracker() {
        if (referring_physician_login() || $this->session->userdata("signup_done")) {
            $this->load->model("tracker_model");
            $response = $this->tracker_model->load_tracker_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }
    
    public function upload_missing_items() {
        if (referring_physician_login() || $this->session->userdata("signup_done")) {
            $this->load->model("tracker_model");
            $response = $this->tracker_model->upload_missing_items_model();
            if($response === true) {
                log_message("error", "File uploaded successfully :-> " . json_encode($response));
                $this->session->set_flashdata("success", "Missing items uploaded successfully");
                $this->session->keep_flashdata('success');
                redirect("tracker/referral/" . $this->input->post("id"));
            }
            else {
                log_message("error", "Failed to upload :-> " . json_encode($response));
                $this->session->set_flashdata("error", "Missing items failed to upload");
                $this->session->keep_flashdata('error');
                redirect("tracker/referral/" . $this->input->post("id"));
            }
        } else {
            redirect("/");
        }
    }
    
    public function get_upload_missing_item_info() {
        if (referring_physician_login() || $this->session->userdata("signup_done")) {
            $this->load->model("tracker_model");
            $response = $this->tracker_model->get_upload_missing_item_info_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }
    
    public function remove_missing_item() {
        if (referring_physician_login()) {
            $this->load->model("tracker_model");
            $response = $this->tracker_model->remove_missing_item_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

}
