<?php

if (!defined('BASEPATH'))
    exit("Access Denied!");

class Signup extends CI_Controller {

    public function index() {

        if (clinic_login()) {

            redirect("patients");
        } else {

            $this->load->view("signup_master");
        }
    }

    public function register_new_user() {

        $this->load->model("signup_model");

        $response = $this->signup_model->register_user_model();

        if ($response === true) {

            // redirect("patients");

            echo "Verify Email Address Using Your Email Account within 24 hours from now.";
        } else {

            echo json_encode($response);

            $data["validation_errors"] = $response;

            $this->load->view("signup_master", $data, null);
        }
    }

    public function verify_physician() {

        $this->load->model("signup_model");

        $response = $this->signup_model->verify_physician_model();

        echo json_encode($response);
    }

    public function email_confirmation() {

        $this->load->model("signup_model");

        $response = $this->signup_model->email_confirmation_model();

        if ($response == true) {

            redirect("patients");
        } else {

            echo "Failed to Verify Your Email Address";
        }
    }

    //callback for verify CPSO number start here      
    // public function validcpso($cpso){
    // 	$curl_handle=curl_init();          
    // 	curl_setopt($curl_handle, CURLOPT_URL,'http://www.cpso.on.ca/public-register/doctor-details.aspx?view=1&id='.$cpso); 
    // 	curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);          
    // 	curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);          
    // 	$page_content = curl_exec($curl_handle);          
    // 	curl_close($curl_handle);          
    // 	log_message("error", "the page content = " . $page_content);
    // 	//check if not valid CPSO
    // 	if(strpos($page_content, "<h1>Page not found</h1>")){
    // 		echo "Invalid SPSO";
    // 		$this->form_validation->set_message('validcpso','Invalid CPSO number');
    // 		return FALSE;
    // 	}
    // 	//page not found
    // 	//cpso number, speciality , clinic , name
    // 	return true;          
    // 	if(strpos($tempvar,'<h2>Object moved to <a href') !== false) {                
    // 		//echo "Doc Not found";                  
    // 		return false;             
    // 	} 
    // 	else {                 
    // 		return true;                 
    // 		/*echo "CPSO==>".$cpso."<br>";                 
    // 		libxml_use_internal_errors( true);                 
    // 		$doc = new DOMDocument();                 
    // 		$doc->loadHTML($tempvar);                 
    // 		$xpath = new DOMXpath( $doc);                 
    // 		// A name attribute on a <div>???                 
    // 		$node = $xpath->query( '//h1[@class="grey"]')->item( 0);                 
    // 		echo $node->textContent; // This will print **GET THIS TEXT**                 
    // 		die(); */            	 
    // 	}      
    // }
}
