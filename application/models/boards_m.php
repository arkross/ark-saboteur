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
			$v['place']['type'] = 'player';
			$v['place']['id'] = $player_id;
			$v['place']['value'] = 'hand';
			$this->update($v['id'], $v);
		}
	}
	
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
				array_push($maze, $card);
			}
		}
		return $maze;
	}
	
	/**
	 * Gets hand cards of the current player
	 * @return Mixed array of card records
	 */
	public function get_hand() {
		$all = (array)$this->get_many_by('room_id', $this->session->userdata('room_id'));
		$hand = array();
		foreach ($all as $card) {
			$card = (array)$card;
			$card['place'] = unserialize($card['place']);
			if (isset($card['place']['value']) && 
				$card['place']['value'] == 'hand' &&
				$card['place']['id'] == $this->session->userdata('user_id')) {
				array_push($hand, $card);
			}
		}
		return $hand;
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
