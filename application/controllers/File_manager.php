<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class File_manager extends CI_Controller {

    public function index() {
//        $data = $this->input->get();
//        $filename = $data["filename"];
        $file_path = "/var/emrsftp/clinics/1679091c5a880faf6fb5e6087eb1b2dc/eurobsdcon2017-branch-vpn.pdf";
        if (file_exists($file_path)) {
//            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
//            header('Expires: 0');
//            header('Cache-Control: must-revalidate');
//            header('Pragma: public');
//            header('Content-Length: ' . filesize($file_path));
            readfile($file_path);
            exit;
        }
    }

}
