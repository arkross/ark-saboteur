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
 * @property Server_Controller $ci
 * @property Boards_m $boards_m
 */
class Board {
	var $deck = array();
	var $hand = array();
	var $discard = array();
	var $roles = array();
	var $maze = array();
	var $bank = array();

	var $players = array();
	var $win = '';
	
	public function __construct() {
		$this->ci =& get_instance();

		$this->ci->load->config('board');

		$this->ci->load->library('card');
		$this->ci->load->model('boards_m');
	}

	/**
	 * Prepares everything for a new round
	 */
	public function prepare() {
		if ($this->ci->rooms_m->get_round() > 1) {
			$this->_clean();
		}
		
		$this->players = $this->ci->roles_m->get_current_room_players(false);
		
		// Applies roles to players
		$this->roles = $this->ci->card->build_role_cards(count($this->players));
		for ($i = 0; $i < count($this->players); $i++) {
			$this->ci->roles_m->add_status($this->players[$i]['player_id'], array('role' => $this->roles[$i]));
		}
		
		// Builds deck cards
		$this->deck = $this->ci->card->build_deck();
		$this->ci->boards_m->set_deck($this->deck);
		
		// Distributes hand cards
		$dist = $this->ci->card->distribution;
		foreach($this->players as $player) {
			$this->ci->boards_m->draw($dist[count($this->players)]['hand'], $player['player_id']);
		}
		
		// Prepares maze
		$this->ci->boards_m->prepare_maze($this->ci->card->build_goal_cards());
		
		// Prepares gold
		if ($this->ci->rooms_m->get_round() <= 1) {
			$this->ci->boards_m->set_bank($this->ci->card->build_gold_cards());
		}
		
		// Activate the first player
		if ($this->ci->rooms_m->get_round() <= 1)
		$this->ci->roles_m->next_turn();
	}
	
	/**
	 * Called everytime the ajax wants to update.
	 */
	public function update() {
		$this->players = $this->ci->roles_m->get_current_room_players(false);
		
		$this->deck = $this->ci->boards_m->get_deck();
		// Checks whether hand cards of all players have been depleted
		$hands = array();
		$sabo_win = false || (count($this->deck) ? true : false);
		foreach($this->players as $player) {
			$hands[$player['player_id']] = $this->ci->boards_m->get_hand($player['player_id']);
			$sabo_win = $sabo_win || (count($hands[$player['player_id']]) ? true : false);
		}
		if (!$sabo_win && $this->ci->rooms_m->is_playing()) {
			$this->win = 'saboteur';
		}
		
		// Checks whether the goal card has been reached
		$this->maze = $this->ci->boards_m->get_maze();
		if (count($this->maze) && $this->ci->boards_m->goal_opened()) {
			$this->win = 'gold-digger';
		}
		
		$this->hand = $this->ci->boards_m->get_hand();
		if ($this->win != "") {
//			$this->end_round();
		}
	}
	
	public function move($deck_id, $options = array()) {
		$deck = (array)$this->ci->boards_m->get($deck_id);
		$card = $this->ci->cards_m->get($deck['card_id']);
		$return = $this->ci->card->play($card, $options);
		$this->check_path();
		return $return;
	}
	
	public function discard($deck_id) {
		$card = (array)$this->ci->boards_m->get($deck_id);
		$card['place'] = array('type' => 'discard');
		return array('response' => $this->ci->boards_m->update($card['id'], $card), 'error' => '');
	}
	
	public function end_turn() {
		$this->ci->boards_m->draw();
		$this->ci->roles_m->next_turn();
	}
	
	public function end_round() {
		if ($this->ci->roles_m->is_creator()) {
			$this->bank = $this->ci->boards_m->get_bank();
			if ($this->win == 'gold-digger') {
				$reversed = array_reverse($this->players);
				$active = $this->ci->roles_m->get_active_player();

				do {
					array_push($reversed, array_shift($reversed));
					reset($reversed);
					$current = current($reversed);
				} while($current['player_id'] != $active['player_id']);

				$take = array_slice($this->bank, 0, count($this->players));
				$current = current($take);
				$this->ci->boards_m->receive_gold($current['id'], $active['player_id']);
				next($take);
				foreach($take as $t) {
					while($current = current($this->players)) {
						if (next($this->players) === false) reset($this->players);
						if ($current['role']['role'] == 'gold-digger') {
							$this->ci->boards_m->receive_gold($t['id'], 
								$current['player_id']);
							$card = $this->ci->cards_m->get($t['card_id']);
							$this->ci->card->play($card, 
								array('target' => $current['player_id']));
							break;
						}
					}
				}
			} else {

			}
		}
		
	}
	
	public function _clean() {
		$hands = $this->ci->boards_m->get_hands();
		foreach($hands as $h) {
			$this->ci->boards_m->delete($h['id']);
		}
		unset($hands);
		$this->hand = array();
		
		$this->deck = $this->ci->boards_m->get_deck();
		foreach($this->deck as $d) {
			$this->ci->boards_m->delete($d['id']);
		}
		$this->deck = array();
		
		$this->maze = $this->ci->boards_m->get_maze();
		foreach ($this->maze as $m){
			$this->ci->boards_m->delete($m['id']);
		}
		$this->maze = array();
		
		$this->discard = $this->ci->boards_m->get_discard();
		foreach($this->discard as $d) {
			$this->ci->boards_m->delete($d['id']);
		}
		$this->discard = array();
	}
	
	private function check_path() {
		$maze = $this->ci->boards_m->get_maze();
		$mdata = array();
		$limit = array(
			'min' => array('x' => 100, 'y' => 100),
			'max' => array('x' => 0, 'y' => 0)
		);
		foreach($maze as $m) {
			$m['card_detail']['effect'] = unserialize($m['card_detail']['effect']);
			$adj = $m['card_detail']['effect']['rules'];
			$adj = str_replace('adj[', '', $adj);
			$adj = str_replace(']', '', $adj);
			$adj = str_split($adj);
			if ($limit['min']['x'] > $m['place']['coords']['x']) $limit['min']['x'] = $m['place']['coords']['x'];
			if ($limit['min']['y'] > $m['place']['coords']['y']) $limit['min']['y'] = $m['place']['coords']['y'];
			if ($limit['max']['x'] < $m['place']['coords']['x']) $limit['max']['x'] = $m['place']['coords']['x'];
			if ($limit['max']['y'] < $m['place']['coords']['y']) $limit['max']['y'] = $m['place']['coords']['y'];
			$data = array(
				'deck_id' => $m['id'],
				'card_id' => $m['card_id'],
				'card_type' => $m['type_name'],
				'adj' => $adj,
				'coords' => $m['place']['coords'],
				'reversed' => isset($m['place']['reversed']) ? $m['place']['reversed']: 0,
				'face_down' => isset($m['place']['face_down']) ? $m['place']['face_down']: 0
			);
			array_push($mdata, $data);
		}
		$tree = new Sabo_Tree($mdata);
		foreach ($tree->goal as $goal) {
			$this->ci->boards_m->flip_up($goal);
		}
	}
}