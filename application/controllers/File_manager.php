<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class File_manager extends CI_Controller {

    public function pdf() {
        if (clinic_login()) {
            $clinic_dir = $this->uri->segment(3);
            $patient_dir = $this->uri->segment(4);
            $file_name = $this->uri->segment(5);
            $filename = $data["filename"];
            $file_path = files_dir() . "$clinic_dir/$patient_dir/$file_name";
            log_message("error", "trying FILE ACCESS = " . $file_path);
            if (file_exists($file_path)) {
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                readfile($file_path);
                exit;
            }
        }
    }
    public function tiff() {
        if (clinic_login()) {
            $clinic_dir = $this->uri->segment(3);
            $patient_dir = $this->uri->segment(4);
            $file_name = $this->uri->segment(5);
            $filename = $data["filename"];
            $file_path = files_dir() . "$clinic_dir/$patient_dir/$file_name";
            log_message("error", "trying FILE ACCESS = " . $file_path);
            if (file_exists($file_path)) {
                header('Content-Type: application/tiff');
                header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
                readfile($file_path);
                exit;
            }
        }
    }

}
