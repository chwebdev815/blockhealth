<?php

use \ConvertApi\ConvertApi;

if (!defined('BASEPATH'))
    exit("Access Denied!");

class Cron_involvedMed_inbox extends CI_Controller {

    public function index() {
        if (isset($argv)) {
            if (!empty($argv[1])) {
                switch ($argv[1]) {
                    case "pjYfSaBABGfEeXdPKsjYbtPusDfwkCaA":
                        $this->pjYfSaBABGfEeXdPKsjYbtPusDfwkCaA();
                        log_message("error", "Called function cron involvedmed pjYfSaBABGfEeXdPKsjYbtPusDfwkCaA");
                        break;
                }
            }
        }
    }

    public function pjYfSaBABGfEeXdPKsjYbtPusDfwkCaA() {
        log_message("error", "Cron_involvedMed_inbox called");
//        
//        log_message("error", "======== GMAIL =======");
        exit();
        $hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
        $username = 'involved@blockhealth.co';
        $password = 'Blockhealth18';

        ini_set('max_execution_time', 3000);
        date_default_timezone_set('US/Eastern');

        require_once('./vendor/convertapi/convertapi-php/lib/ConvertApi/autoload.php');
//        use \ConvertApi\ConvertApi;
        $inbox = imap_open($hostname, $username, $password) or die('Cannot connect to Gmail: ' . imap_last_error());
        echo "connected to involved <br/>";
        /* grab emails */
        $emails = imap_search($inbox, 'ALL');
        echo "fetched from involved <br/>";

        /* useful only if the above search is set to 'ALL' */


        /* if any emails found, iterate through each email */
        if ($emails) {


            /* begin output var */
            $output = '';

            /* put the newest emails on top */
            rsort($emails);

            $result = $this->db->select("value")->from("status_tracker")->where(array(
                        "field" => "involvedmed_email_extract"
                    ))->get()->result();
            if (!$result) {
                return;
            }
            $involvedmed_email_extract = $result[0]->value;

            $email_count_saved = false;
            /* for every email... */
            foreach ($emails as $email_number) {
                echo "Email found " . $email_number . "<br/>";
                if (!$email_count_saved) {
                    // save it in db
                   $this->db->where(array(
                       "field" => "involvedmed_email_extract"
                   ));
                   $this->db->update("status_tracker", array(
                       "value" => $email_number //save last email number
                   ));
                    $email_count_saved = true;
                }

                log_message("error", "checking $email_number <= $involvedmed_email_extract");
                echo "checking $email_number <= $involvedmed_email_extract";
                if ($email_number <= $involvedmed_email_extract) {
                    echo "breaking as $email_number <= $involvedmed_email_extract";
                    log_message("error", "breaking as $email_number <= $involvedmed_email_extract");
                    break;
                }

                /* get information specific to this email */
                $overview = imap_fetch_overview($inbox, $email_number, 0);
                $message = imap_fetchbody($inbox, $email_number, 2);
                $datetime_mail_received = date("Y-m-d H:i:s", strtotime($overview[0]->date));
                echo "date = " . $datetime_mail_received . "<br/>";
                log_message("error", "involvedmed date  = " . json_encode($overview[0]->date) . " and $datetime_mail_received");
                // echo "sender email id = " . $overview[0]->from . "<br/>";
                // echo "overview = " . json_encode($overview) . "<br/>";

                $header = imap_headerinfo($inbox, $email_number);
                $sender_email_id = $header->from[0]->mailbox . "@" . $header->from[0]->host;
                echo "From E-Mail Address : ".$sender_email_id."<br/>";

                /* get mail structure */
                $structure = imap_fetchstructure($inbox, $email_number);

                $attachments = array();

                /* if any attachments found... */
                if (isset($structure->parts) && count($structure->parts)) {
                    for ($i = 0; $i < count($structure->parts); $i++) {
                        $attachments[$i] = array(
                            'is_attachment' => false,
                            'filename' => '',
                            'name' => '',
                            'attachment' => ''
                        );

                        if ($structure->parts[$i]->ifdparameters) {
                            foreach ($structure->parts[$i]->dparameters as $object) {
                                if (strtolower($object->attribute) == 'filename') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['filename'] = $object->value;
                                }
                            }
                        }

                        if ($structure->parts[$i]->ifparameters) {
                            foreach ($structure->parts[$i]->parameters as $object) {
                                if (strtolower($object->attribute) == 'name') {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if ($attachments[$i]['is_attachment']) {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i + 1);

                            /* 3 = BASE64 encoding */
                            if ($structure->parts[$i]->encoding == 3) {
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            }
                            /* 4 = QUOTED-PRINTABLE encoding */ elseif ($structure->parts[$i]->encoding == 4) {
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }

                /* iterate through each attachment and save it */
                foreach ($attachments as $attachment) {
                    if ($attachment['is_attachment'] == 1) {
                        $pdf_file_name = generate_random_string(32);
                        $tiff_file_name = generate_random_string(32);

                        /* prefix the email number to the filename in case two emails
                         * have the attachment with the same file name.
                         */
                        $fp = fopen("./uploads/efax/" . $pdf_file_name . ".pdf", "w+");
                        fwrite($fp, $attachment['attachment']);
                        fclose($fp);

                        sleep(0.5);
                        // exec(base_url() . "uploads/efax/" . $pdf_file_name.' | awk \'/Pages/ {print $2}\'', $output);
                        echo "finding " . base_url() . "uploads/efax/" . $pdf_file_name . ".pdf<br/>";
                        $page_count = get_pdf_page_count("./uploads/efax/" . $pdf_file_name . ".pdf");
                        echo "page count = " . json_encode($page_count) . "<br/>";

                        ConvertApi::setApiSecret('4zr8HEoEStD19JvY');
                        $result = ConvertApi::convert('tiff', [
                            'File' => base_url() . "uploads/efax/" . $pdf_file_name . ".pdf",
                            'FileName' => $tiff_file_name,
                        ], 'pdf');
                        $result->saveFiles('./uploads/efax_tiff/');

                        log_message("error", "tiff saved as $tiff_file_name");
                        log_message("error", "pdf saved as $tiff_file_name");

                        $insert_data = array(
                            "to" => 3,
                            "file_name" => $pdf_file_name,
                            "tiff_file_name" => $tiff_file_name . ".tiff",
                            "pages" => $page_count,
                            "sender_email_id" => $sender_email_id,
                            "create_datetime" => $datetime_mail_received
                        );
                        $this->db->insert("efax_info", $insert_data);

                        log_message("error", " done for tiff");
                    }
                }

                // imap_delete();
            }
        }

        /* close the connection */
        imap_close($inbox);

        echo "Done";
    }

}
