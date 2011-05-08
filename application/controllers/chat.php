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
	
	public function index() {
		$this->log();
	}
	
	public function log() {
		if ($_POST) {
			$counter = 5;
			$chat_rev = $this->input->post('chat_rev');
			$event_rev = $this->input->post('event_rev');
			do {
				$logs = $this->events_m->get_all_updates($chat_rev, $event_rev);
				$logs = json_encode($logs);
				$checksum = md5($logs);
				header('ETag:'.$checksum);
				if ($checksum == $_SERVER['HTTP_IF_NONE_MATCH']) usleep(1000000);
				$counter --;
			} while ($checksum == $_SERVER['HTTP_IF_NONE_MATCH']
				&& $this->users_m->still_alive()
				&& $this->roles_m->get_current_room() == $this->session->userdata('room_id')
				&& $counter > 0);
			if ($checksum == $_SERVER['HTTP_IF_NONE_MATCH']) {
				$this->_respond_304();
			} else {
				echo $logs;
			}
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