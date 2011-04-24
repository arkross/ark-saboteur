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
class Presence extends Server_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	function index() {
		$this->ping();
	}
	
	/**
	 * Indicates that the user is still online
	 */
	function ping() {
		if ($this->users_m->ping($this->session->userdata('user_id'))) {
			$this->_respond('1');
		} else {
			$this->_respond('0');
		}
	}
	
	/**
	 * Checks if the current room has not been closed.
	 */
	function validate_room() {
		$room = $this->db->where('id', $this->session->userdata('room_id'))
			->count_all_results('rooms');
		if ($room < 1) {
			$this->_respond('1');
		} else {
			$this->_respond('0');
		}
	}
	
	/**
	 * Updates players list for the current room
	 */
	function players() {
		$users = $this->roles_m->get_current_room_players();
		$this->_respond(json_encode($users));
	}
	
	/**
	 * Updates rooms list
	 */
	function rooms() {
		$rooms = $this->rooms_m->dropdown();
		$this->_respond(json_encode($rooms));
	}
	
	/**
	 * Leaves the current game room
	 */
	function leave() {
		$this->rooms_m->quit();
		echo '1';
	}
}