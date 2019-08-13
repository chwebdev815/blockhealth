<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Referral extends CI_Controller {

    public function fetch_dashboard_counts() {
        $this->load->model("referral_model");
        $response = $this->referral_model->fetch_dashboard_counts_model();
        echo json_encode($response);
    }

    public function search_patient() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->search_patient_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function update_patient() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->update_patient_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function update_physician() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->update_physician_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function cancel_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->cancel_referral_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function decline_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->decline_referral_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function set_next_visit() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->set_next_visit_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function accept_admin_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->accept_admin_referral_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function missing_items_details() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->missing_items_details_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function request_missing_items() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->request_missing_items_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function accept_physician_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->accept_physician_referral_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function complete_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->complete_referral_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_clinic_physicians() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_clinic_physicians_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function assign_physician() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->assign_physician_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function set_priority() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->set_priority_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    //*********************************
    //  Record Management Functions
    //********************************
    public function update_checklist_item() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->update_checklist_item_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function add_health_record() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->add_health_record_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function add_admin_note() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->add_admin_note_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function add_patient_visit() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->add_patient_visit_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function update_patient_visit() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->update_patient_visit_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function cancel_patient_visit() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->cancel_patient_visit_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    // records table SSP
    public function ssp_health_records() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->ssp_health_records_model();
            echo $response;
        } else {
            echo false;
        }
    }

    public function ssp_admin_notes() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->ssp_admin_notes_model();
            echo $response;
        } else {
            echo false;
        }
    }

    public function ssp_patient_visits() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->ssp_patient_visits_model();
            echo $response;
        } else {
            echo false;
        }
    }

    public function get_health_record_info() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_health_record_info_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_admin_notes_info() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_admin_notes_info_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_patient_visit_info() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_patient_visit_info_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_visit_allocation_for_manual_visit() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_visit_allocation_for_manual_visit_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function confirm_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->confirm_referral_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function is_patient_scheduled() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->is_patient_scheduled_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_patient_visit_calendar_month_view() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_patient_visit_calendar_month_view_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_patient_visit_calendar_week_view() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_patient_visit_calendar_week_view_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function get_location_and_custom() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_location_and_custom_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function set_patient_location() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->set_patient_location_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function set_custom() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->set_custom_model();
        } else {
            $response = session_expired();
        }
        echo json_encode($response);
    }

    public function confirm_visit_key() {
        $this->load->model("referral_model");
        $response = $this->referral_model->confirm_visit_key_model();
    }

    public function log_data_points() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->log_data_points_model();
        } else {
            $response = session_expired();
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

    public function migrate_GUVuZeXSz9x85BdcS2TJu83Yo9BaR1pK() {
        $this->load->library('migration');

        if (!$this->migration->current()) {
            show_error($this->migration->error_string());
        }
        else {
            echo "<h2>Migration completed successfully</h2>";
        }
    }

}
