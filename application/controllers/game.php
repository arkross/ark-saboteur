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
	/**
	 * The array of responses to send to client
	 * @var Array
	 */
	var $response = array();
	
	public function __construct() {
		parent::__construct();
		$this->load->library('board');
	}
	
	/**
	 * All game updates should be requested here
	 */
	public function update() {
		$this->response['players'] = $this->roles_m->get_current_room_players();
		
		$this->board->update();
		
		// Prevents other players' roles for being broadcast
		foreach($this->response['players'] as &$player) {
			unset($player['role']['role']);
		}
		
		$this->response['round'] = $this->rooms_m->get_round();
		if ($this->response['round'] == 0) {
			if ($this->roles_m->is_creator())
				$this->response['round'] = lang('game.start');
			else
				$this->response['round'] = lang('game.wait');
		} else {
			$this->response['round'] = sprintf(lang('game.round'), $this->response['round']);
		}
		
		$this->response['cards']['deck_count'] = count($this->board->deck);
		$this->response['cards']['hand'] = $this->board->hand;
		
		$this->response['actions'] = $this->roles_m->get_status($this->session->userdata('user_id'));
		$this->response['actions'] = lang('game.'.$this->response['actions']['role']);
		$this->_respond();
	}

	/**
	 * Starts a new game
	 * @return void
	 */
	public function start_game() {
		// Not the room creator? no way you can start the game
		if (! $this->roles_m->is_creator()) return;
		
		$this->_shuffle_players();
		
		// If it's not successful, return immediately and do nothing
		if (!$this->response['success']) {
			$this->respond();
			return;
		}
		$this->start_round();
	}

	/**
	 * Starts a new round
	 */
	public function start_round() {
		$this->rooms_m->set_round($this->rooms_m->get_round() + 1);
		$this->board->prepare();
	}

	/**
	 * Shuffles the players' positions
	 * @return void
	 */
	public function _shuffle_players() {
		$players = $this->roles_m->get_current_room_players();
		$this->board->players = $players;
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
			$this->roles_m->add_status($value, array('turn' => $key, 'gold' => 0));
		}
	}
	
	protected function _respond() {
		parent::_respond(json_encode($this->response));
	}
}