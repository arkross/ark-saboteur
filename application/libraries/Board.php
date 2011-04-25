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

	var $players = array();
	
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
		$this->players = $this->ci->roles_m->get_current_room_players();
		
		// Applies roles to players
		$this->roles = $this->ci->card->build_role_cards(count($this->players));
		for ($i = 0; $i < count($this->players); $i++) {
			$this->ci->roles_m->add_status($this->players[$i]['id'], array('role' => $this->roles[$i]));
		}
		
		// Builds deck cards
		$this->deck = $this->ci->card->build_deck();
		$this->ci->boards_m->set_deck($this->deck);
		
		// Distributes hand cards
		$dist = $this->ci->card->distribution;
		foreach($this->players as $player) {
			$this->ci->boards_m->draw($dist[count($this->players)]['hand'], $player['id']);
		}
		
		// Activate the first player
		$this->ci->roles_m->next_turn();
	}
	
	/**
	 * Called everytime the ajax wants to update.
	 */
	public function update() {
		$this->players = $this->ci->roles_m->get_current_room_players();
		$this->deck = $this->ci->boards_m->get_deck();
		$this->hand = $this->ci->boards_m->get_hand();
	}
	
	public function move($deck_id, $options = array()) {
		$card = (array)$this->ci->cards_m->get($deck_id);
		return $this->ci->card->play($card, $options);
	}
	
	public function discard($deck_id) {
		$card = (array)$this->ci->boards_m->get($deck_id);
		$card['place'] = serialize(array('type' => 'discard'));
		return $this->ci->boards_m->update($card['id'], $card);
	}
	
	public function end_turn() {
		$this->ci->boards_m->draw();
		$this->ci->roles_m->next_turn();
	}
}