<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_triage extends CI_Controller {  
  public function index() {  
      if (clinic_login()) {  
          $data['page_content'] = $this->load->view('admin_triage_master', NULL, TRUE);   
         $data['page_title'] = "Admin Triage";    
        $data['jquery'] = $this->load->view('scripts/admin_triage_script', NULL, TRUE);      
      $this->load->view('main', $data); 
       } else {    
        redirect("/");
        }   
 }   
 public function ssp_admin_triage() { 
       if (clinic_login()) {
            $this->load->model("admin_triage_model");      
      $response = $this->admin_triage_model->ssp_admin_triage_model();  
          echo $response;   
     } else {   
         echo false;      
  }  
  }  
  public function get_specialist_by_cpso() {   
     if (clinic_login()) {    
        $this->load->model("admin_triage_model");  
          $response = $this->admin_triage_model->get_specialist_by_cpso_model();    
    } else {   
         $response = "Sesion Expired";   
     }        
echo json_encode($response); 
   }   
 // ********************************************************************* 
   // Admin Triage Patient Details
     // *********************************************************************    
public function referral_details() {  
      if (clinic_login()) {    
        //check valid state       
     $this->load->model("referral_model"); 
           $response = $this->referral_model->check_valid_referral_state_model("Admin Triage");
            if ($response == true) {     
           $data['page_content'] = $this->load->view('admin_triage_referral_details', NULL, TRUE);    
            $data['page_title'] = "Admin Triage Patient";    
            $data['jquery'] = $this->load->view('scripts/admin_triage_referral_details_script', NULL, TRUE); 
               $this->load->view('main', $data);  

          } else {       
         //set error message (remaining)    
            redirect("admin_triage");  
          }        } else {      
      redirect("/");      
  }   
 }    
public function get_referral_dash_info() {   
     if (clinic_login()) {  
          $this->load->model("admin_triage_model"); 
           $response = $this->admin_triage_model->get_referral_dash_info_model();  
      } else {    
        $response = "Sesion Expired";    
    }       
 echo json_encode($response);  
  }
}