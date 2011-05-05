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
		$counter = 5;
		do {
			$this->users_m->ping($this->session->userdata('user_id'));
			$this->response['valid_room'] = $this->db
				->where('id', $this->session->userdata('room_id'))
				->count_all_results('rooms');
			if ($this->response['valid_room'] < 1) break;
			$this->board->update();
			$this->response = array();
			$this->response['players'] = $this->board->players;

			$this->response['round'] = $this->rooms_m->get_round();
			$this->response['is_playing'] = ($this->rooms_m->is_playing() ? 1 : 0);
			if ($this->response['is_playing'] == 0) {
				if ($this->roles_m->is_creator())
					$this->response['round'] = lang('game.start');
				else
					$this->response['round'] = lang('game.wait');
			} else {
				$this->response['round'] = sprintf(lang('game.round'), $this->response['round']);
			}

			if ($this->rooms_m->is_playing()) {
				$this->response['cards']['deck_count'] = count($this->board->deck);
				$this->response['cards']['hand'] = $this->board->hand;

				$this->response['maze'] = $this->board->maze;

				$this->response['actions'] = $this->roles_m->get_status($this->session->userdata('user_id'));
				$this->response['actions'] = lang('game.'.$this->response['actions']['role']);
			

				// Prevents other players' roles for being broadcast
				foreach($this->response['players'] as &$player) {
					unset($player['role']['role']);

					// Automatically skips the player's turn if he/she has no card at hand.
					if (isset($player['role']['active'])
						&& $player['role']['active']
						&& $player['player_id'] == $this->session->userdata('user_id')
						&& count($this->response['cards']['hand']) == 0) {
						$this->board->end_turn();
					}
				}
			}

			if ($this->board->win != '') {
				if (!$this->rooms_m->is_playing()) $this->response['winner'] = lang('game.'.$this->board->win.'_win');
				else $round = $this->rooms_m->get_round();
			}
			
			$this->response = json_encode($this->response);
			$checksum = md5($this->response);
			header('ETag:'.$checksum);
			if ($checksum == $_SERVER['HTTP_IF_NONE_MATCH']) usleep(1000000);
			$counter --;
		} while ($checksum == $_SERVER['HTTP_IF_NONE_MATCH'] && $counter > 0);
		
		if (is_array($this->response))
			$this->response = json_encode($this->response);
		if ($counter == 0 && $checksum == $_SERVER['HTTP_IF_NONE_MATCH']) {
			$this->_respond_304();
		} else {
			echo $this->response;
		}
		if (isset($round) && $round < 3) {
			if ($this->roles_m->is_creator() && $this->rooms_m->is_playing()) {
				$this->events_m->fire_event('end_round');
				$this->rooms_m->set_round($this->rooms_m->get_round() + 1, false);
			}
		} elseif (isset($round) && $round >= 3){
			$this->rooms_m->quit();
		}
		$this->response = array();
	}
	
	/**
	 * Submits a move
	 */
	public function move() {
		if (empty($_POST)) return;
		$deck_id = $this->input->post('deck_id');
		$target = $this->input->post('target');
		$args = $_POST;
		if ($target == 'discard') {
			$success = $this->board->discard($deck_id);
			$this->events_m->fire_event('game.discard', array($this->users_m->get_user()->username));
		} else {
			$success = $this->board->move($deck_id, $args);
		}
		if ($success['response'] == true) {
			if ($target != 'discard')
				$this->events_m->fire_event('game.play_card', array($this->users_m->get_user()->username));
			$this->board->end_turn();
		}
		echo json_encode($success);
	}

	/**
	 * Starts a new game
	 * @return void
	 */
	public function start_game() {
		// Not the room creator? no way you can start the game
		if (! $this->roles_m->is_creator()) return;
		$round = $this->rooms_m->get_round();
		echo 'round:'.$round;
		if ($round == 1) {
			$this->_shuffle_players();
			// If it's not successful, return immediately and do nothing
			if (!$this->response['success']) {
				echo json_encode($this->response);
				return;
			}
			$this->events_m->fire_event('start_game');
		}
		
		$this->start_round();
	}

	/**
	 * Starts a new round
	 */
	public function start_round() {
		$this->boards_m->db->trans_start();
		$this->events_m->fire_event('start_round', array($this->rooms_m->get_round()));
		$this->board->prepare();
		$this->rooms_m->set_round($this->rooms_m->get_round());
		$this->boards_m->db->trans_complete();
	}
	
	/**
	 * Shuffles the players' positions
	 * @return void
	 */
	public function _shuffle_players() {
		$players = $this->roles_m->get_current_room_players(false);
		$this->board->players = $players;
		if (count($players) < 3 || count($players) > 10) {
			$this->response = array_merge(array('success' => '0'), $this->response);
			return;
		} else {
			$this->response = array_merge(array('success' => '1'), $this->response);
		}
		foreach($players as $key => $value) {
			$players[$key] = $value['player_id'];
		}
		shuffle($players);
		foreach($players as $key => $value) {
			$this->roles_m->add_status($value, 
				array(
					'turn' => $key, 
					'gold' => 0,
					'active' => 0,
					'pick_off' => 0,
					'wagon_off' => 0,
					'lantern_off' => 0
				));
		}
	}
	
	protected function _respond() {
		parent::_respond(json_encode($this->response));
	}
}