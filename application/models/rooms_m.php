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
class Rooms_m extends MY_Model {
	
	var $min_player = 3;
	var $max_player = 10;
	
	public function __construct() {
		parent::__construct();
		$this->_table = 'rooms';
	}

	/**
	 * Sets the current round
	 * @param int $round 1/2/3 if playing, 0 if standby
	 * @return bool true if successful
	 */
	public function set_round($round) {
		$room = $this->get_current();
		$room['is_playing'] = $round;
		return $this->update($room['id'], $room);
	}

	/**
	 * Gets the current round
	 * @return int 1/2/3 if playing, 0 if standby
	 */
	public function get_round() {
		$room = $this->get_current();
		return $room['is_playing'];
	}

	/**
	 * Get current game room. 1 indicates lobby
	 * 現在のルームを手に入れる。一はロビです。
	 * @return Record current room
	 */
	public function get_current() {
		return $this->get($this->session->userdata('room_id'));
	}
	
	/**
	 * Player joins a room
	 * @todo Finish
	 * @param String $room_name
	 * @param Boolean $creator
	 * @return boolean true if successful 
	 */
	public function enter($room_id, $creator = false) {
		$creator ? $creator = 1 : $creator = 0;
		if (! $room = $this->get($room_id)) {
			return FALSE;
		}
		if ($room->is_playing) {
			return FALSE;
		}
		$current_players = $this->db->select('*')
			->where('room_id', $room->id)
			->join('roles', $this->_table.'.id = roles.room_id')
			->count_all_results($this->_table);
		if ($current_players < $this->max_player) {
			$this->session->set_userdata('room_id', $room->id);
			$this->load->model('events_m');
			$this->events_m->fire_event('enter_room');
			return $this->roles_m->set_role(array('role' => 'ready', 'creator' => $creator));
		}
		return FALSE;
	}
	
	/**
	 * Leaves a room
	 */
	public function quit() {
		$this->load->model('events_m');
		$this->events_m->fire_event('leave_room');
		// Checks if it's the room creator quitting
		if ($this->roles_m->is_creator()) {
			$this->_close();
		}
		$this->session->set_userdata('room_id', 1);
		return $this->roles_m->set_role(array('role'=>'ready'));
	}

	/**
	 * Creates a new room
	 * 新規ルームを作成
	 * @param String $title
	 * @return boolean true if successful
	 */
	public function create($title) {
		$new_room = array(
			'title' => $title,
			'created_at' => now(),
			'is_playing' => 0
		);
		return $this->insert($new_room);
	}

	public function _close() {
		$id = $this->session->userdata('room_id');
		$this->db
			->where('room_id', $id)
			->delete('chat_packets');
		$this->db
			->where('room_id', $id)
			->delete('events');
		$this->delete($id);
	}
	
	/**
	 * Gets currently available rooms
	 * 最近有効なルームを手に入れる
	 * @return Mixed Array of records
	 */
	public function dropdown() {
		$result = parent::dropdown('id', 'title');
		unset($result[1]);
		return $result;
	}
	
	public function _create() {
		$query = "CREATE TABLE IF NOT EXISTS `rooms` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`title` VARCHAR(255) NOT NULL ,
			`created_at` INT NOT NULL ,
			`is_playing` SMALLINT NOT NULL DEFAULT 0 COMMENT '0 if not playing, 1, 2, 3 indicates round.' ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB";
		$inserts = "INSERT INTO `rooms` (`id`, `title`, `created_at`, `is_playing`) VALUES ('1', 'Lobby', NULL, '1');";
		return $this->db->query($query) && $this->db->query($inserts);
	}
}