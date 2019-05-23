<?php

class My_tasks_model extends CI_Model {

    public function ssp_my_tasks_model() {
        $table = "view_my_tasks";
        if ($this->session->userdata("emr_pathway") === "true") {
            $table = "view_my_tasks_emr";
        }
        $primaryKey = "id";
        $columns = array(
            array('db' => 'patient_name', 'dt' => 0),
            array('db' => 'record_type', 'dt' => 1),
            array('db' => 'notes', 'dt' => 2),
            array('db' => 'status', 'dt' => 3),
            array('db' => 'id', 'dt' => 4),
            array('db' => 'pdf_file', 'dt' => 5),
            array('db' => 'tiff_file', 'dt' => 6),
            array('db' => 'sender_fax_number', 'dt' => 7),
            array('db' => 'task_date_time', 'dt' => 8),
            array('db' => 'patient_id', 'dt' => 9)
        );

        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        $where = "";
        if (clinic_admin_login()) {
            $where = "assigned_to = 0 and clinic_id =" . $this->session->userdata("user_id");
        } else if (clinic_physician_login()) {
            $where = "assigned_to <> 0 and assigned_to = " . $this->session->userdata("user_id");
        }

        require('ssp.class.php');
        return json_encode(
                SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where)
        );
    }

    public function update_task_model() {
        log_message("error", "updating task");
        $this->form_validation->set_rules('id', 'Patient ID', 'required');
        $this->form_validation->set_rules('task_id', 'Task ID', 'required');
        $this->form_validation->set_rules('record_type', 'Record Type', 'required');
//        $this->form_validation->set_rules('description', 'Description', 'required');
        $this->form_validation->set_rules('assign_physician', 'Physician', 'required');
        if ($this->form_validation->run()) {
            $this->db->trans_start();
            $data = $this->input->post();
            $task_id = get_decrypted_id($data["task_id"], "clinic_physician_tasks");
            log_message("error", "patient dropdown = " . $data["patient_dropdown"]);
            if ($data["patient_dropdown"] != "0") {
                log_message("error", "inside dropdown");
                $clinic_id = $this->session->userdata("user_id");
                //if patient is assigned
                $task_info = $this->db->select("patient_id, pdf_file, tiff_file")
                                ->from("clinic_physician_tasks")
                                ->where(array(
                                    "id" => $task_id
                                ))->get()->result();
                if (!$task_info) {
                    return array(
                        "result" => "error",
                        "msg" => "Patient info not found"
                    );
                }
                $patient_id = $task_info[0]->patient_id;
                // delete tiff
                $tiff_file = files_dir() . md5($clinic_id) . "/" . md5($patient_id) . "/" . 
                        $task_info[0]->tiff_file;
                log_message("error", "deleting tiff = >" . $tiff_file);
                unlink($tiff_file);
                // set pdf as doc for patient selected
//                
                $new_patient_id = get_decrypted_id($data["patient_dropdown"], "referral_patient_info");
                log_message("error", "new patient id = " . $new_patient_id);
                //insert health record
                $inserted = $this->db->insert("records_clinic_notes", array(
                    "patient_id" => $new_patient_id,
                    "record_type" => "Saved Referral",
                    "physician" => $data["assign_physician"],
                    "description" => $data["description"],
                    "record_file" => $task_info[0]->pdf_file
                ));
                
                log_message("error", "inserting = > " . $this->db->last_query());

                //remove record from fax triage
                $updated = $this->db->where("id", $task_id)
                        ->update("clinic_physician_tasks", array(
                    "active" => 0
                ));
                
                log_message("error", "updating = > " . $this->db->last_query());
                
                if ($inserted && $updated) {
                    $this->db->trans_complete();
                    return array(
                        "result" => "success"
                    );
                } else {
                    return array(
                        "result" => "error",
                        "message" => "Request not completed."
                    );
                }
            } else {

                //if patient not assigned


                $physician_id = ((isset($data["assign_physician"])) ? get_decrypted_id($data["assign_physician"], "clinic_physician_info") : 0);
                $patient_id = ((isset($data["id"])) ? (get_decrypted_id($data["id"], "referral_patient_info")) : 0);

                $task_info = $this->db->select("pdf_file, tiff_file")
                                ->from("clinic_physician_tasks")
                                ->where(array(
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
                log_message("error", "update = " . $this->db->last_query());

                if ((isset($data["id"])) && $data["id"] != "") {
//                $new_file_name = generate_random_string(32);
                    $inserted = $this->db->insert("records_clinic_notes", array(
                        "patient_id" => $patient_id,
                        "physician" => "Admin",
                        "record_type" => $data["record_type"],
                        "description" => $data["description"],
                        "record_file" => $task_info[0]->pdf_file
                    ));
//                copy(files_dir() . "$clinic_id/" . md5($patient_id) . "/" . $task_info[0]->pdf_file, "./uploads/health_records/" . $new_file_name . ".pdf");
//                log_message("error", "task record => " . $task_id . " => ./uploads/physician_tasks/pdf" . $task_info[0]->pdf_file ." to ./uploads/health_records/" . $new_file_name . ".pdf");
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
            }
        } else {
            return array(
                "result" => "error",
                "msg" => validation_errors()
            );
        }
    }

    public function task_completed_model() {
        log_message("error", "updating task");
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
            log_message("error", "remove tasks => " . $this->db->last_query());
            if ($updated) {
                return array(
                    "result" => "success"
                );
            } else {
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

    public function fetch_task_details_model() {
        log_message("error", "fetch_task_details_model");
        $this->form_validation->set_rules('id', 'Task ID', 'required');
        if ($this->form_validation->run()) {

            $data = $this->input->post();

            $task_data = $this->db->select("tsk.record_type, tsk.notes, md5(dr.id) as id,"
                                    . "pat.fname, pat.lname, pat.dob, pat.ohip, pat.gender")->
                            from("clinic_physician_tasks tsk")->
                            join("clinic_physician_info dr", "tsk.assigned_to = dr.id and dr.active = 1", "LEFT")->
                            join("referral_patient_info pat", "tsk.patient_id = pat.id and pat.active = 1", "LEFT")->
                            where(array(
                                "md5(tsk.id)" => $data["id"],
                                "tsk.active" => 1
                            ))->get()->result();

            log_message("error", "remove tasks => " . $this->db->last_query());
            if ($task_data) {
                return array(
                    "result" => "success",
                    "data" => $task_data
                );
            } else {
                return array(
                    "result" => "error",
                    "msg" => "Failed to fetch task details"
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
