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
class Cards_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'cards';
		$this->root_path = 'cards/';
		$this->pic_ext = 'jpg';
	}
	
	/**
	 * Get cards of a specified type
	 * @param String $type
	 * @return Mixed array of cards
	 */
	public function get_cards($type = 'all') {
		if ($type == 'all') {
			return $this->get_all();
		} else {
			$cards = $this->db
				->select('c.id, c.name, c.slug, c.effect, c.description, c.photo, c.quantity, t.name as type_name')
				->join('card_types t', 't.id = c.type', 'right')
				->where('t.name', $type)
				->get($this->_table.' c')
				->result_array();
			foreach ($cards as &$card) {
				$card['effect'] = unserialize($card['effect']);
//				$card['photo'] = unserialize($card['photo']);
			}
			return $cards;
		}
	}
	
	public function get($primary_value) {
		$card = (array)parent::get($primary_value);
		$card['type'] = $this->db
			->where('id', $card['type'])
			->get('card_types')
			->row_array();
//		$card['effect'] = unserialize($card['effect']);
//		$card['photo'] = unserialize($card['photo']);
		return $card;
	}
	
	public function get_all() {
		$cards = $this->db
			->select('c.id, c.name, c.slug, c.effect, c.description, c.photo, t.name as type_name')
			->join('card_types t', 't.id = c.type', 'right')
			->get($this->_table.' c')
			->result_array();
		foreach ($cards as &$card) {
			$card['effect'] = unserialize($card['effect']);
//			$card['photo'] = unserialize($card['photo']);
		}
		return $cards;
	}
	
	public function insert($data) {
		$type_id = $this->db->where('name', $data['type'])->get('card_types')->row();
		$type_id = $type_id->id;
		$slug = preg_replace('/[^a-zA-Z0-9 ]/', '', $data['name']);
		$data['slug'] = str_replace(' ', '-', strtolower($slug));
		$data['photo'] = $this->root_path.$data['photo'].'.'.$this->pic_ext;
		$data['type'] = $type_id;
		
		return parent::insert($data);
	}
	
	public function card_types() {
		return $this->db->get('card_types')->result_array();
	}
	
	public function _create() {
		$cards = "CREATE TABLE IF NOT EXISTS `cards` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`slug` VARCHAR(255) NOT NULL ,
			`name` VARCHAR(255) NOT NULL ,
			`description` TEXT NULL ,
			`photo` TEXT NULL ,
			`type` INT NOT NULL ,
			`effect` TEXT NOT NULL ,
			`quantity` INT NOT NULL DEFAULT 1 ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB
		COMMENT = 'The card detail. Used for user manual and to describe the effect in a serialized value.'";
		
		$type = "CREATE TABLE IF NOT EXISTS `card_types` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`name` VARCHAR(45) NOT NULL ,
			`photo` VARCHAR(255) NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB
		COMMENT = 'Path, Action, Role, or Nugget Card'";
		
		$this->db->query($cards);
		$this->db->query($type);
		
		$type = array(
			array(
				'name' => 'path',
				'photo' => 'cards/deck/40-BGOther.jpg'
			),
			array(
				'name' => 'action',
				'photo' => 'cards/deck/40-BGOther.jpg'
			),
			array(
				'name' => 'start',
				'photo' => 'cards/dest/30-BGStarDest.jpg'
			),
			array(
				'name' => 'goal',
				'photo' => 'cards/dest/30-BGStarDest.jpg'
			),
			array(
				'name' => 'gold',
				'photo' => 'cards/gold/gold-BG.jpg'
			),
			array(
				'name' => 'role',
				'photo' => 'cards/char/20-BGCharacter.jpg'
			)
		);

		foreach ($type as $t) {
			$this->db->insert('card_types', $t);
		}
		
		$data = array(
			// Role Cards
			array(
				'name' => 'Saboteur',
				'type' => 'role',
				'photo' => 'char/22-CharSaboteurBlue',
				'quantity' => 4,
				'effect' => array(
					'target' => 'player',
					'result' => 'role[saboteur]'
				),
				'description' => 'Saboteur has to prevent Miners from reaching the goal',
			),
			array(
				'name' => 'Gold Digger',
				'type' => 'role',
				'photo' => 'char/21-CharGoldDiggerBlue',
				'quantity' => 7,
				'effect' => array(
					'target' => 'player',
					'result' => 'role[miner]'
				),
				'description' => 'Gold Miner has to get the gold.'
			),
			
			// Gold Cards
			array(
				'name' => 'Gold Chunks 1',
				'type' => 'gold',
				'photo' => 'gold/gold-1',
				'quantity' => 16,
				'effect' => array(
					'target' => 'player',
					'result' => 'gold[1]'
				),
				'description' => 'Worth 1 chunks of gold'
			),
			array(
				'name' => 'Gold Chunks 2',
				'type' => 'gold',
				'photo' => 'gold/gold-2',
				'quantity' => 8,
				'effect' => array(
					'target' => 'player',
					'result' => 'gold[2]'
				),
				'description' => 'Worth 2 chunks of gold'
			),
			array(
				'name' => 'Gold Chunks 3',
				'type' => 'gold',
				'photo' => 'gold/gold-3',
				'quantity' => 4,
				'effect' => array(
					'target' => 'player',
					'result' => 'gold[3]'
				),
				'description' => 'Worth 3 chunks of gold'
			),
			
			// Start & Destination Cards
			array(
				'name' => 'Start',
				'type' => 'start',
				'photo' => 'dest/31-Start',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11111]'
				),
				'description' => 'The start path card'
			),
			array(
				'name' => 'Stone Left',
				'type' => 'goal',
				'photo' => 'dest/32-DestUpLeft',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11001]'
				),
				'description' => 'A fake goal card'
			),
			array(
				'name' => 'Stone Right',
				'type' => 'goal',
				'photo' => 'dest/32-DestUpRight',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11100]'
				),
				'description' => 'A fake goal card'
			),
			array(
				'name' => 'Gold Goal',
				'type' => 'goal',
				'photo' => 'dest/32-DestUpRightDownLeft',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11111]'
				),
				'description' => 'The true goal card'
			),
			
			// Action Cards
			array(
				'name' => 'Wagon Off',
				'type' => 'action',
				'photo' => 'deck/43-SpecialCartOff-x3',
				'quantity' => 3,
				'effect' => array(
					'target' => 'player',
					'rules' => 'not[wagon_off]',
					'result' => 'add[wagon_off]'
				),
				'description' => 'Disables the Mine Wagon.'
			),
			array(
				'name' => 'Pick Off',
				'type' => 'action',
				'photo' => 'deck/43-SpecialHoeOff-x3',
				'quantity' => 3,
				'effect' => array(
					'target' => 'player',
					'rules' => 'not[pick_off]',
					'result' => 'add[pick_off]'
				),
				'description' => 'Disables the Pick.'
			),
			array(
				'name' => 'Lantern Off',
				'type' => 'action',
				'photo' => 'deck/43-SpecialLampOff-x3',
				'quantity' => 3,
				'effect' => array(
					'target' => 'player',
					'rules' => 'not[lantern_off]',
					'result' => 'add[lantern_off]'
				),
				'description' => 'Disables the Lantern.'
			),
			array(
				'name' => 'Wagon On',
				'type' => 'action',
				'photo' => 'deck/43-SpecialCartOn-x2',
				'quantity' => 2,
				'effect' => array(
					'target' => 'player',
					'rules' => 'has[wagon_off]',
					'result' => 'remove[wagon_off]'
				),
				'description' => 'Re-enables the broken Wagon.'
			),
			array(
				'name' => 'Pick On',
				'type' => 'action',
				'photo' => 'deck/43-SpecialHoeOn-x2',
				'quantity' => 2,
				'effect' => array(
					'target' => 'player',
					'rules' => 'has[pick_off]',
					'result' => 'remove[pick_off]'
				),
				'description' => 'Re-enables the broken Pick.'
			),
			array(
				'name' => 'Lantern On',
				'type' => 'action',
				'photo' => 'deck/43-SpecialLampOn-x2',
				'quantity' => 2,
				'effect' => array(
					'target' => 'player',
					'rules' => 'has[lantern_off]',
					'result' => 'remove[lantern_off]'
				),
				'description' => 'Re-enables the broken Lantern.'
			),
			array(
				'name' => 'Pick Wagon On',
				'type' => 'action',
				'photo' => 'deck/44-SpecialHoeCartOn',
				'quantity' => 1,
				'effect' => array(
					'target' => 'player',
					'rules' => 'has[pick_off,wagon_off]',
					'result' => 'remove[pick_off,wagon_off]'
				),
				'description' => 'Re-enables Pick or Wagon (Choose one)'
			),
			array(
				'name' => 'Pick Lantern On',
				'type' => 'action',
				'photo' => 'deck/44-SpecialHoeLampOn',
				'quantity' => 1,
				'effect' => array(
					'target' => 'player',
					'rules' => 'has[pick_off,lantern_off]',
					'result' => 'remove[pick_off,lantern_off]'
				),
				'description' => 'Re-enables Pick or Lantern (Choose one)'
			),
			array(
				'name' => 'Wagon Lantern On',
				'type' => 'action',
				'photo' => 'deck/44-SpecialLampCartOn',
				'quantity' => 1,
				'effect' => array(
					'target' => 'player',
					'rules' => 'has[lantern_off,wagon_off]',
					'result' => 'remove[lantern_off,wagon_off]'
				),
				'description' => 'Re-enables Wagon or Lantern (Choose one)'
			),
			array(
				'name' => 'Road Off',
				'type' => 'action',
				'photo' => 'deck/45-SpecialRoadOff-x3',
				'quantity' => 3,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'occupied|maze_not[goal]|maze_not[start]',
					'result' => 'remove'
				),
				'description' => 'Destroys a path card, removing it from the maze.'
			),
			array(
				'name' => 'Map',
				'type' => 'action',
				'photo' => 'deck/45-SpecialMap-x6',
				'quantity' => 6,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'occupied|maze_is[goal]',
					'result' => 'peek'
				),
				'description' => 'Take a peek at one of the goal card.'
			),
			
			// Path Cards
			array(
				'name' => 'Path Right Left',
				'type' => 'path',
				'photo' => 'deck/41-PathRightLeft-x4',
				'quantity' => 3,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[10101]'
				)
			),
			array(
				'name' => 'Path Up Down',
				'type' => 'path',
				'photo' => 'deck/41-PathUpDown-x4',
				'quantity' => 4,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11010]'
				)
			),
			array(
				'name' => 'Path Up Left',
				'type' => 'path',
				'photo' => 'deck/41-PathUpLeft-x5',
				'quantity' => 4,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11001]'
				)
			),
			array(
				'name' => 'Path Up Right',
				'type' => 'path',
				'photo' => 'deck/41-PathUpRight-x5',
				'quantity' => 5,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11100]'
				)
			),
			array(
				'name' => 'Path Up Right Down',
				'type' => 'path',
				'photo' => 'deck/41-PathUpRightDown-x3',
				'quantity' => 5,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11110]'
				)
			),
			array(
				'name' => 'Path Up Right Left',
				'type' => 'path',
				'photo' => 'deck/41-PathUpRightLeft-x5',
				'quantity' => 5,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11101]'
				)
			),
			array(
				'name' => 'Path Up Right Down Left',
				'type' => 'path',
				'photo' => 'deck/41-PathUpRightDownLeft-x5',
				'quantity' => 5,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[11111]'
				)
			),
			
			// Blocked Cards
			array(
				'name' => 'Blocked Right',
				'type' => 'path',
				'photo' => 'deck/42-BlockedRight',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[00100]'
				)
			),
			array(
				'name' => 'Blocked Up',
				'type' => 'path',
				'photo' => 'deck/42-BlockedUp',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[01000]'
				)
			),
			array(
				'name' => 'Blocked Right Left',
				'type' => 'path',
				'photo' => 'deck/42-BlockedRightLeft',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[00101]'
				)
			),
			array(
				'name' => 'Blocked Up Down',
				'type' => 'path',
				'photo' => 'deck/42-BlockedUpDown',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[01010]'
				)
			),
			array(
				'name' => 'Blocked Up Right',
				'type' => 'path',
				'photo' => 'deck/42-BlockedUpRight',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[01100]'
				)
			),
			array(
				'name' => 'Blocked Up Left',
				'type' => 'path',
				'photo' => 'deck/42-BlockedUpLeft',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[01001]'
				)
			),
			array(
				'name' => 'Blocked Up Right Down',
				'type' => 'path',
				'photo' => 'deck/42-BlockedUpRightDown',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[01110]'
				)
			),
			array(
				'name' => 'Blocked Up Right Left',
				'type' => 'path',
				'photo' => 'deck/42-BlockedUpRightLeft',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[01101]'
				)
			),
			array(
				'name' => 'Blocked Up Right Down Left',
				'type' => 'path',
				'photo' => 'deck/42-BlockedUpRightDownLeft',
				'quantity' => 1,
				'effect' => array(
					'target' => 'maze',
					'rules' => 'adj[01111]'
				)
			)
		);
		
		foreach ($data as $d) {
			$this->insert($d);
		}
	}

	function _drop() {
		parent::_drop();
		$this->dbforge->drop_table('card_types');
	}
}