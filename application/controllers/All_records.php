<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class All_records extends CI_Controller { 
   public function index() {       
 if (clinic_login()) {        
    $data['page_content'] = $this->load->view('all_records_master', NULL, TRUE); 
           $data['page_title'] = "All Patient Records";    
        $data['jquery'] = $this->load->view('scripts/all_records_script', NULL, TRUE);   
         $this->load->view('main', $data);  
      } else { 
           redirect("/");  
      }  
  }   
 public function ssp_all_records() {   
     if (clinic_login()) {
            $this->load->model("all_records_model"); 
           $response = $this->all_records_model->ssp_all_records_model();  
          echo $response;   
     } else {   
         echo false;   
     }    
} 
   public function referral_details() {  
      log_message("error", "ref called");    
    if (clinic_login()) {    
        log_message("error", "ref called 2");   
         //check valid state   
         $this->load->model("all_records_model");    
        $response = $this->all_records_model->check_valid_referral_state_model();   
         if ($response == true) {  
              log_message("error", "ref called 3");  
              $data['page_content'] = $this->load->view('all_records_referral_details', NULL, TRUE);   
             $data['page_title'] = "Patient Details"; 
               $data['jquery'] = $this->load->view('scripts/all_records_referral_details_script', NULL, TRUE); 
               $this->load->view('main', $data); 
           } else {     
           log_message("error", "ref called 4");  
              //set error message (remaining)         
       redirect("all_records");   
         }   
     } else { 
           redirect("/");  
      } 
   }
}