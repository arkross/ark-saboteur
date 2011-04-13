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
class Chat_Packets_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'chat_packets';
	}
	
	/**
	 * Gets all log updates
	 * 全てのログアプデートを手に入れる
	 * @param type $from_rev Takes update from revision n
	 * @return Mixed array of records
	 */
	public function get_updates($from_rev = 0) {
		$room_id = $this->session->userdata('room_id');
		$result = $this->db
			->select('chat_packets.*, users.username as sender')
			->join('users', 'users.id = chat_packets.sender_id', 'LEFT')
			->where('room_id', $room_id)
			->get($this->_table)
			->result_array();
		for($i = 0; $i < $from_rev; $i++) {
			unset($result[$i]);
		}
		return $result;
	}
	
	public function _create() {
		$query = "CREATE TABLE IF NOT EXISTS `chat_packets` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`sender_id` INT NOT NULL ,
			`room_id` INT NOT NULL ,
			`message` TEXT NULL ,
			`created_at` INT NOT NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB
		COMMENT = 'Chat can only happen when a user has entered a room.'";
		return $this->db->query($query);
	}
}