<?php  defined('BASEPATH') OR exit('No direct script access allowed'); 
class Welcome extends CI_Controller {   
 public function index() {  
      //check login and redirect according to that   
     if (clinic_login()) {       
     redirect("workflow_dash");   
     } else if (referring_physician_login()) {     
       redirect("dashboard");       
 } else {         
   $this->load->view("signin_master");     
   }   
 }}