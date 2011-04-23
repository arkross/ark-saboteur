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
	
	public function set_deck($deck) {
		$data = array(
			'room_id' => $this->session->userdata('room_id'),
			'place' => serialize(array('type' => 'deck'))
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
