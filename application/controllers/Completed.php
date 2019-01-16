<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Completed extends CI_Controller {    public function index() {   
     if (clinic_login()) { 

           $data['page_content'] = $this->load->view('completed_master', NULL, TRUE);   
         $data['page_title'] = "All Patient Records";   
         $data['jquery'] = $this->load->view('scripts/completed_script', NULL, TRUE); 
           $this->load->view('main', $data);    
    } else {    
        redirect("/");     
   }    }    
public function ssp_completed() { 
       if (clinic_login()) {   
         $this->load->model("completed_model");  
          $response = $this->completed_model->ssp_completed_model(); 
           echo $response;        
} else {     
       echo false;  
     }   
 }   
 // *********************************************************************   
 // Completed Patient Details     
// *********************************************************************   
 public function referral_details() {   
     if (clinic_login()) {   
         //check valid state    
        $this->load->model("referral_model");    
        $response = $this->referral_model->check_valid_referral_state_model("Cancelled", "Declined");
            if ($response == true) { 
               $data['page_content'] = $this->load->view('completed_referral_details', NULL, TRUE); 
               $data['page_title'] = "Completed Referral";  
              $data['jquery'] = $this->load->view('scripts/completed_referral_details_script', NULL, TRUE);    
            $this->load->view('main', $data);   
         } else {  
              //set error message (remaining) 
               redirect("completed"); 
           }  
      } else {  
          redirect("/");   
     }   
 }   
 public function get_referral_dash_info() {  
      if (clinic_login()) {     
       $this->load->model("completed_model"); 
           $response = $this->completed_model->get_referral_dash_info_model();   
     } else {   
         $response = "Sesion Expired";  
      }    
    echo json_encode($response);  
  }
}