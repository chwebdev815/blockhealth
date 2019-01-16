<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Workflow_dash extends CI_Controller {  
  public function index() {   
     if (clinic_login()) {   
         $data['page_content'] = $this->load->view('workflow_dash_master', NULL, TRUE);
            $data['page_title'] = "Workflow Dashboard";   
         $data['jquery'] = $this->load->view('scripts/workflow_dash_script', NULL, TRUE);  
          $this->load->view('main', $data);       
 } else {            redirect("/");   
     } 
   }  
  public function get_workflow_dash_info() {   
     if (clinic_login()) {    
        $this->load->model("workflow_dash_model");    
        $response = $this->workflow_dash_model->get_workflow_dash_info_model();   
     } else {         
   $response = "Sesion Expired";   
     }     
   echo json_encode($response);  
  } 
   public function get_scheduled_patients() {  
      if (clinic_login()) { 
           $this->load->model("workflow_dash_model"); 
           $response = $this->workflow_dash_model->get_scheduled_patients_model();    
    } else {    
        $response = "Sesion Expired";   
     }    
    echo json_encode($response); 
   }
}