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
class Roles_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'roles';
	}
	
	public function set_role($role) {
		$user = $this->users_m->get_user();
		$room = $this->rooms_m->get_current();
		$data = array(
			'player_id' => $user->id,
			'room_id' => $room->id,
			'role' => serialize($role)
		);
		if (!$existing_role = $this->get_by('player_id', $user->id)) {
			return $this->insert($data);
		} else {
			return $this->update($existing_role->id, $data);
		}
	}

	public function add_status($user_id, $status = array()) {
		$role = $this->db
			->where('player_id', $user_id)
			->where('room_id', $this->session->userdata('room_id'))
			->get($this->_table)
			->row_array();
		$role['role'] = array_merge(unserialize($role['role']), $status);
		$role['role'] = serialize($role['role']);
		return $this->update($role['id'], $role);
	}

	public function get_status($user_id) {
		$role = $this->db
			->select('role')
			->where('room_id', $this->session->userdata('room_id'))
			->where('player_id', $user_id)
			->get($this->_table)
			->row_array();
		return unserialize($role['role']);
	}
	
	public function get_current_room_players() {
		$players = $this->db
			->select('users.username as player, roles.*')
			->join('users', 'users.id = roles.player_id', 'LEFT')
			->where('room_id', $this->session->userdata('room_id'))
			->where('users.last_seen >=', now() - 5)
			->get($this->_table)
			->result_array();
		foreach($players as &$value) {
			$value['role'] = unserialize($value['role']);
		}
		if (isset($players[0]['role']['turn'])) {
			$newplayers = array();
			for($i = 0; $i < count($players); $i++) {
				$newplayers[$players[$i]['role']['turn']] = $players[$i];
			}
			return $newplayers;
		}
		return $players;
	}
	
	public function is_creator() {
		$player_id = $this->session->userdata('user_id');
		$role = $this->db
			->where('player_id', $player_id)
			->get($this->_table)
			->row_array();
		$role = unserialize($role['role']);
		if (isset($role['creator']) && $role['creator'] == 1) {
			return true;
		}
		return false;
	}
	
	public function _create() {
		$query = "CREATE TABLE IF NOT EXISTS `roles` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`player_id` INT NOT NULL ,
			`role` VARCHAR(255) NOT NULL ,
			`room_id` INT NOT NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB";
		return $this->db->query($query);
	}
}