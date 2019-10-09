<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Telnyx_call_well_health extends CI_Controller {

    public function get_data() {
        $json = file_get_contents('php://input');
        $action = json_decode($json, true);

        log_message("error", "telnyx webhook triggerd");

        $paydata = $action['data'];
        $payload = $paydata['payload'];
        $event_type = $paydata['event_type'];
        $call_to = $payload["to"];

        log_message("error", "event = > " . $event_type . ", "
                . "payload = " . base64_decode($payload['client_state']));



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

            $text = "Hello. Thank you for calling the {$clinic_name}.";
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
            $encodedString = base64_encode('welcome_first');
            $dataarray = array(
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

            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
            $text = 'If you are a new patient, or calling about a new referral, please press 1 .
	                     , .. . .
						 If you are calling about a prescription refill, an existing booking, or a follow-up appointment, please press 2 .
						 , . . .
						 If you are having an allergic reaction or an emergency, please press 3 .
						 , .. . .
						 If you are calling from a clinic or pharmacy, or for some other reason, please press 4 .';

            $encodedString = base64_encode('MainMenu');
            $dataarray = array(
                'payload' => $text,
                'voice' => 'female',
                'language' => 'en-US',
                'payload_type' => 'ssml',
                'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                'terminating_digit' => '#',
                'timeout_millis' => '5000',
                'inter_digit_timeout_millis' => '2000',
                'valid_digits' => '1234',
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
        } elseif ($event_type == 'call.gather.ended' && base64_decode($payload['client_state']) == "MainMenu") {
            $digits = $payload['digits'];
            $update = updateData('step_one', $digits, $call_control_id);
            if ($digits == 1 || $digits == 2) {
                $caller = ($digits == 1) ? "New patient" : "Patient";
                $update = updateData('caller', $caller, $call_control_id);

                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $text = "Great, I can help you with that";
                $encodedString = base64_encode('proceed_to_step_two');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
            } elseif ($digits == 3) {
                $update = updateData('caller', "Emergency", $call_control_id);
                $text = 'In the case of an emergency, please hang up and report to the emergency department at VGH or Saint Pauls Hospital, where an on-demand dermatologist can assist you.';
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('call_hangup');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
            } elseif ($digits == 4) {
                $update = updateData('caller', "Other", $call_control_id);
                $text = 'The clinic staff are unable to answer the phone right now.';
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('user_select_four');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
            } else {
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
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "user_select_four") {
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
            $text = 'Using the keypad, please enter a 10 digit phone number we can reach you at, including the area code, followed by the pound key.';
            $encodedString = base64_encode('phone_verification');
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
        } elseif ($event_type == 'call.gather.ended' && base64_decode($payload['client_state']) == "phone_verification") {
            $digits = $payload['digits'];
            $len = strlen($digits);
//            file_put_contents('payloadnext.txt', print_r($payload, true));
            if ($len >= 10) {
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('phone_virified_next_step_voicemail');
                $dataarray = array(
                    'payload' => 'Thankyou.',
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
                $data = curlPostData($urlNew, $call_control_id, $dataarray);
            } elseif ($len < 10) {
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
                $text = 'I’m sorry, I didn’t catch that. please enter a 10 digit phone number we can reach you at, including the area code, followed by the pound key';
                $encodedString = base64_encode('phone_verification');
                $dataarray = array(
                    'payload' => $text,
                    'voice' => 'female',
                    'language' => 'en-US',
                    'payload_type' => 'ssml',
                    'invalid_payload' => 'I’m sorry, I didn’t catch that.',
                    'terminating_digit' => '#',
                    'timeout_millis' => '5000',
                    'inter_digit_timeout_millis' => '5000',
                    'minimum_digits' => '10',
                    'maximum_digits' => '13',
                    'valid_digits' => '0123456789',
                    'terminating_digit' => '#',
                    'command_id' => rand(),
                    'client_state' => $encodedString
                );
                $welcome = curlPostData($urlNew, $call_control_id, $dataarray);
            }
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "phone_virified_next_step_voicemail") {
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
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "voicemail_message") {
            $url_new = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/record_start';
            $datarecord = array(
                'format' => 'mp3',
                'channels' => 'single',
                'play_beep' => 'true',
                'client_state' => base64_encode('save_voicemail_after_record'),
                'command_id' => '891510ac-f3e4-11e8-af5b-de00688a49022'
            );
            $data = curlPostData($url_new, $call_control_id, $datarecord);
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "proceed_to_step_two") {
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/gather_using_speak';
            $text = 'Please enter your 10 digit health card number, followed by the pound key. If you don’t have a health card number, please press 0.';
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
        } elseif ($event_type == 'call.gather.ended' && base64_decode($payload['client_state']) == "UserCard") {
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


            if ($len >= 10 || $digits == '0') {
                $update = updateData('user_number', $digits, $call_control_id);
                log_message("error", "phone number is saved. ");
                //phone number is taken fine. now process 3
                $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                $encodedString = base64_encode('speak_hold_message');
                $dataarray = array(
                    'payload' => 'Please hold',
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
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "speak_hold_message") {

            $caller = selectOne('caller', $call_control_id);
            $caller = $caller[0]->caller;

            log_message("error", "caller = $caller");
            if ($caller === "New patient") {
                //check hcn if found status = 'referral triage'

                /* QUERY TO  DATABASE WILL GOES HERE */

                $hcn = selectOne('health_card', $call_control_id);
                $hcn = $hcn[0]->health_card;

                $patient_data = $this->db->select("pat.fname")
                                ->from("referral_patient_info pat, clinic_referrals c_ref, efax_info efax")
                                ->where(array(
                                    "pat.ohip" => $hcn,
                                    "pat.active" => 1,
                                    "c_ref.active" => 1,
                                    "efax.active" => 1,
                                    "efax.to" => $clinic_id,
                                    "c_ref.status" => "Referral Triage"
                                ))
                                ->where("pat.referral_id", "c_ref.id", false)
                                ->where("c_ref.efax_id", "efax.id", false)
                                ->get()->result();
                log_message("error", "hcn lookup = " . $this->db->last_query());

                $patient_name = "";
                if ($patient_data) {
                    //stage 4. If caller = ‘newpatient’, and status = ‘referral triage’
                    updateData("status", "valid", $call_control_id);
                    updateData("progress_status", "Referral triage", $call_control_id);
                    
                    log_message("error", "stage 4. If caller = ‘newpatient’, and status = ‘referral triage’");
                    $text = "Hello {$patient_name}.
                             We have successfully received your referral, and are working with the doctor to find the best date and time. We will be in touch soon to book an appointment. 
                             Thank you";

                    $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                    $encodedString = base64_encode('call_hangup');
                    $dataarray = array(
                        'payload' => $text,
                        'voice' => 'female',
                        'language' => 'en-US',
                        'payload_type' => 'ssml',
                        'command_id' => rand(),
                        'client_state' => $encodedString
                    );
                    $data = curlPostData($urlNew, $call_control_id, $dataarray);
                } else {
                    updateData("status", "valid", $call_control_id);
                    updateData("progress_status", "Awaiting reply", $call_control_id);
                    // stage 5. If caller = ‘newpatient’, and status =/= ‘referral triage’
                    log_message("error", "stage 5. If caller = ‘newpatient’, and status =/= ‘referral triage’");
                    $text = "Unfortunately, we are currently unable to find your referral. Your details have been passed to the clinic staff, and they will be in touch soon. \n"
                            . "Thank you. ";

                    $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                    $encodedString = base64_encode('call_hangup');
                    $dataarray = array(
                        'payload' => $text,
                        'voice' => 'female',
                        'language' => 'en-US',
                        'payload_type' => 'ssml',
                        'command_id' => rand(),
                        'client_state' => $encodedString
                    );
                    $data = curlPostData($urlNew, $call_control_id, $dataarray);
                }
            } else if ($caller === "Patient") {
                log_message("error", "is patient");

                date_default_timezone_set("America/Los_Angeles");
                $time = date("H:i:s");
                $w = date("D");
                $weekname = array('Mon', 'Tue', 'Wed', 'Thu');
                $Fr = array('Fri');
                log_message("error", "comparing $time and $w");
                if ($time >= "10:00:00" && $time <= "14:00:00" && in_array($w, $weekname)) {
                    updateData("status", "valid", $call_control_id);
                    updateData("progress_status", "Forwarded", $call_control_id);
                    //stage 6. If caller = ‘patient’, and call during operating hours 
                    log_message("error", "stage 6. If caller = ‘patient’, and call during operating hours ");
                    $text = "Please note, that we have limited phone hours, and the best way to reach us is by e-mail at dermlab@wellclinics.ca - d. e. r. m. l. a. b. at. w. e. l. l. c. l. i. n. i. c. s. dot. c. a. \n"
                            . "If you would like to speak to a representative, we will do our best to speak with you shortly. \n"
                            . "Please hold.";

                    $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                    $encodedString = base64_encode('speak_for_patient_in_op_hours');
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
//                        echo $time . " IN";
                } elseif (($time < "10:00:00" || $time > "14:00:00") ||
                        ($w == "Fri" || $w == "Sat" || $w == "Sun")) {
                    updateData("status", "valid", $call_control_id);
                    updateData("progress_status", "Awaiting reply", $call_control_id);
//                        echo $time . " out";
                    // Stage 7. If caller = ‘patient’, and call outside operating hours
                    log_message("error", "Stage 7. If caller = ‘patient’, and call outside operating hours");


                    $text = "Unfortunately, we are currently unable to find your referral. Your details have been passed to the clinic staff, and they will be in touch soon. \n"
                            . "Please note our phone lines are currently closed and will reopen from 10 am to 2 pm on Monday to Thursday, and 9 am to 12 pm on Fridays. \n"
                            . "Please try back during those hours, or you can reach us is by e-mail at dermlab@wellclinics.ca - d. e. r. m. l. a. b. at. w. e. l. l. c. l. i. n. i. c. s. dot. c. a. \n"
                            . "Thank you, and have a great day.  \n";

                    $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/speak';
                    $encodedString = base64_encode('speak_for_patient_outside_op_hours');
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
                } else {
                    log_message("error", "Invalid option");
                }
            }
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "speak_for_patient_in_op_hours") {
            //forward call to hassaan
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/transfer';
            $encodedString = base64_encode('call_forwarded');
            $dataarray = array(
                'command_id' => rand(),
                'client_state' => $encodedString,
                'to' => "+16479066970"
            );
            $data = curlPostData($urlNew, $call_control_id, $dataarray);
            log_message("error", "trying to fw for " . "+16479066970");
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "speak_for_patient_outside_op_hours") {
            //forward call to hassaan
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/hangup';
            $encodedString = base64_encode('call_end_command');
            $dataarray = array(
                'command_id' => rand(),
                'client_state' => $encodedString
            );
            $data = curlPostData($urlNew, $call_control_id, $dataarray);
            log_message("error", "trying to hangup ");
        } elseif ($event_type == 'call.speak.ended' && base64_decode($payload['client_state']) == "call_hangup") {
            updateData("status", "valid", $call_control_id);
            $urlNew = 'https://api.telnyx.com/v2/calls/' . $call_control_id . '/actions/hangup';
            $encodedString = base64_encode('call_end_command');
            $dataarray = array(
                'command_id' => rand(),
                'client_state' => $encodedString
            );

            $data = curlPostData($urlNew, $call_control_id, $dataarray);
        }
    }

    public function show_data() {
        $data = $this->db->select("*")
                        ->from("telnyx_incoming")
                        ->order_by("id", "desc")
                        ->limit(15)
                        ->get()->result();

        echo json_encode($data);
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
