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
	
	public function is_active() {
		$active = $this->get_active_player();
		if ($this->session->userdata('user_id') == $active['player_id']) {
			return true;
		}
		return false;
	}
	
	public function get_creator($room_id = '') {
		if ($room_id == '') $room_id = $this->session->userdata('room_id');
		$players = $this->get_many_by('room_id', $room_id);
		foreach($players as $p) {
			$p->role = unserialize($p->role);
			if ($p->role['creator']) {
				return $this->users_m->get($p->player_id);
			}
		}
		return FALSE;
	}
	
	public function get_players_by_role($role = 'saboteur') {
		$all = $this->get_current_room_players();
		$selected = array();
		foreach($all as $a) {
			if (isset($a['role']['role']) && $a['role']['role'] == $role) {
				$selected[] = $a;
			}
		}
		return $selected;
	}
	
	public function get_active_player() {
		$all = $this->get_current_room_players(false);
		foreach($all as $a) {
			if (isset($a['role']['active']) && $a['role']['active'] == 1) {
				return $a;
			}
		}
	}
	
	public function get_current_room($user_id = '') {
		if ($user_id == '') $user_id = $this->session->userdata('user_id');
		if ($role = $this->get_by('player_id', $user_id)) {
			return $role->room_id;
		}
		return False;
	}
	
	public function delete_role($user_id = '') {
		if ($user_id == '') $user_id = $this->session->userdata('user_id');
		return $this->delete_by('player_id', $user_id);
	}
	
	/**
	 * Sets the role field of a player, overrides the current.
	 * @param Array $role array of role and details
	 * @return boolean true if succeeded
	 */
	public function set_role($role) {
		$user = $this->users_m->get_user();
		$room = $this->rooms_m->get_current();
		$data = array(
			'player_id' => $user->id,
			'room_id' => $room->id,
			'role' => $role
		);
		if (!$existing_role = $this->get_by('player_id', $user->id)) {
			return $this->insert($data);
		} else {
			return $this->update($existing_role->id, $data);
		}
	}

	/**
	 * Adds a status to the role field. Overrides if the current key already exists.
	 * @param int $user_id the player id inflicted
	 * @param array $status the array of statuses
	 * @return boolean true if status updated
	 */
	public function add_status($user_id, $status = array()) {
		$role = $this->db
			->where('player_id', $user_id)
			->where('room_id', $this->session->userdata('room_id'))
			->get($this->_table)
			->row_array();
		$role['role'] = array_merge(unserialize($role['role']), $status);
		return $this->update($role['id'], $role);
	}

	/**
	 * Gets the current status of a player
	 * @param int $user_id the player id
	 * @return Array the status
	 */
	public function get_status($user_id) {
		$role = $this->db
			->where('room_id', $this->session->userdata('room_id'))
			->where('player_id', $user_id)
			->get($this->_table)
			->row_array();
		if ($role) return unserialize($role['role']);
		else return false;
	}
	
	/**
	 * Gets all players on the current room
	 * @return Mixed Players records
	 */
	public function get_current_room_players($lobby = true) {
		$lag = now() - 5;
		$timeout = now() - 60;
		if ($lobby) $timeout + 30;
		$players = $this->db
			->select('users.username as player, roles.*, users.last_seen')
			->join('users', 'users.id = roles.player_id', 'LEFT')
			->where('room_id', $this->session->userdata('room_id'))
			->where('users.last_seen >=', $timeout)
			->get($this->_table)
			->result_array();
		foreach($players as &$value) {
			$value['role'] = unserialize($value['role']);
			$value['role']['lag'] = $value['last_seen'] < $lag ? 1 : 0;
			unset($value['last_seen']);
		}
		if (isset($players[0]['role']['turn'])) {
			$newplayers = array();
			for($i = 0; $i < count($players); $i++) {
				$newplayers[$players[$i]['role']['turn']] = $players[$i];
			}
			ksort($newplayers);
			return $newplayers;
		}
		return $players;
	}
	
	/**
	 * Checks if the current player is the creator of the current room.
	 * @return boolean true if the player is creator, false otherwise.
	 */
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
	
	/**
	 * Changes turn to the next player
	 */
	public function next_turn() {
		$players = $this->get_current_room_players();
		$next = 0;
		for($i = 0; $i < count($players); $i++) {
			if ($players[$i]['role']['active'] == 1) {
				$this->add_status($players[$i]['player_id'], array('active' => 0));
				$next = $i+1;
				break;
			}
		}
		if ($next == count($players)) $next = 0;
		$next = $players[$next]['player_id'];
		return $this->add_status($next, array('active' => 1));
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