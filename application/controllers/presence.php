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
		$this->lobbyupdate();
	}
	
	/**
	 * All updates for the lobby, using long polling
	 */
	function lobbyupdate() {
		$counter = 5;
		$checksum = '';
		do {
			$this->users_m->ping($this->session->userdata('user_id'));
			$response = array();
			$response['users'] = $this->roles_m->get_current_room_players();
			$response['rooms'] = $this->rooms_m->dropdown();
			$response = json_encode($response);
			$checksum = md5($response);
			header('ETag:'.$checksum);
			if ($checksum == $_SERVER['HTTP_IF_NONE_MATCH']) usleep(1000000);
			$counter--;
		} while ($checksum == $_SERVER['HTTP_IF_NONE_MATCH']
			&& $this->users_m->still_alive()
			&& $this->roles_m->get_current_room() == $this->session->userdata('room_id')
			&& $counter > 0);
		if ($checksum == $_SERVER['HTTP_IF_NONE_MATCH']) {
			$this->_respond_304();
		} else {
			echo $response;
		}
	}
	
	/**
	 * Leaves the current game room
	 */
	function leave() {
		$this->rooms_m->quit();
		echo '1';
	}
}