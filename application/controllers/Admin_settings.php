<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_settings extends CI_Controller { 
   public function index() {  
      if (clinic_login()) {   
         $data['page_content'] = $this->load->view('admin_settings_master', NULL, TRUE); 
           $data['page_title'] = "Admin Settings";  
          $data['jquery'] = $this->load->view('scripts/admin_settings_script', NULL, TRUE);
            $this->load->view('main', $data);   
     } else {      
      redirect("/");  
      }  

  }   

 public function update_password() {  
      if (clinic_login()) {       
     $this->load->model("admin_settings_model");      
      $response = $this->admin_settings_model->update_password_model();
        } else {  
          $response = "Sesion Expired";    
    }       
 echo json_encode($response); 
   }
}