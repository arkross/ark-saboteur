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
	 * @return boolean true if successful 
	 */
	public function enter($room_id) {
		if (! $room = $this->get($room_id)) {
			return FALSE;
		}
		if ($room->is_playing) {
			return FALSE;
		}
		$current_players = count($this->db->select('*')
			->where('room_id', $room->id)
			->join('roles', $this->_table.'.id = roles.room_id')
			->get($this->_table)
			->result());
		if ($current_players < $this->max_player) {
			$this->session->set_userdata('room_id', $room->id);
			return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * Leaves a room
	 */
	public function quit() {
		$this->session->unset_userdata('room_id');
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
			`created_at` TIMESTAMP NOT NULL ,
			`is_playing` TINYINT(1)  NOT NULL DEFAULT 0 ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB";
		$inserts = "INSERT INTO `rooms` (`id`, `title`, `created_at`, `is_playing`) VALUES ('1', 'Lobby', NULL, '1');";
		return $this->db->query($query) && $this->db->query($inserts);
	}
}