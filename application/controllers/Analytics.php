<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Analytics extends CI_Controller { 
   public function index() { 
       if (clinic_login()) {   
         $data['page_content'] = $this->load->view('analytics_master', NULL, TRUE); 
           $data['page_title'] = "Analytics";   
         $data['jquery'] = $this->load->view('scripts/analytics_script', NULL, TRUE); 
           $this->load->view('main', $data);  
      } else {         
   redirect("/");      
  }   
 }
}