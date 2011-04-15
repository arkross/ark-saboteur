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

class Events_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'events';
	}
	
	public function fire_event($type, $details = array()) {
		$user_id = $this->session->userdata('user_id');
		$room_id = $this->session->userdata('room_id');
		$details = array_merge($details, array('type' => $type));
		$data = array(
			'sender_id' => $user_id,
			'room_id' => $room_id,
			'details' => serialize($details),
			'created_at' => now()
		);
		return $this->insert($data);
	}
	
	public function get_all_updates($from_chat_id, $from_event_id) {
		$this->load->model('chat_packets_m');
		$chats = $this->chat_packets_m->get_updates($from_chat_id);
		$events = $this->get_updates($from_event_id);
		$all = array_merge($chats, $events);
		function mysort($a, $b) {
			if ($a['created_at'] < $b['created_at']) return -1;
			if ($a['created_at'] > $b['created_at']) return 1;
			if ($a['created_at'] == $b['created_at']) return 0;
		}
		usort($all, 'mysort');
		return $all;
	}
	
	public function get_updates($from_id = 0) {
		$room_id = $this->session->userdata('room_id');
		$result = $this->db
			->select('events.*, users.username as sender, rooms.title as room_title')
			->join('users', 'users.id = events.sender_id', 'right')
			->join('rooms', 'rooms.id = events.room_id', 'right')
			->where('room_id', $room_id)
			->where('events.id >', $from_id)
			->order_by('created_at')
			->get($this->_table)
			->result_array();
		
		foreach($result as &$res) {
			$res['string'] = $this->_generate_string($res);
		}
		return $result;
	}
	
	function _generate_string(&$record) {
		$args = array(
			$record['sender'],
			$record['room_title']
		);
		$type = unserialize($record['details']);
		$type = $type['type'];
		return vsprintf(lang($type), $args);
	}
	
	public function _create() {
		$create = "CREATE  TABLE IF NOT EXISTS `ark-sabo`.`events` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`room_id` INT NOT NULL ,
			`sender_id` INT NOT NULL ,
			`details` TEXT NULL ,
			`created_at` INT NOT NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB
		COMMENT = 'Every changes to the board will be recorded here.'";
		
		return $this->db->query($create);
	}
}