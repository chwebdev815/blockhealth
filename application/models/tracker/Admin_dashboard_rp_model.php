<?php

class Admin_dashboard_rp_model extends CI_Model {

    public function get_page_info_model() {
        $this->db->select("count(id) as new_referral_count")->from("clinic_referrals c_ref");
        $this->db->where("c_ref.create_datetime < ", "now()", false);
        $this->db->where("c_ref.create_datetime >= ", "date_sub(now(),INTERVAL 1 WEEK)", false);
        $new_referral_count = $this->db->get()->result()[0]->new_referral_count;
        
        //log_message("error", "sel 1 = " . $this->db->last_query());


        $accepted_referral_count = $this->db->select("count(id) as accepted_count")
                        ->from("count_accepted_referrals")
                        ->where(array(
                            "active" => 1
                        ))->get()->result()[0]->accepted_count;
        //log_message("error", "sel 2 = " . $this->db->last_query());

        $faxes_sent = $this->db->select("count(id) as faxes_sent")->from("count_sent_fax")->where(array(
                    "active" => 1
                ))->get()->result()[0]->faxes_sent;
        //log_message("error", "sel 3 = " . $this->db->last_query());

        $api_calls = $this->db->select("sum(data_points) as data_points")->from("count_data_points")->where(array(
                    "active" => 1
                ))->get()->result()[0]->data_points;
        //log_message("error", "sel 4 = " . $this->db->last_query());

        return array(
            "new_referral" => $new_referral_count,
            "accepted_referrals" => $accepted_referral_count,
            "faxes_sent" => $faxes_sent,
            "api_calls" => $api_calls
        );
    }

    public function ssp_rp_statistics_model() {
        $table = "view_tracker_rp";
        $primaryKey = "id";
        $columns = array(
            array('db' => 'physician_name', 'dt' => 0),
            array('db' => 'email_id', 'dt' => 1),
            array('db' => 'phone_number', 'dt' => 2),
            array('db' => 'fax_number', 'dt' => 3),
            array('db' => 'clinic_institution_name', 'dt' => 4),
            array('db' => 'signup_date', 'dt' => 5)
        );

        $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db' => $this->db->database,
            'host' => $this->db->hostname
        );
        $where = "1";
        require('assets/libraries/ssp.class.php');
        return json_encode(SSP::complex($_GET, $sql_details, $table, $primaryKey, $columns, null, $where));
    }
    
    public function get_graph_data_model() {
        $sql = "select date(a.`date`) as referral_date, count(t.id) as referral_count from(SELECT (CURDATE() - INTERVAL c.number DAY) AS date
            FROM (SELECT singles + tens  number FROM 
            ( SELECT 0 singles
            UNION ALL SELECT   1 UNION ALL SELECT   2 UNION ALL SELECT   3
            UNION ALL SELECT   4 UNION ALL SELECT   5 UNION ALL SELECT   6
            UNION ALL SELECT   7 UNION ALL SELECT   8 UNION ALL SELECT   9
            ) singles JOIN 
            (SELECT 0 tens
            UNION ALL SELECT  10 UNION ALL SELECT  20 UNION ALL SELECT  30
            ) tens

            ORDER BY number DESC) c  
            WHERE c.number BETWEEN 0 and 30)a
            left join clinic_referrals t
            on date(t.create_datetime) = a.date
            group by a.`date`
            order by a.date";
        $new_referral_data = $this->db->query($sql)->result();
        
        $sql = "select date(a.`date`) as referral_date, count(t.id) as referral_count from(SELECT (CURDATE() - INTERVAL c.number DAY) AS date
            FROM (SELECT singles + tens  number FROM 
            ( SELECT 0 singles
            UNION ALL SELECT   1 UNION ALL SELECT   2 UNION ALL SELECT   3
            UNION ALL SELECT   4 UNION ALL SELECT   5 UNION ALL SELECT   6
            UNION ALL SELECT   7 UNION ALL SELECT   8 UNION ALL SELECT   9
            ) singles JOIN 
            (SELECT 0 tens
            UNION ALL SELECT  10 UNION ALL SELECT  20 UNION ALL SELECT  30
            ) tens
            
            ORDER BY number DESC) c  
            WHERE c.number BETWEEN 0 and 30)a
            left join clinic_referrals t
            on date(t.accepted_datetime) = a.date and t.accepted_datetime IS NOT NULL and t.accepted_datetime <> ''
            group by a.`date`
            order by a.date";
        $accepted_data = $this->db->query($sql)->result();
        
        $sql = "select date(a.`date`) as referral_date, count(t.id) as faxes_sent 
            from(SELECT (CURDATE() - INTERVAL c.number DAY) AS date
            FROM (SELECT singles + tens  number FROM 
            ( SELECT 0 singles
            UNION ALL SELECT   1 UNION ALL SELECT   2 UNION ALL SELECT   3
            UNION ALL SELECT   4 UNION ALL SELECT   5 UNION ALL SELECT   6
            UNION ALL SELECT   7 UNION ALL SELECT   8 UNION ALL SELECT   9
            ) singles JOIN 
            (SELECT 0 tens
            UNION ALL SELECT  10 UNION ALL SELECT  20 UNION ALL SELECT  30
            ) tens

            ORDER BY number DESC) c  
            WHERE c.number BETWEEN 0 and 30)a
            left join count_sent_fax t
            on date(t.create_datetime ) = a.date 
            group by a.`date`
            order by a.date";
        $faxes_data = $this->db->query($sql)->result();
        
        $sql = "select date(a.`date`) as referral_date, sum(if(t.data_points, t.data_points, 0))  as data_points 
            from(SELECT (CURDATE() - INTERVAL c.number DAY) AS date
            FROM (SELECT singles + tens  number FROM 
            ( SELECT 0 singles
            UNION ALL SELECT   1 UNION ALL SELECT   2 UNION ALL SELECT   3
            UNION ALL SELECT   4 UNION ALL SELECT   5 UNION ALL SELECT   6
            UNION ALL SELECT   7 UNION ALL SELECT   8 UNION ALL SELECT   9
            ) singles JOIN 
            (SELECT 0 tens
            UNION ALL SELECT  10 UNION ALL SELECT  20 UNION ALL SELECT  30
            ) tens

            ORDER BY number DESC) c  
            WHERE c.number BETWEEN 0 and 30)a
            left join count_data_points t
            on date(t.create_datetime ) = a.date 
            group by a.`date`
            order by a.date";
        $data_points_data = $this->db->query($sql)->result();
        
        
        return array(
            "new_referral_count" => $new_referral_data,
            "accepted_data" => $accepted_data,
            "faxes_data" => $faxes_data,
            "data_points_data" => $data_points_data
        );

    }
    

    //
}
