<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Telnyx_call_well_health extends CI_Controller {

    public function get_data() {
        log_message("error", "welcome to well health");
//        $json = file_get_contents('php://input');
//        log_message("error", "test 1 = " . $json);
//        log_message("error", "test 2 = " . json_encode($_REQUEST));
//        log_message("error", "test 3 = " . json_encode($this->input->get()));
//        log_message("error", "test 4 = " . json_encode($this->input->post()));
//        $action = json_decode($json, true);
//
//        log_message("error", "telnyx webhook triggerd");

//        $paydata = $action['data'];
//        $payload = $paydata['payload'];
//        $event_type = $paydata['event_type'];
//        $call_to = $payload["to"];
//
//        log_message("error", "event = > " . $event_type . ", "
//                . "payload = " . base64_decode($payload['client_state']));
//
//
//
//        if (isset($payload['call_control_id']) && !empty($payload['call_control_id'])) {
//            $call_control_id = $payload['call_control_id'];
//        } else {
//            $datalPAyload = selectCallID($payload['call_leg_id']);
//            $call_control_id = $datalPAyload[0]->call_control_id;
//        }
//
//
//        $clinic_id = 0;
//        $clinic_name = "";
//        if ($payload["clinic_id"] && $payload["clinic_name"]) {
//            $clinic_id = $payload["clinic_id"];
//            $clinic_name = $payload["clinic_name"];
//        }
//
//        $selectData = selectOne('step_one', $call_control_id);
//        $status_update = selectOne('status_update', $call_control_id);
//        $recording_saved = selectOne('recording_saved', $call_control_id);
//
//        $selectData = ($selectData) ? $selectData[0] : $selectData;
//        $status_update = ($status_update) ? $status_update[0] : $status_update;
//        $recording_saved = ($recording_saved) ? $recording_saved[0] : $recording_saved;
//
////        log_message("error", "status update = " . json_encode($status_update));
////        log_message("error", "paylad dir = " . $payload['direction']);
//
//        if ($event_type == 'call.initiated') {
//            log_message("error", "===============================================");
//            log_message("error", "===============================================");
//            log_message("error", "===============================================");
//            log_message("error", "===============================================");
//            log_message("error", "start - call.initiated");
//            $url = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/answer';
//            $data1 = getcallType($url, $call_control_id);
////            log_message("error", "end - call.initiated");
//        } 
    }

    public function show_data() {
        $data = $this->db->select("*")
                        ->from("telnyx_incoming")
                        ->order_by("id", "desc")
                        ->limit(5)
                        ->get()->result();

        foreach ($data as $key => $value) {
            echo "row $key = > " . json_encode($value) . "<br/><br/>";
        }
    }

    private function get_telnyx_clinic_info($call_to) {
        $clinic = $this->db->select("id, clinic_institution_name")
                        ->from("clinic_user_info")
                        ->where(array(
                            "concat('+1', telnyx_number) = " => $call_to
                        ))->get()->result();
        log_message("error", "clinic info q = " . $this->db->last_query());
        return $clinic;
    }

    private function transcript($audioFile) {

        //Imports the Google Cloud client library
        require_once 'vendor/google/cloud-speech/src/V1/SpeechClient.php';
        require_once 'vendor/google/cloud-speech/src/V1/RecognitionAudio.php';
        require_once 'vendor/google/cloud-speech/src/V1/RecognitionConfig.php';
        require_once 'vendor/google/cloud-speech/src/V1/RecognitionConfig/AudioEncoding.php';

        log_message("error", "inside transcript");
//        
        # Imports the Google Cloud client library
//        echo "hello";
        # get contents of a file into a string
        $content = file_get_contents($audioFile);

        # set string as audio content
        $audio = (new Google\Cloud\Speech\V1\RecognitionAudio())
                ->setContent($content);

        # The audio file's encoding, sample rate and language

        $config = new Google\Cloud\Speech\V1\RecognitionConfig([
            //'encoding' => AudioEncoding::MP3,
            'sample_rate_hertz' => 32000,
            'language_code' => 'en-US'
        ]);


//        $cred_file = file_get_contents("uploads/gk.json");
        # Instantiates a client
        $client = new Google\Cloud\Speech\V1\SpeechClient([
            'credentials' => "uploads/gk.json"
        ]);


        # Detects speech in the audio file
        $response = $client->recognize($config, $audio);

        # Print most likely transcription
        $datatrans = array();
        $getc = array();
        foreach ($response->getResults() as $result) {
            $alternatives = $result->getAlternatives();
            $mostLikely = $alternatives[0];
            $transcript = $mostLikely->getTranscript();
            $getConfidence = $mostLikely->getConfidence();

            $datatrans[] = $transcript;
            $getc[] = $getConfidence;
        }
        $client->close();

        return array(
            "transcript" => $transcript,
            "confidence" => $getConfidence
        );
    }

    //telnyx helper


    private function selectOne($key, $call_id) {
        
        $data = $this->db->select("$key")
                        ->from("telnyx_incoming")
                        ->where(array(
                            "call_control_id" => $call_id
                        ))->get()->result();
        return $data;
    }

    private function selectCallID($call_leg_id) {
        
        $data = $this->db->select("call_control_id")
                        ->from("telnyx_incoming")
                        ->where(array(
                            "call_leg_id" => $call_leg_id
                        ))->get()->result();
        return $data;
    }

    private function updateData($key, $value, $call_id) {
        
        $updated = $this->db->where(array(
                    "call_control_id" => $call_id
                ))->update("telnyx_incoming", array(
            "$key" => $value
        ));
        return $updated;
    }

    private function getcallType($url, $call_control) {
        $data = array(
            'client_state' => base64_encode('NewCall')
        );

        $POSTdata = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POSTFIELDS => $POSTdata,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            // CURLOPT_USERPWD => '7dfb9f4c-64ed-4a0b-9727-8737d48500e6:g-WQqIOjTQSRrQlHogUDGg',
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer KEY016D1769CF2D40ED3273B5A1E7279F57_cdyFo6KQXLXInbajc8MJew"
            )
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $data = curl_getinfo($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return true;
        }
    }

    /*
      Post data to telnyx api.
     */

    private function curlPostData($url, $call_control, $data) {

        $POSTdata = json_encode($data);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POSTFIELDS => $POSTdata,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Accept: application/json",
                "Authorization: Bearer KEY016D1769CF2D40ED3273B5A1E7279F57_cdyFo6KQXLXInbajc8MJew"
            )
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        $data = curl_getinfo($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {

            return $data1 = json_decode($response, true);
        }
    }

}
