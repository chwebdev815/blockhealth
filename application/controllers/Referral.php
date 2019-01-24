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
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function update_patient() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->update_patient_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function update_physician() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->update_physician_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function cancel_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->cancel_referral_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function decline_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->decline_referral_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function accept_admin_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->accept_admin_referral_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function missing_items_details() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->missing_items_details_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function request_missing_items() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->request_missing_items_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function accept_physician_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->accept_physician_referral_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function complete_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->complete_referral_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function get_clinic_physicians() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_clinic_physicians_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function assign_physician() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->assign_physician_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function set_priority() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->set_priority_model();
        } else {
            $response = "Session Expired";
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
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function add_health_record() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->add_health_record_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function add_admin_note() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->add_admin_note_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function add_patient_visit() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->add_patient_visit_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function update_patient_visit() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->update_patient_visit_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function cancel_patient_visit() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->cancel_patient_visit_model();
        } else {
            $response = "Session Expired";
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
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function get_admin_notes_info() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_admin_notes_info_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function get_patient_visit_info() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->get_patient_visit_info_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function confirm_referral() {
        if (clinic_login()) {
            $this->load->model("referral_model");
            $response = $this->referral_model->confirm_referral_model();
        } else {
            $response = "Session Expired";
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
            $response = "Session Expired";
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

    private function check_visit_allocation($start, $end) {
        echo "checking $start and $end <br/>";
//        $str_cur_visit_date = substr($current_visit->visit_start_time, 0, 8);
//        $str_next_visit_date = substr($next_visit->visit_start_time, 0, 8);
//
//        //if two visit are from same day
//        if ($str_cur_visit_date === $str_next_visit_date) {
//            //check if visit can be scheduled between 2 visits
//            $cur_visit_end_time = new DateTime($current_visit->visit_end_time);
//            $next_visit_start_time = new DateTime($next_visit->visit_start_time);
//            $gap = $cur_visit_end_time->diff($next_visit_start_time);
////                    echo json_encode($gap);
//            $gap_in_minutes = ($gap->h * 60) + $gap->i;
//            if ($gap_in_minutes > $visit_duration) {
//                //assign a block here
//                $assign_start_time = $cur_visit_end_time->format("Y-m-d H:i:s");
//                $assign_end_time = date('Y-m-d H:i:s', strtotime("+$visit_duration minute", strtotime($cur_visit_end_time->format("Y-m-d H:i:s"))));
//                $new_slots[] = array(
//                    "start" => $assign_start_time,
//                    "end" => $assign_end_time
//                );
//                echo "assign $assign_start_time to $assign_end_time <br/>";
//
//                $assigned_date = $cur_visit_end_time->format("Y-m-d");
//                for ($j = $i + 1; $j < $visits_count; $j++) {
//                    $start_datetime = new DateTime($visits_booked[$j]->visit_start_time);
//                    if ($start_datetime->format("Y-m-d") === $assigned_date) {
//                        $i++;
//                        echo "skipped $assigned_date <br/>";
//                    }
//                }
//                //now skip next visits for assigned date
//            }
//        } else {
//            echo $str_cur_visit_date;
//            echo "different date encountered <br/>";
//        }
    }

    public function assign_slots() {
        $next_day = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', strtotime("+1 day")));

        $visits_booked = $this->db->select(
                                "concat(visit_date, ' ', visit_time) as visit_start_time, "
                                . "concat(visit_date, ' ', visit_end_time) as visit_end_time")
                        ->from("records_patient_visit")->where(array(
                    "active" => 1,
                    "visit_date >= " => $next_day->format('Y-m-d')
                ))->order_by("1")->get()->result();

//        echo json_encode($visits_booked) . "<br/><br/>";
//        echo $this->db->last_query() . "<br/><br/>";

        $new_visit_duration = 30;

        $available_visit_slots = array();

        $day = $next_day;
        $index = 1;
        do {
            //for each day
            //echo "*** day = " . json_encode($day) . "<br/>";
            $scheduling_day = $this->check_day_availability($day);
            $day_assigned = false;

            if ($scheduling_day["available"]) {
                //echo "is available <br/>";
                $day_start_time = $scheduling_day["day_start_time"];
                $day_end_time = $scheduling_day["day_end_time"];

                //echo "day times = $day_start_time and $day_end_time <br/>";

                $processed_keys = 0;
                $time1 = $scheduling_day["day"] . " " . $day_start_time;

                $visits_booked_for_day = $this->get_visit_booked_for_day($day, $visits_booked);

                if (sizeof($visits_booked_for_day) != 0) {
                    //echo "visits_booked_for_day has visits <br/>";
                    for ($key = 0; $key < sizeof($visits_booked_for_day) && !$day_assigned; $key++) {
                        //echo "inside for loop <br/>";
                        $processed_keys = $key;

                        $time2 = $visits_booked_for_day[$key]->visit_start_time;
                        $slot_response = $this->time_slot_available($time1, $time2, $new_visit_duration);
                        if ($slot_response["available"]) {
                            $available_visit_slots[] = array(
                                "start_time" => $slot_response["start_time"],
                                "end_time" => $slot_response["end_time"]
                            );
                            $day_assigned = true;
                            break; //for loop break
                        } else {
                            //check for next visit
                            $time1 = $visits_booked_for_day[$key]->visit_end_time;
                        }
                    }
                } else {
                    //echo "visits_booked_for_day has no visits <br/>";
                    $time2 = $scheduling_day["day"] . " " . $day_end_time;

                    $slot_response = $this->time_slot_available($time1, $time2, $new_visit_duration);
                    //echo "response from slot = " . json_encode($slot_response) . "<br/>";
                    if ($slot_response["available"]) {
                        $new_visit = array(
                            "start_time" => $slot_response["start_time"],
                            "end_time" => $slot_response["end_time"]
                        );
                        $available_visit_slots[] = $new_visit;
                        //echo " =====> assigned to " . json_encode($new_visit) . "<br/>";
                    }
                }
            } else {
                echo "is not available <br/>";
            }
            $day = $day->modify('+1 day');

            $index++;
        } while (sizeof($available_visit_slots) < 3 || $index > 100);


        //echo "<br/> ============================================================= <br/>";
        echo json_encode($available_visit_slots);
    }

    private function time_slot_available($time1, $time2, $new_visit_duration) {
        //echo "### called time_slot_available" . "<br/>";
        //echo json_encode($time1) . "<br/>";
        //echo json_encode($time2) . "<br/>";

        $datetime1 = DateTime::createFromFormat('Y-m-d H:i:s', $time1);
        $datetime2 = DateTime::createFromFormat('Y-m-d H:i:s', $time2);


        $gap = $datetime1->diff($datetime2);
        //echo "gap = " . json_encode($gap) . "<br/>";
        $gap_in_minutes = ($gap->h * 60) + $gap->i;

        if ($gap_in_minutes > $new_visit_duration) {
            $response = array(
                "available" => true,
                "start_time" => $datetime1->format("Y-m-d H:i:s"),
                "end_time" => $datetime1->add(new DateInterval("PT".$new_visit_duration."M"))->format("Y-m-d H:i:s")
            );
        } else {
            $response = array(
                "available" => false
            );
        }
        return $response;
    }

    private function day_of($visit) {
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $visit->visit_start_time)->format("Y-m-d");
        return $date;
    }

    private function get_visit_booked_for_day($day, $visits_booked) {
//        echo json_encode($visits_booked) . "<br/>" . json_encode($day);
        //echo "### called get_visit_booked_for_day <br/>";
        $visits_booked_for_day = array();
        foreach ($visits_booked as $key => $value) {
            $visit_day = DateTime::createFromFormat('Y-m-d H:i:s', $value->visit_start_time)->format("Y-m-d");
            if ($visit_day === $day->format("Y-m-d")) {
                $visits_booked_for_day[] = $value;
            }
        }
        //echo json_encode($visits_booked_for_day) . "<br/>";
        return $visits_booked_for_day;
    }

    private function check_day_availability($day) {
        if ($this->check_for_specific_leaves($day)) {
            $availability_response = $this->check_for_weekend_days($day);
            if ($availability_response["available"]) {
                return $availability_response;
            }
        }
        return array(
            "available" => false
        );
    }

    private function check_for_weekend_days($day) {
        //convert day to weekday name
        //echo "### called check_for_weekend_days <br/>";
//        echo "day = " . json_encode($day) . "<br/>";

        $weekday_name = strtolower($day->format('D'));
        $day = strtolower($day->format('Y-m-d'));
        $data = $this->db->select("$weekday_name as available, start_time, end_time")->from("schedule_visit_settings")->where(array(
                    "clinic_id" => 1, //convert to session then
                    "active" => "yes"
                ))->get()->result();

//        echo json_encode($day) . "<br/>";
//        echo $this->db->last_query() . "<br/>";

        if ($data) {
            if ($data[0]->available === "yes") {
                $response = array(
                    "available" => true,
                    "day_start_time" => $data[0]->start_time,
                    "day_end_time" => $data[0]->end_time,
                    "day" => $day
                );
            } else {
                $response = array(
                    "available" => false
                );
            }
        } else {
            $response = array(
                "available" => false
            );
        }
        //echo "response = " . json_encode($response) . "<br/>";
        return $response;
    }

    private function check_for_specific_leaves($day) {
        return true;
    }

//        $visits_count = sizeof($visits_booked);
//        
//        for ($i=0; $i < $visits_count; $i++) {
//            $current_visit = $visits_booked[$i];
//            $key = $i;
//            
//            $next_key = $key + 1;
//            if (isset($visits_booked[$next_key])) {
//                $next_visit = $visits_booked[$next_key];
//                $str_cur_visit_date = substr($current_visit->visit_start_time, 0, 8);
//                $str_next_visit_date = substr($next_visit->visit_start_time, 0, 8);
//
//                //if two visit are from same day
//                if ($str_cur_visit_date === $str_next_visit_date) {
//                    //check if visit can be scheduled between 2 visits
//                    $cur_visit_end_time = new DateTime($current_visit->visit_end_time);
//                    $next_visit_start_time = new DateTime($next_visit->visit_start_time);
//                    $gap = $cur_visit_end_time->diff($next_visit_start_time);
////                    echo json_encode($gap);
//                    $gap_in_minutes = ($gap->h * 60) + $gap->i;
//                    if ($gap_in_minutes > $visit_duration) {
//                        //assign a block here
//                        $assign_start_time = $cur_visit_end_time->format("Y-m-d H:i:s");
//                        $assign_end_time = date('Y-m-d H:i:s', strtotime("+$visit_duration minute", strtotime($cur_visit_end_time->format("Y-m-d H:i:s"))));
//                        $new_slots[] = array(
//                            "start" => $assign_start_time,
//                            "end" => $assign_end_time
//                        );
//                        echo "assign $assign_start_time to $assign_end_time <br/>";
//                        
//                        $assigned_date = $cur_visit_end_time->format("Y-m-d");
//                        for($j = $i + 1; $j<$visits_count; $j++) {
//                            $start_datetime = new DateTime($visits_booked[$j]->visit_start_time);
//                            if($start_datetime->format("Y-m-d") === $assigned_date) {
//                                $i++;
//                                echo "skipped $assigned_date <br/>";
//                            }
//                        }
//                        //now skip next visits for assigned date
//                        
//                    }
//                } else {
//                    echo $str_cur_visit_date;
//                    echo "different date encountered <br/>";
//                }
//            }
//        }
}
