<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Call_view extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('url');
    }

    public function index() {
        $this->load->view('call_view');
    }

    public function call() {
        $pname = $this->input->post('defaultContactFormName');
        $patient_lname = $this->input->post('patient_lname');
        $pvname = $this->input->post('defaultContactFormName2');
        $cname = $this->input->post('defaultContactFormName3');
        $aDate = $this->input->post('defaultContactFormName4');
        $aTime = $this->input->post('defaultContactFormName5');
        $mob = $this->input->post('defaultContactFormName6');
        $address = $this->input->post('address'); //"Unit 412 - 3075 Hospital Gate in Oakville, Ontario";
        $type = $this->input->post("type");
		$patient_lname = "Kumar";
        if (!empty($mob)) {
            $dataNew = $this->call_confirm($type, $mob, $pname, $patient_lname, $pvname, $cname, $aDate, $aTime, $address);
            echo "<pre>";
            print_r($dataNew);
        }
    }

    public function call_confirm($type, $to_number, $pname, $patient_lname, $pvname, $cname, $aDate, $aTime, $address) {

        $sid = 'AC2da3b84b65b63ccf4f05c27ac1713060';
        $token = '342a214ee959d16bf97ea87579016762';
        $twilio_number = "+13658000973";
        //$to = "+919876907251";  
        
        
//        $call_handle_file = "callhandle.php";
//        if($type == "visitCreate") {
//            $call_handle_file = "callhandle.php";
//        }
        
        $call_handle_file = "callhandle_new.php";
        if($type == "visitCreate") {
            $call_handle_file = "callhandle_new.php";
        }
        else if($type == "24hReminder") {
            $call_handle_file = "reminder_callhandle.php";
        }

        log_message("error", "Starting " . $call_handle_file . " for $type");

        $url = base_url() . "/call_view/callhandle?pname=" . urlencode($pname) . "&patient_lname=" . $patient_lname . "&pvname=" . urlencode($pvname) . "&cname=" . urlencode($cname) . "&aDate=" . urlencode($aDate) . "&aTime=" . urlencode($aTime) . "&address=" . urlencode($address);
        $uri = 'https://api.twilio.com/2010-04-01/Accounts/' . $sid . '/Calls.json';
        $auth = $sid . ':' . $token;
        $fields = '&Url=' . urlencode($url) .
                '&To=' . urlencode($to_number) .
                '&From=' . urlencode($twilio_number);
        $res = curl_init();
        curl_setopt($res, CURLOPT_URL, $uri);
        curl_setopt($res, CURLOPT_POST, 3);
        curl_setopt($res, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($res, CURLOPT_USERPWD, $auth);
        curl_setopt($res, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($res);
        $resp = json_decode($result);
        $status = curl_getinfo($res, CURLINFO_HTTP_CODE);
        echo "<pre>";
        print_r($resp);
        if (curl_errno($res)) {
            log_message("error", " Error – " . curl_error($res));
            return false;
        } else {
            log_message("error", "Calling");
            return true;
        }
    }

    public function call_resp() {
        //if(isset($_GET['pname'])){
        echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        echo "<Response>";
        echo "<Gather  NumDigits='1' action='" . base_url() . "call_view/call_resp_handle/' method='GET'>";
        echo "<Say>";
        echo "Hello         " . $_GET['pname'] . "
                Your      appointment   " . $_GET['pvname'] . "    with    " . $_GET['cname'] . "    has been booked for " . $_GET['aDate'] . "    at    " . $_GET['aDate'] . "    .The    address    is:    " . $_GET['address'] . "    Please     type    1    to    confirm    this    booking.    If    this    date    does   not     work,    please   type   2    to    alert    the    clinic    staff";
        echo "</Say>";
        echo "</Gather>";

        echo "</Response>";
        //}
    }

    public function call_resp_handle() {
        if (isset($_REQUEST['Digits'])) {
            /*    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
              if($_REQUEST['Digits'] == 1){
              echo "<Response><Say>You entered  " . $_REQUEST['Digits'] . " , Thank you </Say></Response>";
              }else{
              echo "<Response><Say>You entered wrong digit</Say></Response>";
              } */
            echo "<pre>";
            print_r($_REQUEST);
            echo "<pre>";
        }
    }

    //for wrong number
    public function vQee6Sn25pSzD6bDamgcfNvSq2NYHRhc() {

        if (isset($_REQUEST['data']) && isset($_REQUEST['to'])) {

            $data = $this->input->post();
            $From = $data["to"];
            // $Body = $data["data"];


            //remove + sign
            $From = substr($From, 2);

            // log_message("error", "body is 1 or 2 is => " . $Body);
            $this->db->select("DISTINCT(r_pv.id), r_pv.visit_confirmed, r_pv.notify_email");
            $this->db->from("referral_patient_info pat, records_patient_visit r_pv");
            $this->db->where(array(
                "pat.active" => 1,
                "r_pv.active" => 1,
                "pat.cell_phone" => $From
            ));
            $this->db->where("r_pv.patient_id", "pat.id", false);
            $result = $this->db->get()->result();

            log_message("error", "webhook sql = " . $this->db->last_query());

            $change_status = false;

            $this->db->trans_start();
            foreach ($result as $row) {
                //change status to confirm
                $this->db->where(array(
                    "id" => $row->id
                ));
                // if()//Confirmed by email (wrong number)
                // if($row->notify_email === "1") {
                //     $this->db->set("visit_confirmed", "Confirmed by email (wrong number)");
                // } else {
                    $this->db->set("visit_confirmed", "Wrong Number");
                // }
                
                $this->db->set("notify_voice", "0");
                $this->db->set("notify_sms", "0");
                
                $this->db->update("records_patient_visit");
                $change_status = true;
                log_message("error", "change (1) " . $this->db->last_query());
            }
            $this->db->trans_complete();
        }
    }
	
	public function callhandle(){
		if(isset($_GET['pname'])){
		$address = $_GET['address'];
		$dataarray = http_build_query($_GET);
		$base_url = "http://35.203.47.37";

		echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		echo "<Response>
				<Gather  timeout='3' numDigits='1' action='$base_url/twiml/callhandle.php?pname=" . urlencode($_GET['pname']) . "&amp;patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;pvname=" . urlencode($_GET['pvname']) . "&amp;cname=" . urlencode($_GET['cname']) . "&amp;aDate=" . urlencode($_GET['aDate']) . "&amp;aTime=" . urlencode($_GET['aTime']) . "&amp;address=" . urlencode($_GET['address']) . "' method='GET'>
						<Say  voice='Polly.Joanna'> Hello </Say>
						<Pause length='1'/>
						<Say voice='Polly.Joanna'> This is an automated appointment call for  " . $_GET['pname'] . "  " . $_GET['patient_lname'] . ".</Say>
						<Pause length='1'/>
						<Say voice='Polly.Joanna'> If you are  " . $_GET['pname'] . "  " . $_GET['patient_lname'] . " , please enter 1 to continue. If this is the wrong number, please type 2 to end the call</Say>
				</Gather>
					<Pause length='10'/>
					<Redirect method='GET'>
					$base_url/twiml/callhandle_new.php?pname=" . urlencode($_GET['pname']) . "&amp;patient_lname=" . urlencode($_GET['patient_lname']) . "&amp;pvname=" . urlencode($_GET['pvname']) . "&amp;cname=" . urlencode($_GET['cname']) . "&amp;aDate=" . urlencode($_GET['aDate']) . "&amp;aTime=" . urlencode($_GET['aTime']) . "&amp;address=" . urlencode($_GET['address']) . "&amp;Digits=timeout</Redirect>
		</Response>";
	   }
	}

}
