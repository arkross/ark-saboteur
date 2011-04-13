<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');
/* 
 * DO NOT REMOVE THIS LICENSE
 * 
 * This source code is created by Alexander.
 * You can use and modify this source code freely but
 * you are forbidden to change or remove this license.
 * 
 * Nick    : Alex
 * YM      : nikolas_alexander@ymail.com
 * Email   : nikolas.l.alexander@gmail.com
 * Blog    : http://www.arkross.com
 * Company : http://mimicreative.net
 */

/**
 * @author Alexander
 * @property Chat_Packets_m $chat_packets_m
 */
class Chat extends Client_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function ajax_chatlog() {
		if ($_POST) {
			$cur_rev = $this->input->post('rev');
			$logs = $this->chat_packets_m->get_updates($cur_rev);
			echo json_encode($logs);
		}
	}
	
	public function ajax_eventlog($cur_rev = 0) {
		
	}
	
	public function ajax_send() {
		if ($_POST) {
			$room = $this->data['room'];
			$user = $this->data['user'];
			$data = array(
				'sender_id' => $user->id,
				'room_id' => $room->id,
				'message' => $this->input->post('message'),
				'created_at' => now()
			);
			echo $this->chat_packets_m->insert($data);
		}
	}
}