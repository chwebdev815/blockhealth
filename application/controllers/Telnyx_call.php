<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Telnyx_call extends CI_Controller {

    public function get_data() {
        $json = file_get_contents('php://input');
        $action = json_decode($json, true);



        log_message("error", "telnyx webhook triggerd");

        $paydata = $action['data'];
        $payload = $paydata['payload'];
        $event_type = $paydata['event_type'];
        $call_to = $payload["to"];

        log_message("error", "event = > " . $event_type . ", payload = " . base64_decode($payload['client_state']));



        if (isset($payload['call_control_id']) && !empty($payload['call_control_id'])) {
            $call_control_id = $payload['call_control_id'];
        } else {
            $datalPAyload = selectCallID($payload['call_leg_id']);
            $call_control_id = $datalPAyload[0]->call_control_id;
        }


        $clinic_id = 0;
        $clinic_name = "";
        if ($payload["clinic_id"] && $payload["clinic_name"]) {
            $clinic_id = $payload["clinic_id"];
            $clinic_name = $payload["clinic_name"];
        }

        $selectData = selectOne('step_one', $call_control_id);
        $status_update = selectOne('status_update', $call_control_id);
        $recording_saved = selectOne('recording_saved', $call_control_id);

        $selectData = ($selectData) ? $selectData[0] : $selectData;
        $status_update = ($status_update) ? $status_update[0] : $status_update;
        $recording_saved = ($recording_saved) ? $recording_saved[0] : $recording_saved;

//        log_message("error", "status update = " . json_encode($status_update));
//        log_message("error", "paylad dir = " . $payload['direction']);

        if ($event_type == 'call.initiated') {
            log_message("error", "===============================================");
            log_message("error", "===============================================");
            log_message("error", "===============================================");
            log_message("error", "===============================================");
            log_message("error", "start - call.initiated");
            $url = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/answer';
            $data1 = getcallType($url, $call_control_id);
//            log_message("error", "end - call.initiated");
        } elseif ($event_type == 'call.answered' && base64_decode($payload['client_state']) == "NewCall") {
            log_message("error", "start - call.answered");

            //insert incomnig call entry

            $clinic_info = $this->get_telnyx_clinic_info($call_to);
            if ($clinic_info) {
                $clinic_id = $clinic_info[0]->id;
                $clinic_name = $clinic_info[0]->clinic_institution_name;
            }

            $inserted = $this->db->insert("telnyx_incoming", array(
                "call_control_id" => $call_control_id,
                "call_leg_id" => $payload['call_leg_id'],
                "clinic_id" => $clinic_id
            ));

            $text = "Hello. Thank you for calling {$clinic_name}.";
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
            $encodedString = base64_encode('welcome_first');
            $dataarray = array(
                "clinic_id" => $clinic_id,
                "clinic_name" => $clinic_name,
                'payload' => $text,
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'command_id' => rand(),
                'client_state' => $encodedString
            );

            $data = curlPostData($urlNew, $call_control_id, $dataarray);
            log_message("error", "end - call.answered");
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == 'welcome_first') {
            log_message("error", "start - call.speak.ended");

            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
            $text = "If you are calling to book an appointment with {$clinic_name}, please press 1.
                     If you are calling from a clinic or pharmacy, please press 2.
                    If you are calling for some other reason, please press 3.";
            $encodedString = base64_encode('MainMenu');
            $dataarray = array(
                "clinic_id" => $clinic_id,
                'payload' => $text,
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                'terminating_digit' => '#',
                'timeout_millis' => '5000',
                'inter_digit_timeout_millis' => '2000',
                'valid_digits' => '123',
                'command_id' => rand(),
                'client_state' => $encodedString
            );

            $welcome = curlPostData($urlNew, $call_control_id, $dataarray);

            log_message("error", "end - call.speak.ended");
        } elseif ($event_type == 'call.dtmf.received' && base64_decode($payload['client_state']) == "MainMenu") {
            //pressed button 1/2/3

            log_message("error", "start - call.dtmf.received main menu");
//            log_message("error", "payload = " . json_encode($payload));
            $digits = $payload['digit'];
            $update = updateData('step_one', $digits, $call_control_id);
            if ($digits == 1) {
                $update = updateData('caller', "patient", $call_control_id);
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $text = "Great, I can help you with that";
                $encodedString = base64_encode('user_response_get');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
            } elseif ($digits == 2 || $digits == 3) {
                $update = updateData('caller', "other", $call_control_id);
                $text = 'The clinic staff are unable to answer the phone right now.';
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('next_message_step_one');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
            } else {
//                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $text = "I’m sorry, I didn’t catch that.";
                $encodedString = base64_encode('user_response_get');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
            }
            $data = curlPostData($urlNew, $call_control_id, $dataarray);

            log_message("error", "end - call.dtmf.received main menu");
        } elseif ($event_type == 'speak_ended' && base64_decode($payload['client_state']) == "next_message_step_one") {
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
            $text = 'Using the keypad, please enter a 10 digit phone number we can reach you at';
            $encodedString = base64_encode('UserPhoneBeforeVoicemail');
            $dataarray = array(
                'payload' => $text,
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                'terminating_digit' => '#',
                'timeout_millis' => '5000',
                'inter_digit_timeout_millis' => '5000',
                'minimum_digits' => '1',
                'maximum_digits' => '13',
                'valid_digits' => '0123456789',
                'terminating_digit' => '#',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $data = curlPostData($urlNew, $call_control_id, $dataarray);
        } elseif ($event_type == 'call.speak.ended' &&
                base64_decode($payload['client_state']) == "user_response_get") {

            log_message("error", "start - call.speak.ended user_response_get");
//            log_message("error", "select data = " . json_encode($selectData));
//            log_message("error", "s1 = " . $selectData->step_one);

            if ($selectData->step_one === "1") {
//                log_message("error", "step1 = 1");
                $text = 'First, I’m going to need to ask you a few questions.';
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('first_message_step_two');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
                $data = curlPostData($urlNew, $call_control_id, $dataarray);
            } elseif ($selectData->step_one == "2") {

                log_message("error", "step1 = 2");
//                $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
//                $datarecord = array(
//                    'format' => 'mp3',
//                    'channels' => 'single',
//                    'play_beep' => 'true',
//                    'client_state' => base64_encode('voicemailoption1'),
//                    'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49022'
//                );
//                $data = curlPostData($url_new, $call_control_id, $datarecord);
            } elseif ($selectData->step_one == "3") {
                log_message("error", "step1 = 3");
//                $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
//                $datarecord = array(
//                    'format' => 'mp3',
//                    'channels' => 'single',
//                    'play_beep' => 'true',
//                    'client_state' => base64_encode('voicemailoption2'),
//                    'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49022'
//                );
//                $data = curlPostData($url_new, $call_control_id, $datarecord);
            } else {
                log_message("error", "none of 1/2/3");
            }
            log_message("error", "end - call.speak.ended user_response_get");
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "first_message_step_two") {
            $text = 'Please say your first and last name after the beep ?';
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
            $encodedString = base64_encode('user_name_say');
            $dataarray = array(
                'payload' => $text,
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $data = curlPostData($urlNew, $call_control_id, $dataarray);
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "user_name_say" && $status_update->status_update == '0') {

            log_message("error", "start - call.speak.ended user_name_say");

            $update = updateData('status_update', '1', $call_control_id);

            $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
            $datarecord = array(
                'format' => 'mp3',
                'channels' => 'single',
                'play_beep' => 'true',
                'client_state' => base64_encode('nametracnscription'),
                'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49022'
            );
            $data = curlPostData($url_new, $call_control_id, $datarecord);
            if (isset($data)) {
                sleep(4);
                $url_stop = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_stop';
                $datastop = array(
                    'client_state' => base64_encode('name_recording_stop'),
                    'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49012'
                );
                $data1 = curlPostData($url_stop, $call_control_id, $datastop);
            }

            log_message("error", "end - call.speak.ended user_name_say with = $call_control_id");
        } elseif ($event_type == 'call.recording.saved' && base64_decode($payload['client_state']) == "   " && $recording_saved->recording_saved == '0') {
            log_message("error", "start - call.recording.saved blank");

            $datalPAyload = selectCallID($payload['call_leg_id']);
            $call_control_id = $datalPAyload[0]->call_control_id;
            $update = updateData('recording_saved', '1', $call_control_id);

            $file_name = generate_random_string() . ".mp3";
//            push_telnyx_to_bucket($file_name, $payload['recording_urls']['mp3']);
//            $saved = file_put_contents("./uploads/telnyx/" . $file_name, $payload['recording_urls']['mp3']);
            $this->load->helper('file');
            $saved = write_file("./uploads/telnyx/" . $file_name, $payload['recording_urls']['mp3']);

            log_message("error", "mp3 file saving with name " . $file_name);
            log_message("error", "save response=  " . $saved);

            $trascription_result = $this->transcript($payload['recording_urls']['mp3']);
            updateData("first_name", $trascription_result["transcript"], $call_control_id);
            updateData("confidence_score", $trascription_result["confidence"], $call_control_id);
            updateData("recording_file", $file_name, $call_control_id);



            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
            $encodedString = base64_encode('user_dob_question');
            // $update1 =  updateData('first_name',$run,$call_control_id,$conn);
//            $nameget = explode(' ', $run);
            $dataarray = array(
                'payload' => "Thank you . ",
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $data = curlPostData($urlNew, $call_control_id, $dataarray);

            log_message("error", "stop - call.recording.saved blank");
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "user_dob_question") {
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
            $encodedString = base64_encode('dob_record');
            $dataarray = array(
                'payload' => "Please say your date of birth, for example, January 2nd 1985 ?  ",
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $data = curlPostData($urlNew, $call_control_id, $dataarray);
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "name_confirmation") {

            log_message("error", "start - call.speak.ended name_confirmation");

            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
            $text = 'Using the keypad, Please enter your 10 digit health card number, followed by the pound key. If you don’t have a health card number, please press 0.';
            $encodedString = base64_encode('UserCard');
            $dataarray = array(
                'payload' => $text,
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                'terminating_digit' => '#',
                'inter_digit_timeout_millis' => '5000',
                'minimum_digits' => '1',
                'maximum_digits' => '10',
                'valid_digits' => '0123456789',
                'terminating_digit' => '#',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $welcome = curlPostData($urlNew, $call_control_id, $dataarray);


            log_message("error", "stop - call.speak.ended name_confirmation");
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "dob_record") {
            // $update     = updateData('status_update', '1', $call_control_id, $conn);
            $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
            $datarecord = array(
                'format' => 'mp3',
                'channels' => 'single',
                'play_beep' => 'true',
                'client_state' => base64_encode('dobtracnscription'),
                'command_id' => '891510ac-f3e4-11e8-af5b-de00688a490222'
            );
            $data = curlPostData($url_new, $call_control_id, $datarecord);
            if (isset($data)) {
                sleep(4);
                $url_stop = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_stop';
                $datastop = array(
                    'client_state' => base64_encode('dob_recording_stop'),
                    'command_id' => '891510ac-f3e4-11e8-af5b-de00688a490123'
                );
                $data1 = curlPostData($url_stop, $call_control_id, $datastop);
            }
        } elseif ($event_type == 'call.recording.saved' && base64_decode($payload['client_state']) == "dob_recording_stop") {
            $datalPAyload = selectCallID($payload['call_leg_id']);
            $call_control_id = $datalPAyload[0]->call_control_id;
            // $update          = updateData('recording_saved', '1', $call_control_id, $conn);
            //$run = getTranscription($payload['recording_urls']['mp3']);
            $file_name = generate_random_string() . ".mp3";
            $this->load->helper('file');
            $saved = write_file("./uploads/telnyx/" . $file_name, $payload['recording_urls']['mp3']);

            log_message("error", "dob - mp3 file saving with name " . $file_name);
            log_message("error", "save response=  " . $saved);

            $trascription_result = $this->transcript($payload['recording_urls']['mp3']);
            updateData("dob", $trascription_result["transcript"], $call_control_id);
            updateData("dob_confidence_score", $trascription_result["confidence"], $call_control_id);
            updateData("dob_recording_file", $file_name, $call_control_id);

//            file_put_contents('recording_url.txt', $payload['recording_urls']['mp3']);
            //file_put_contents('recording_url_trans.txt', $run);
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
            $encodedString = base64_encode('name_confirmation');
            // $update1 =  updateData('first_name',$run,$call_control_id,$conn);
            //$nameget       = explode(' ', $run);
            $dataarray = array(
                'payload' => "Thank you .",
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $data = curlPostData($urlNew, $call_control_id, $dataarray);
        } elseif ($event_type == 'call.dtmf.received' && base64_decode($payload['client_state']) == "name_recording_stop") {

            log_message("error", "start - call.dtmf.received name_recording_stop");
            $url_stop = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_stop';
            $datastop = array(
                'client_state' => base64_encode('name_recording_stop'),
                'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49012'
            );
            $data1 = curlPostData($url_stop, $call_control_id, $datastop);
            log_message("error", "stop - dtmf name_recording_stop");
        } elseif ($event_type == 'call.gather.ended' && base64_decode($payload['client_state']) == "UserCard") {

            log_message("error", "start - call.gather.ended UserCard");

            $digits = $payload['digits'];
            $len = strlen($digits);

            if ($len >= 10 || $digits == '0') {
                $update = updateData('health_card', $digits, $call_control_id);
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('thankyou_message_after_card');
                $dataarray = array(
                    'payload' => 'Thank you',
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );

                $data = curlPostData($urlNew, $call_control_id, $dataarray);
            } elseif ($digits != '0' && $len < 10) {
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
                $text = 'I’m sorry, I didn’t catch that. Please enter your 10 digit health card number, followed by the pound key. ';
                $encodedString = base64_encode('UserCard');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                    'terminating_digit' => '#',
                    'timeout_millis' => '5000',
                    'inter_digit_timeout_millis' => '5000',
                    'minimum_digits' => '1',
                    'maximum_digits' => '10',
                    'valid_digits' => '0123456789',
                    'terminating_digit' => '#',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
                $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
                log_message("error", "stop - call.gather.ended UserCard");
            }
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "thankyou_message_after_card") {

            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
            $text = 'Please enter your 10 digit phone number, including the area code, followed by the pound key';
            $encodedString = base64_encode('UserPhone');
            $dataarray = array(
                'payload' => $text,
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                'terminating_digit' => '#',
                'timeout_millis' => '5000',
                'inter_digit_timeout_millis' => '5000',
                'minimum_digits' => '1',
                'maximum_digits' => '13',
                'valid_digits' => '0123456789',
                'terminating_digit' => '#',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
        } elseif ($event_type == 'call.gather.ended' && base64_decode($payload['client_state']) == "UserPhone") {
            $digits = $payload['digits'];
            $len = strlen($digits);
            log_message("error", "payload = " . json_encode($payload));
//            file_put_contents('payloadnext.txt', print_r($payload, true));
            if ($len >= 10 || $digits == '0') {
                $update = updateData('user_number', $digits, $call_control_id);
                $text = 'Thank you. The clinic staff has been notified of your appointment request, and will be in touch as soon as possible.
    Have a great day';
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('user_final_callback');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );

                $data = curlPostData($urlNew, $call_control_id, $dataarray);
            } elseif ($digits != '0' && $len < 10) {
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
                $text = 'I’m sorry, I didn’t catch that. Please enter your 10 digit phone number, including the area code, followed by the pound key';
                $encodedString = base64_encode('UserPhone');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                    'terminating_digit' => '#',
                    'timeout_millis' => '5000',
                    'inter_digit_timeout_millis' => '5000',
                    'minimum_digits' => '1',
                    'maximum_digits' => '13',
                    'valid_digits' => '0123456789',
                    'terminating_digit' => '#',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
                $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
            }
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "user_final_callback") {
            updateData("status", "valid", $call_control_id);

            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/hangup';
            $encodedString = base64_encode('call_end_command');
            $dataarray = array(
                'command_id' => rand(),
                'client_state' => $encodedString
            );

            $data = curlPostData($urlNew, $call_control_id, $dataarray);
        } elseif ($event_type == 'call.gather.ended' && base64_decode($payload['client_state']) == "UserPhoneBeforeVoicemail") {
            $digits = $payload['digits'];
            $len = strlen($digits);
//            file_put_contents('payloadnext.txt', print_r($payload, true));
            if ($len >= 10 || $digits == '0') {
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('voicemail_message_before');
                $dataarray = array(
                    'payload' => 'Thankyou.',
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
                $data = curlPostData($urlNew, $call_control_id, $dataarray);
            } elseif ($digits != '0' && $len < 10) {
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
                $text = 'I’m sorry, I didn’t catch that. Using the keypad, please enter a 10 digit phone number we can reach you at';
                $encodedString = base64_encode('UserPhoneBeforeVoicemail');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                    'terminating_digit' => '#',
                    'timeout_millis' => '5000',
                    'inter_digit_timeout_millis' => '5000',
                    'minimum_digits' => '1',
                    'maximum_digits' => '13',
                    'valid_digits' => '0123456789',
                    'terminating_digit' => '#',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
                $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
            }
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "voicemail_message") {
            $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
            $datarecord = array(
                'format' => 'mp3',
                'channels' => 'single',
                'play_beep' => 'true',
                'client_state' => base64_encode('voicemailoption2'),
                'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49022'
            );
            $data = curlPostData($url_new, $call_control_id, $datarecord);
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "voicemail_message_before") {
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
            $encodedString = base64_encode('voicemail_message');
            $dataarray = array(
                'payload' => 'Please record a message after the beep, and we will be in touch as soon as possible.',
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $data = curlPostData($urlNew, $call_control_id, $dataarray);
        } else {
            
        }
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

}
