<?php

class Clinic_model extends CI_Model {
    
    public function ssp_clinic_referrals_model() {
        $table = "clinic_referrals_dash";
        $primaryKey = "referral_code";
        
        $columns = array(
            array('db' => 'name', 'dt' => 0),
            array('db' => 'patient_status', 'dt' => 1),
            array('db' => 'days_received', 'dt' => 2),
            array('db' => 'referral_code', 'dt' => 3)
        );
        
        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        
        $where = "clinic_id = '".$this->uri->segment(3)."' and sender_fax_number = '".$this->session->userdata("fax_number")."'";
        //log_message("error", "where = " . $this->db->last_query());
        require('ssp.class.php');
        return json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where));
    }   
}
