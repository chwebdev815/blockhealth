<?php

if (!defined('BASEPATH'))
    exit("Access Denied!");

class Efax extends CI_Controller {

    public function check() {
        $db_predict = $this->load->database('predictions', TRUE);
        $row_count = $db_predict->select("count(*) as row_counts")
                ->from("stored_physicians")->get()->result();
        echo json_encode($row_count);
        
        echo CI_VERSION;
        
    }

    public function get_images() {

        //Data, connection, auth
        $soapUrl = "38.104.251.164/softlinx/replixfax/wsapi"; // asmx URL of WSDL
        $soapUser = "4162669449";  //  username
        $soapPassword = "HwDH3zvK"; // password
        // xml post structure

        $xml_post_string = '<?xml version="1.0" encoding="utf-8"?>            
                            <soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rep="http://www.softlinx.com/ReplixFax">
                            <soapenv:Header/>
                            <soapenv:Body>
                               <rep:GetReceiveFaxContent>
                                  <GetReceiveFaxContentInput>
                                     <FaxId>24224650</FaxId>
                                     <FaxContentType>pdf</FaxContentType>
                                  </GetReceiveFaxContentInput>
                               </rep:GetReceiveFaxContent>
                            </soapenv:Body>
                         </soapenv:Envelope>';

        $headers = array(
            "Content-Transfer-Encoding: binary",
            "Accept-Ranges: bytes",
            "Cache-Control: no-cache",
            "Content-Encoding: none",
            "Pragma: no-cache",
            "SOAPAction: http://www.softlinx.com/wsapi/op=GetReceiveFaxContent/ver=66",
            "Content-length: " . strlen($xml_post_string),
        ); //SOAPAction: your op URL
        $filename = 'recv-fax-24224650.pdf';
        header('Content-Type: application/.pdf');
        header('Content-Disposition: attachment; filename=' . $filename . '.pdf');


        $url = $soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_post_string");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $soapUser . ":" . $soapPassword);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        $response = curl_exec($ch);

//        echo "file = " . json_encode($response);
//        exit();
//        file_put_contents("hello.pdf", base64_decode($response));
//        exit();
//
        $destination = dirname(_FILE_) . '/' . $filename . '.pdf';
        $file = fopen($destination, "w+");
        fputs($file, $response);
        fclose($file);
        readfile($destination);
//
//
//        exit();
//
//        // converting
//        $response = curl_exec($ch);
//        curl_close($ch);
//
//
//        file_put_contents("xyz.pdf", $response);
//
//        exit();
//
//        die;
    }

    public function index() {

        //Data, connection, auth
        $soapUrl = "38.104.251.164/softlinx/replixfax/wsapi"; // asmx URL of WSDL
        $soapUser = "4162669449";  //  username
        $soapPassword = "HwDH3zvK"; // password
        // xml post structure

        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:rep="http://www.softlinx.com/ReplixFax">
                            <soapenv:Header/>
                            <soapenv:Body>
                               <rep:QueryReceiveFax>
                                  <QueryReceiveFaxInput>



                                  </QueryReceiveFaxInput>
                               </rep:QueryReceiveFax>
                            </soapenv:Body>
                         </soapenv:Envelope>';   // data from the form, e.g. some ID number

        $headers = array(
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: http://www.softlinx.com/wsapi/op=QueryReceiveFax/ver=66",
            "Content-length: " . strlen($xml_post_string),
        ); //SOAPAction: your op URL

        $url = $soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $soapUser . ":" . $soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        curl_close($ch);

        print_r($response);
        exit();
    }

    public function send_referral_efax() {
        if (clinic_login()) {
            $this->load->model("efax_model");
            $response = $this->efax_model->send_referral_efax_model();
        } else {
            $response = "Session Expired";
        }
        echo json_encode($response);
    }

    public function delete_today_log() {
        $date = date("Y-m-d");
        $this->reset_logs();
    }

    public function reset_logs() {
        $this->load->helper('file');
        delete_files(APPPATH . 'logs/');
        $default_403_forbidden = file_get_contents(APPPATH . 'index.html');
        write_file(APPPATH . 'logs/index.html', $default_403_forbidden);
    }

    public function call_handle() {
        // echo "call handle" . json_encode($this->input->post());
        //log_message("error", "from call handeling => " . json_encode($this->input->post()));

        $data = $this->input->post();
        $From = $data["to"];
        $Body = $data["data"];


        //remove + sign
        $From = substr($From, 2);

        log_message("error", "body is 1 or 2 is => " . $Body);
        $this->db->select("DISTINCT(r_pv.id), r_pv.visit_confirmed");
        $this->db->from("referral_patient_info pat, records_patient_visit r_pv");
        $this->db->where(array(
                    "pat.active" => 1,
                    "r_pv.active" => 1
                ))->group_start()
                ->or_group_start()->where(array(
                    "pat.cell_phone" => $From
                ))->group_end()
                ->or_group_start()->where(array(
                    "pat.work_phone" => $From
                ))->group_end()
                ->or_group_start()->where(array(
                    "pat.home_phone" => $From
                ))->group_end()
                ->group_end();
        $this->db->where("r_pv.patient_id", "pat.id", false);
        $result = $this->db->get()->result();

        //log_message("error", "webhook sql = " . $this->db->last_query());

        $change_status = false;

        $this->db->trans_start();
        foreach ($result as $row) {
            //log_message("error", "row = " . json_encode($row) . "with body = " . $Body . ", status = " . $row->visit_confirmed);
            if ($Body == "1") {
                //change status to confirm
                $this->db->where(array(
                    "id" => $row->id
                ));
                $this->db->set("visit_confirmed", "Awaiting Confirmation");
                $this->db->update("records_patient_visit");
                $change_status = true;
                //log_message("error", "change (1) " . $this->db->last_query());
            }
            if ($Body == "2") {
                //change status to Change required
                $this->db->where(array(
                    "id" => $row->id
                ));
                $this->db->set("visit_confirmed", "Change required");
                $this->db->update("records_patient_visit");
                $change_status = true;
                //log_message("error", "change (2) " . $this->db->last_query());
            }
        }
        $this->db->trans_complete();
    }

}
