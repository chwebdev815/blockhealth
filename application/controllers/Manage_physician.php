<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Manage_physician extends CI_Controller {
    public function index() {   
     if (clinic_login()) {   
         $data['page_content'] = $this->load->view('manage_physician_master', NULL, TRUE);
            $data['page_title'] = "Manage Physician";   
         $data['jquery'] = $this->load->view('scripts/manage_physician_script', NULL, TRUE);  
          $this->load->view('main', $data);  
      } else {      
      redirect("/"); 
       }   
 }    
public function ssp_clinic_physician() { 
       if (clinic_login()) {    
        $this->load->model("manage_physician_model"); 
   
        $response = $this->manage_physician_model->ssp_clinic_physician_model();  
          echo $response; 
       } else {    
        echo false;    
    }  
  }   
 public function add_physician() {   
     if (clinic_login()) { 
           $this->load->model("manage_physician_model"); 
           $response = $this->manage_physician_model->add_physician_model();  
      } else {       
     $response = "Sesion Expired";   
     }      
  echo json_encode($response);
    }
}