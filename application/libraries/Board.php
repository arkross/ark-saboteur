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
	
	var $playerwinner = array();
	
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
			if ($this->_clean()) echo 'Cleaning successful'."\n";
		}
		
		$this->players = $this->ci->roles_m->get_current_room_players(false);
		foreach($this->players as $player) {
			$this->ci->roles_m->add_status($player['player_id'], array(
				'pick_off' => 0,
				'wagon_off' => 0,
				'lantern_off' => 0
			));
		}
		
		// Applies roles to players
		$this->roles = $this->ci->card->build_role_cards(count($this->players));
		echo 'Roles applied to:'."\n";
		for ($i = 0; $i < count($this->players); $i++) {
			echo 'player: '.$this->players[$i]['player']."\n";
			$this->ci->roles_m->add_status($this->players[$i]['player_id'], array('role' => $this->roles[$i]));
		}
		
		// Builds deck cards
		echo 'Setting deck'."\n";
		$this->deck = $this->ci->card->build_deck();
		$this->ci->boards_m->set_deck($this->deck);
		
		// Distributes hand cards
		echo 'Handing out cards:'."\n";
		$dist = $this->ci->card->distribution;
		$count = count($this->players);
		foreach($this->players as $player) {
			echo 'player: '.$player['player'].' draws '.$dist[$count]['hand']."\n";
			$this->ci->boards_m->draw($dist[$count]['hand'], $player['player_id']);
		}
		
		// Prepares maze
		echo 'Preparing maze'."\n";
		$this->ci->boards_m->prepare_maze($this->ci->card->build_goal_cards());
		
		// Prepares gold
		echo "Preparing gold\n";
		if ($this->ci->rooms_m->get_round() <= 1) {
			$this->ci->boards_m->set_bank($this->ci->card->build_gold_cards());
		}
		
		// Activate the first player
		if ($this->ci->rooms_m->get_round() <= 1) {
			echo 'Activate player'."\n";
			$this->ci->roles_m->next_turn();
		}
	}
	
	/**
	 * Called everytime the ajax wants to update.
	 */
	public function update() {
		$this->win = '';
		$this->players = $this->ci->roles_m->get_current_room_players(false);
		
		$this->deck = $this->ci->boards_m->get_deck();
		$this->maze = $this->ci->boards_m->get_maze();
		$this->hand = $this->ci->boards_m->get_hand();
		
		// Checks whether hand cards of all players have been depleted
		$hands = array();
		$sabo_win = false || (count($this->deck) ? true : false);
		foreach($this->players as $player) {
			$hands[$player['player_id']] = $this->ci->boards_m->get_hand($player['player_id']);
			$sabo_win = $sabo_win || (count($hands[$player['player_id']]) ? true : false);
		}
		if (!$sabo_win && 
			(($this->ci->rooms_m->get_round() == 1 && $this->ci->rooms_m->is_playing())
			|| $this->ci->rooms_m->get_round() > 1)) {
			$this->win = 'saboteur';
		}
		
		// Checks whether the goal card has been reached
		
		if (count($this->maze) > 10
			&& $this->ci->boards_m->goal_opened()) {
			$this->win = 'gold-digger';
		}
		
		if ($this->win != '' && $this->ci->rooms_m->is_playing()) {
			$this->end_round();
		}
		
		if ($this->ci->rooms_m->get_round() > 3) {
			$this->end_game();
		}
	}
	
	/**
	 * Plays a card
	 * @param int $deck_id The card ID in the deck
	 * @param Array $options arguments containing target, and additional things
	 * @return Array Response
	 */
	public function move($deck_id, $options = array()) {
		$deck = (array)$this->ci->boards_m->get($deck_id);
		$card = $this->ci->cards_m->get($deck['card_id']);
		$return = $this->ci->card->play($card, $options);
		if ($return['response'] == true) {
			$this->ci->events_m->fire_event('game.play_card', array($card['name']));
		}
		$this->check_path();
		return $return;
	}
	
	/**
	 * Place a card into the discard pile
	 * @param int $deck_id The card ID in the deck
	 * @return Array Response
	 */
	public function discard($deck_id) {
		$card = (array)$this->ci->boards_m->get($deck_id);
		$card['place'] = array('type' => 'discard');
		return array('response' => $this->ci->boards_m->update($card['id'], $card), 'error' => '');
	}
	
	/**
	 * Draws a card from the deck and pass turn
	 */
	public function end_turn() {
		$this->ci->boards_m->draw();
		$this->ci->roles_m->next_turn();
	}
	
	/**
	 * Distributes gold
	 * @return void
	 */
	public function end_round() {
		if (!$this->ci->roles_m->is_creator()) return;
		
		$this->bank = $this->ci->boards_m->get_bank();

		// If Gold digger wins
		if ($this->win == 'gold-digger') {
			$reversed = array_reverse($this->players);
			$active = $this->ci->roles_m->get_active_player();

			do {
				array_push($reversed, array_shift($reversed));
				reset($reversed);
				$current = current($reversed);
			} while($current['player_id'] != $active['player_id']);

			$take = count($this->players);
			if ($take > 9) $take = 9;
			$take = array_slice($this->bank, 0, $take);
			$current = current($take);

			$this->ci->events_m->fire_event($active['player'].' gets gold first');
			$this->ci->boards_m->receive_gold($current['id'], $active['player_id']);
			$card = $this->ci->cards_m->get($current['card_id']);
			$this->ci->card->play($card, 
				array('target' => $active['player_id']));
			array_shift($take);
			next($reversed);
			foreach($take as $t) {
				while($current = current($reversed)) {
					if (next($reversed) === false) reset($reversed);
					if ($current['role']['role'] == 'gold-digger') {
						$this->ci->events_m->fire_event($current['player'].' gets gold');
						$this->ci->boards_m->receive_gold($t['id'], 
							$current['player_id']);
						$card = $this->ci->cards_m->get($t['card_id']);
						$this->ci->card->play($card, 
							array('target' => $current['player_id']));
						break;
					}
				}
			}
		}

		// If Saboteur wins
		else {
			$saboteurs = $this->ci->roles_m->get_players_by_role('saboteur');
			$quota = $this->ci->config->item('saboteur');
			$quota = $quota[count($saboteurs)];
			$bank = $this->bank;

			function mygsort($a, $b) {
				if ($a['gold'] < $b['gold']) return -1;
				if ($a['gold'] > $b['gold']) return 1;
				return 0;
			}
			usort($bank, 'mygsort');
			$bank = array_reverse($bank);

			foreach($saboteurs as $s) {
				reset($bank);

				// Greedy Algorithm
				$temp = 0;
				while ($temp != $quota) {
					$try = each($bank);
					if ($temp + $try['value']['gold'] > $quota) {
						continue;
					}
					$this->ci->events_m->fire_event($s['player'].' gets gold '.$try['value']['gold']);
					$this->ci->boards_m->receive_gold($try['value']['id'], $s['player_id']);
					$card = $this->ci->cards_m->get($try['value']['card_id']);
					$this->ci->card->play($card, array('target' => $s['player_id']));
					$temp += $try['value']['gold'];
					unset($bank[$try['key']]);
				}
			}
		}
	}
	
	/**
	 * Looks for players with the most gold
	 */
	public function end_game() {
		$max = 0;
		foreach($this->players as $p) {
			if ($p['role']['gold'] > $max)
				$max = $p['role']['gold'];
		}
		foreach($this->players as $p) {
			if ($p['role']['gold'] == $max) {
				$status = array('winner' => 1);
				$this->ci->roles_m->add_status($p['player_id'], $status);
			}
		}
	}
	
	public function _clean() {
		$hands = $this->ci->boards_m->get_hands();
		$ids = array();
		foreach($hands as $h) {
			$ids[] = $h['id'];
		}
		unset($hands);
		$this->hand = array();
		
		$this->deck = $this->ci->boards_m->get_deck();
		foreach($this->deck as $d) {
			$ids[] = $d['id'];
		}
		$this->deck = array();
		
		$this->maze = $this->ci->boards_m->get_maze();
		foreach ($this->maze as $m){
			$ids[] = $m['id'];
		}
		$this->maze = array();
		
		$this->discard = $this->ci->boards_m->get_discard();
		foreach($this->discard as $d) {
			$ids[] = $d['id'];
		}
		$this->discard = array();
		return $this->ci->boards_m->delete_many($ids);
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