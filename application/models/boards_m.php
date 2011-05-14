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
 */
class Boards_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'boards';
	}
	
	/**
	 * Moves some cards from the deck to the hand
	 * @param int $count the card you want to draw
	 * @param int $player_id the player who draws the card, default is the current.
	 */
	public function draw($count = 1, $player_id = null) {
		if ($player_id === null)
			$player_id = $this->session->userdata('user_id');
		$deck = $this->get_deck();
		$draw = array();
		for ($i = 0; $i < $count; $i++) {
			array_push($draw, array_pop($deck));
		}
		unset($deck);
		foreach ($draw as $v) {
			if ($v == null) continue;
			$v['place']['type'] = 'player';
			$v['place']['id'] = $player_id;
			$v['place']['value'] = 'hand';
			$this->update($v['id'], $v);
		}
	}
	
	public function get_player_gold($player_id) {
		$all = (array)$this->get_many_by('room_id', $this->session->userdata('room_id'));
		$gold = array();
		foreach ($all as $c) {
			$c = (array)$c;
			$c['place'] = unserialize($c['place']);
			if (isset($c['place']['value']) &&
				$c['place']['value'] == 'gold' &&
				$c['place']['id'] == $player_id) {
				$gold[] = $c;
			}
		}
		return $gold;
	}
	
	public function receive_gold($deck_id, $player_id) {
		$card = (array)$this->get($deck_id);
		if (!$card) return false;
		$card['place'] = array(
			'type' => 'player',
			'id' => $player_id,
			'value' => 'gold'
		);
		return $this->update($card['id'], $card);
	}
	
	public function set_bank($bank) {
		$data = array(
			'room_id' => $this->session->userdata('room_id'),
			'place' => array('type' => 'bank')
		);
		foreach($bank as $card_id) {
			$data['card_id'] = $card_id;
			$this->insert($data);
		}
	}
	
	public function get_bank() {
		$all = (array)$this->get_many_by('room_id', $this->session->userdata('room_id'));
		$bank = array();
		foreach ($all as $card) {
			$card = (array)$card;
			$card['place'] = unserialize($card['place']);
			if ($card['place']['type'] == 'bank') {
				$detail = $this->cards_m->get($card['card_id']);
				$card['gold'] = $detail['effect']['result'];
				$card['gold'] = str_replace('gold[', '', $card['gold']);
				$card['gold'] = str_replace(']', '', $card['gold']);
				array_push($bank, $card);
			}
		}
		return $bank;
	}
	
	/**
	 * Flips a goal card face-up
	 * @param type $deck_id
	 * @return type 
	 */
	public function flip_up($goal) {
		$card = (array)$this->get($goal['id']);
		$card['place']['face_down'] = 0;
		$card['place']['reversed'] = ($goal['reverse'] ? 1 : 0);
		return $this->update($card['id'], $card);
	}
	
	/**
	 * Checks whether a goal card is already face up
	 * @param String $slug the card slug
	 * @return boolean true if opened
	 */
	public function goal_opened($slug = 'gold-goal') {
		$card = $this->cards_m->get_by('slug', $slug);
		$ingame_card = $this->db
			->where('room_id', $this->session->userdata('room_id'))
			->where('card_id', $card->id)
			->get($this->_table)
			->row_array();
		$ingame_card['place'] = unserialize($ingame_card['place']);
		return $ingame_card['place']['face_down'] == 0;
	}
	
	/**
	 * Sets tile at the specified coordinate
	 * @param int $deck_id
	 * @param Array $coords
	 * @param Array $optional
	 * @return true if tile set successfully
	 */
	public function set_tile($deck_id, $coords, $optional = array()) {
		$card = (array)$this->get($deck_id);
		$card['place'] = array(
			'type' => 'maze',
			'coords' => $coords,
			'face_down' => 0,
		);
		if (isset($optional['reversed']) && $optional['reversed']) {
			$card['place']['reversed'] = 1;
		}
		return $this->update($card['id'], $card);
	}
	
	/**
	 * Gets a tile at the specified coordinate
	 * @param Array $coords
	 * @return Mixed if exists, false otherwise 
	 */
	public function get_tile($coords) {
		$maze = $this->get_maze();
		foreach($maze as $m) {
			if ($m['place']['coords'] == $coords) {
				return $m;
			}
		}
		return false;
	}
	
	/**
	 * Gets current tile's neighbors
	 * @param type $coords 
	 */
	public function get_adj($coords) {
		$adjs = array();
		array_push($adjs, $this->get_tile(array('x' => $coords['x'], 'y' => $coords['y']-1)));
		array_push($adjs, $this->get_tile(array('x' => $coords['x']+1, 'y' => $coords['y'])));
		array_push($adjs, $this->get_tile(array('x' => $coords['x'], 'y' => $coords['y']+1)));
		array_push($adjs, $this->get_tile(array('x' => $coords['x']-1, 'y' => $coords['y'])));
		foreach ($adjs as &$a) {
			if ($a == false) continue;
			$card = (array)$this->cards_m->get($a['card_id']);
			$a['card'] = $card;
		}
		return $adjs;
	}
	
	/*
	 * Prepares maze, attaching start and goal cards
	 */
	public function prepare_maze($cards) {
		$data = array(
			'room_id' => $this->session->userdata('room_id'),
			'place' => array('type' => 'maze')
		);
		$goals = array(
			array('x' => 48, 'y' => 40),
			array('x' => 48, 'y' => 38),
			array('x' => 48, 'y' => 42)
		);

		foreach($cards as $card) {
			$data['card_id'] = $card['id'];
			if ($card['type_name'] == 'start') {
				$data['place']['coords'] = array('x' => 40, 'y' => 40);
				$data['place']['face_down'] = 0;
			} else {
				$coord = array_pop($goals);
				$data['place']['coords'] = $coord;
				$data['place']['face_down'] = 1;
			}
			$this->insert($data);
		}
	}
	
	/**
	 * Gets the entire maze cards
	 * @return array 
	 */
	public function get_maze() {
		$all = (array)$this->get_many_by('room_id', $this->session->userdata('room_id'));
		$maze = array();
		foreach ($all as &$card) {
			$card = (array)$card;
			$card['place'] = unserialize($card['place']);
			if ($card['place']['type'] == 'maze') {
				$card_id = $this->db
					->where('id', $card['card_id'])
					->limit(1)
					->get('cards')
					->row_array();
				$card_type = $this->db
					->where('id', $card_id['type'])
					->limit(1)
					->get('card_types')
					->row_array();
				$card['type_name'] = $card_type['name'];
				$card['card_detail'] = $card_id;
				array_push($maze, $card);
			}
		}
		return $maze;
	}
	
	/**
	 * Gets all hand cards
	 * @return array 
	 */
	public function get_hands() {
		$all = (array)$this->get_many_by('room_id', $this->session->userdata('room_id'));
		$hand = array();
		foreach ($all as $card) {
			$card = (array)$card;
			$card['place'] = unserialize($card['place']);
			if (isset($card['place']['value']) && 
				$card['place']['value'] == 'hand') {
				array_push($hand, $card);
			}
		}
		return $hand;
	}
	
	/**
	 * Gets hand cards of the current player
	 * @return Mixed array of card records
	 */
	public function get_hand($player_id = null) {
		if ($player_id === null) $player_id = $this->session->userdata('user_id');
		$all = (array)$this->get_many_by('room_id', $this->session->userdata('room_id'));
		$hand = array();
		foreach ($all as $card) {
			$card = (array)$card;
			$card['place'] = unserialize($card['place']);
			if (isset($card['place']['value']) && 
				$card['place']['value'] == 'hand' &&
				$card['place']['id'] == $player_id) {
				array_push($hand, $card);
			}
		}
		return $hand;
	}
	
	public function get_discard() {
		$all = (array)$this->get_many_by('room_id', $this->session->userdata('room_id'));
		$discard = array();
		foreach ($all as $card) {
			$card = (array)$card;
			$card['place'] = unserialize($card['place']);
			if ($card['place']['type'] == 'discard') {
				array_push($discard, $card);
			}
		}
		return $discard;
	}
	
	/**
	 * Gets the cards in deck
	 * @return Mixed array of card records
	 */
	public function get_deck() {
		$all = (array)$this->get_many_by('room_id', $this->session->userdata('room_id'));
		$deck = array();
		foreach ($all as $card) {
			$card = (array)$card;
			$card['place'] = unserialize($card['place']);
			if ($card['place']['type'] == 'deck') {
				array_push($deck, $card);
			}
		}
		return $deck;
	}
	
	/**
	 * Sets the deck with cards id
	 * @param Mixed $deck array of cards id
	 */
	public function set_deck($deck) {
		$data = array(
			'room_id' => $this->session->userdata('room_id'),
			'place' => array('type' => 'deck')
		);
		foreach($deck as $card_id) {
			$data['card_id'] = $card_id;
			$this->insert($data);
		}
	}

	public function _create() {
		$query = "CREATE TABLE IF NOT EXISTS `boards` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`room_id` INT NOT NULL ,
			`card_id` INT NOT NULL ,
			`place` VARCHAR(255) NOT NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB
		COMMENT = 'Nugget and Role Cards will target players. Path Cards will target board. Untargeted cards means discarded.'";
		
		return $this->db->query($query);
	}
}
