<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Admin_login extends CI_Controller {

    public function index() {
        if (!admin_login()) {
            $this->load->view("tracker/login_master");
        } else {
            redirect("admin_dashboard");
        }
    }

//     public function contrast() {
// //        $imagick = new \Imagick(realpath($imagePath));
// //        if ($contrastType != 2) {
// //            $imagick->contrastImage($contrastType);
// //        }
// //
// //        header("Content-Type: image/jpg");
// //        echo $imagick->getImageBlob();
//         $im = imagecreatefrompng('dave.png');
//         if ($im && imagefilter($im, IMG_FILTER_CONTRAST, -100)) {
//             echo 'Image converted to grayscale.';
//             imagepng($im, 'dave'.generate_random_string().'.png');
//         } else {
//             echo 'Conversion to grayscale failed.';
//         }
//         imagedestroy($im);
//     }

    public function verify_admin_login() {
        $this->load->model("admin_login_model");
        $response = $this->admin_login_model->verify_admin_login_model();
        if ($response === true) {
            redirect("admin_dashboard");
        } else {
            $data["validation_errors"] = $response;
            $this->load->view("tracker/login_master", $data, null);
        }
    }

    public function logout() {
        if (!$this->session->userdata('username')) {
            log_message("debug", $this->session->userdata("username") . " logged out." .
                    " id = " . $this->session->userdata("user_id"));
        }
        $this->session->sess_destroy();
        redirect("admin_login");
    }

}
