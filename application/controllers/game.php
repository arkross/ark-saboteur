<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * @property Card $card
 * @property Board $board
 */
class Game extends Server_Controller {
	var $response = array();
	
	public function __construct() {
		parent::__construct();
		$this->load->library('board');
	}

	public function start_game() {
		$this->response = array('round' => 1);
		$this->_shuffle_players();
		if (!$this->response['success']) {
			$this->respond();
			return;
		}
		$this->start_round();
	}

	public function start_round() {
		
	}

	public function _shuffle_players() {
		$players = $this->roles_m->get_current_room_players();
		if (count($players) < 3 || count($players) > 10) {
			$this->response = array_merge(array('success' => '0'), $this->response);
			return;
		} else {
			$this->response = array_merge(array('success' => '1'), $this->response);
		}
		foreach($players as $key => $value) {
			$players[$key] = $value['id'];
		}
		shuffle($players);
		foreach($players as $key => $value) {
			$this->roles_m->add_status($value, array('turn' => $key));
		}
	}
	
	private function _respond() {
		parent::_respond(json_encode($this->response));
	}
}