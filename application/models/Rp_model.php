<?php

class Rp_model extends CI_Model {

    public function get_dash_info_model() {
        $data = $this->input->post();
        $fax = $this->session->userdata("fax_number");
        $id = $data["id"];
        $target = $data["target"];

        //log_message("error", "fax = " . $fax);
//                $this->check_referral_access($data["id"]);
        $this->db->select("md5(c_usr.id) as id, c_usr.clinic_institution_name");
        $this->db->from("clinic_user_info c_usr, referral_physician_info dr, referral_patient_info pat, clinic_referrals c_ref, efax_info efax");
        $this->db->where(array(
            "dr.fax" => $fax,
            "dr.active" => 1,
            "c_usr.active" => 1,
            "c_ref.active" => 1,
            "efax.active" => 1
        ));
        $this->db->where("pat.id", "dr.`patient_id`", false);
        $this->db->where("c_ref.id", "pat.referral_id", false);
        $this->db->where("efax.id", "c_ref.efax_id", false);
        $this->db->where("c_usr.id", "efax.`to`", false);
        $this->db->group_by("c_usr.id");
        $clinics = $this->db->get()->result();

        //log_message("error", "clinic inbox = " . $this->db->last_query());

        if ($id && $target == "clinic") {
            $clinic_code = $id;
            $this->db->select("md5(c_usr.id) as id, c_usr.clinic_institution_name");
            $this->db->from("clinic_user_info c_usr");
            $this->db->where(array(
                "c_usr.active" => 1,
                "md5(c_usr.id)" => $clinic_code
            ));
            $active_clinic = $this->db->get()->result()[0];
            $header = $active_clinic->clinic_institution_name;
            //log_message("error", "clinic get info query = " . $this->db->last_query());
            

            return array(
                "response" => "success",
                "clinics" => $clinics,
                "header" => $header,
                "clinic_id" => $id
            );
        } else if ($id && $target == "tracker") {
            $ref_code = $id;
            $this->db->select("concat(pat.fname, ' ', pat.lname) as name, pat.gender,"
                    . "DATE_FORMAT(now(), '%Y') - DATE_FORMAT(`pat`.`dob`, '%Y')-"
                    . "(DATE_FORMAT(now(), '00-%m-%d') < DATE_FORMAT(`pat`.`dob`, '00-%m-%d')) AS age, "
                    . "md5(efax.to) as clinic_id");
            $this->db->from("clinic_referrals c_ref, referral_patient_info pat, efax_info efax, referral_physician_info dr");
            $this->db->where(array(
                "c_ref.referral_code" => $ref_code,
                "c_ref.active" => 1,
                "pat.active" => 1,
                "dr.active" => 1,
                "efax.active" => 1
            ));
            $this->db->where("pat.id", "dr.patient_id", false);
            $this->db->where("c_ref.id", "pat.referral_id", false);
            $this->db->where("efax.id", "c_ref.efax_id", false);
            
            $patient_info = $this->db->get()->result()[0];
            $header = "$patient_info->name ( $patient_info->age year old $patient_info->gender )";
            $id = $patient_info->clinic_id;
            //data.patient.name + " (" + data.patient.age + " year old " + data.patient.gender + ")"
            //log_message("error", "pat info = " . json_encode($patient_info));
            //log_message("error", "pat info = " . $this->db->last_query());
            
            
            
            return array(
                "response" => "success",
                "clinics" => $clinics,
                "header" => $header,
                "clinic_id" => $id
            );
        }
        return array(
            "response" => "success",
            "clinics" => $clinics
        );
    }

    private function generate_random_string($length = 32) {
        $timestamp = time();

        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $timestamp . "_" . $randomString;
    }

}
