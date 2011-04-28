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
	
	/**
	 * Fires an event
	 * @param String $type the event type, @see events_lang.php
	 * @param String $details array of parameters passed to event
	 * @return bool true if the event is successfully cast
	 */
	public function fire_event($type, $details = array()) {
		$user_id = $this->session->userdata('user_id');
		$room_id = $this->session->userdata('room_id');
		$details = array_merge($details, array('type' => $type));
		$data = array(
			'sender_id' => $user_id,
			'room_id' => $room_id,
			'details' => $details,
			'created_at' => now()
		);
		return $this->insert($data);
	}
	
	/**
	 * Gets all chat and event updates
	 * @param int $from_chat_id starting from chat id
	 * @param int $from_event_id starting from event id
	 * @return Mixed array of update records
	 */
	public function get_all_updates($from_chat_id, $from_event_id) {
		$this->load->model('chat_packets_m');
		$chats = $this->chat_packets_m->get_updates($from_chat_id);
		$events = $this->get_updates($from_event_id);
		$all = array_merge($chats, $events);
		if (!function_exists('mysort')) {
			function mysort($a, $b) {
				if ($a['created_at'] < $b['created_at']) return -1;
				if ($a['created_at'] > $b['created_at']) return 1;
				if ($a['created_at'] == $b['created_at']) return 0;
			}
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
	
	/**
	 * Generates event string
	 * @param Array $record the event record from the database
	 * @return String the event, parsed to string
	 */
	function _generate_string(&$record) {
		$args = array(
			$record['sender'],
			$record['room_title']
		);
		$other = unserialize($record['details']);
		$type = $other['type'];
		unset($other['type']);
		$args = array_merge($args, $other);
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