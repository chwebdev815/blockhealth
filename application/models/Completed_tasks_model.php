<?php

class Completed_tasks_model extends CI_Model {

    public function ssp_completed_tasks_model() {
        $table = "view_completed_tasks";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'patient_name', 'dt' => 0),
            array('db' => 'record_type', 'dt' => 1),
            array('db' => 'notes', 'dt' => 2),
            array('db' => 'pages', 'dt' => 3),
            array('db' => 'id', 'dt' => 4),
            array('db' => 'pdf_file', 'dt' => 5),
            array('db' => 'tiff_file', 'dt' => 6),
            array('db' => 'sender_fax_number', 'dt' => 7),
            array('db' => 'task_date_time', 'dt' => 8)
        );

        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        $where = "";
        if(clinic_admin_login()) {
            $where = "assigned_to = 0 and clinic_id =" . $this->session->userdata("user_id");
        }
        else if(clinic_physician_login()) {
            $where = "assigned_to <> 0 and assigned_to = ".$this->session->userdata("user_id");
        }
        
        require('ssp.class.php');
        return json_encode(
            SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
        );
    }
    
    
    public function update_task_model() {
        //log_message("error", "updating task");
        $this->form_validation->set_rules('task_id', 'Task ID', 'required');
        $this->form_validation->set_rules('record_type', 'Record Type', 'required');
//        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('assign_physician', 'Physician', 'required');
        if ($this->form_validation->run()) {
            $this->db->trans_start();
            $data = $this->input->post();
            $task_id = get_decrypted_id($data["task_id"], "clinic_physician_tasks");
            
            $physician_id = ((isset($data["assign_physician"])) ? get_decrypted_id($data["assign_physician"], "clinic_physician_info") : 0);
            $patient_id = ((isset($data["id"])) ? (get_decrypted_id($data["id"], "referral_patient_info")) : 0);
            
            $task_info = $this->db->select("pdf_file, tiff_file")->from("clinic_physician_tasks")->where(array(
                "id" => $task_id
            ))->get()->result();
            
            $this->db->where(array(
                "id" => $task_id
            ));
            $updated = $this->db->update("clinic_physician_tasks", array(
                "clinic_id" => $this->session->userdata("user_id"),
                "assigned_to" => $physician_id,
                "patient_id" => $patient_id,
                "record_type" => $data["record_type"],
                "notes" => $data["description"]
            ));
            //log_message("error", "update = " . $this->db->last_query());

            if ((isset($data["id"])) && $data["id"] != "") {
                $new_file_name = generate_random_string(32);
                $inserted = $this->db->insert("records_clinic_notes", array(
                    "patient_id" => $patient_id,
                    "physician" => "Admin",
                    "record_type" => $data["record_type"],
                    "description" => $data["description"],
                    "record_file" => $new_file_name
                ));
                
                copy("./uploads/physician_tasks/pdf/" . $task_info[0]->pdf_file, "./uploads/health_records/" . $new_file_name . ".pdf");
                //log_message("error", "task record => " . $task_id . " => ./uploads/physician_tasks/pdf" . $task_info[0]->pdf_file ." to ./uploads/health_records/" . $new_file_name . ".pdf");
            
            }
           
            $this->db->trans_complete();
            if ($updated) {
                return array(
                    "result" => "success"
                );
            } else {
                return array(
                    "result" => "error",
                    "msg" => "Failed to update task"
                );
            }
        } else {
            return array(
                "result" => "error",
                "msg" => validation_errors()
            );
        }
    }
    
    public function task_completed_model() {
        //log_message("error", "updating task");
        $this->form_validation->set_rules('id', 'Task ID', 'required');
        if ($this->form_validation->run()) {
            $this->db->trans_start();
            $data = $this->input->post();
            
            $this->db->where(array(
                "md5(id)" => $data["id"]
            ));
            $updated = $this->db->update("clinic_physician_tasks", array(
                "active" => 0
            ));
            $this->db->trans_complete();
            //log_message("error", "remove tasks => " . $this->db->last_query());
            if($updated) {
                return array(
                    "result" => "success"
                );
            }
            else {
                return array(
                    "result" => "error",
                    "msg" => "Failed to remove task"
                );
            }
        } else {
            return array(
                "result" => "error",
                "msg" => validation_errors()
            );
        }
    }
}
