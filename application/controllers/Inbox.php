<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Inbox extends CI_Controller {

    public function index() {
        if (clinic_login()) {
            $data['page_content'] = $this->load->view('inbox_master', NULL, TRUE);
            $data['page_title'] = "Fax Inbox";
            $data['jquery'] = $this->load->view('scripts/inbox_script', NULL, TRUE);
            $this->load->view('main', $data);
        } else {
            redirect("/");
        }
    }

    public function ssp_inbox() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->ssp_inbox_model();
            echo $response;
        } else {
            echo false;
        }
    }

    public function ssp_clinic_patients() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->ssp_clinic_patients_model();
            echo $response;
        } else {
            echo false;
        }
    }

    public function check_physician_data() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->check_physician_data_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function get_clinic_referral_usage_forms() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->get_clinic_referral_usage_forms_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function check_patient_data() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->check_patient_data_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function save_patient_record() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->save_patient_record_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function save_task() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->save_task_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function new_referral() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->new_referral_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function missing_items_details() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->missing_items_details_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function request_missing_items() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->request_missing_items_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function delete_referral() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->delete_referral_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function get_clinic_patients() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->get_clinic_patients_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function get_physician_list_save_patient() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->get_physician_list_save_patient_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function get_patient_list_save_patient() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->get_patient_list_save_patient_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function save_referral() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->save_referral_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function add_health_record() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->add_health_record_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function predict_api() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->predict_api_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function phy_extract_api() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->phy_extract_api_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function medication_api() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->medication_api_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function get_referral_checklist() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->get_referral_checklist_model();
        } else {
            $response = "Sesion Expired";
        }
        echo json_encode($response);
    }

    public function save_data_points_predict() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $this->inbox_model->save_data_points_predict_model();
        }
    }

    public function save_data_points_drug() {
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $this->inbox_model->save_data_points_drug_model();
        }
    }

    public function patient_autocomplete() {
        $response = "Sesion Expired";
        if (clinic_login()) {
            $this->load->model("inbox_model");
            $response = $this->inbox_model->patient_autocomplete_model();
        }
        echo json_encode($response);
    }

    public function valid_ohip($ohip) {
        if ($ohip == "")
            return true;
        $parts = explode("-", $ohip);
        $this->form_validation->set_message('valid_ohip', 'Invalid OHIP Code.' .
                'Use OHIP Format : 1234-123-123-AB');
        if (sizeof($parts) != 4) {
            // hyphen check
            return false;
        }
        if (strlen($parts[0]) != 4 || strlen($parts[1]) != 3 || strlen($parts[2]) != 3 || strlen($parts[3]) != 2) {
            //length of each part
            return false;
        }
        if (!preg_match("/\d\d\d\d-\d\d\d-\d\d\d-[A-Z][A-Z]/", $ohip, $match)) {
            //check 
            return false;
        }
        return true;
    }

}
