<?php if (!defined('BASEPATH'))	exit('No direct script access allowed');
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
 */
class Chat extends Server_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	public function log() {
		if ($_POST) {
			$chat_rev = $this->input->post('chat_rev');
			$event_rev = $this->input->post('event_rev');
			$logs = $this->events_m->get_all_updates($chat_rev, $event_rev);
			echo json_encode($logs);
		}
	}
	
	public function send() {
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