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
 * @property MY_Controller $ci
 * @property Cards_m $cards_m
 */
class Card {
	protected $rules = array();
	protected $result = array();
	protected $default_rules = array();
	protected $default_result = array();
	
	var $error = '';
	var $response = true;
	var $distribution;

	public function __construct() {
		$this->ci =& get_instance();

		$this->ci->load->config('card');
		$this->default_rules = $this->ci->config->item('default_rules');
		$this->default_result = $this->ci->config->item('default_result');
		$this->distribution = $this->ci->config->item('distribution');
		$this->ci->load->model('cards_m');
		$this->ci->load->model('roles_m');
	}

	public function play($card, $options = array()) {
		$this->_parse($card, 'rules');
		if (($this->response = $this->_run($options, 'rules')) === true) {
			$this->_parse($card, 'result');
			return array('response'=>$this->_run($options, 'result'), 'error' => $this->error);
		} else {
			return array('response' => $this->response, 'error' => $this->error);
		}
	}

	private function _parse($card, $var = 'rules') {
		$type = $card['type']['name'];
		$default = 'default_'.$var;
		if (array_key_exists($type, $this->{$default})) {
			$this->{$var} = explode('|', $this->{$default}[$type]);
		}
		$card_rule = array();
		if (isset($card['effect'][$var]))
			$card_rule = explode('|', $card['effect'][$var]);
		$this->{$var} = array_merge($this->{$var}, $card_rule);
		$parsed = array();
		foreach ($this->{$var} as &$v) {
			if (preg_match('/\[[a-zA-Z0-9_,]+\]/', $v, $matches)) {
			  $key = str_replace($matches[0], '', $v);
				$value = str_replace($key, '', $v);
				$value = str_replace('[', '', $value);
				$value = str_replace(']', '', $value);
				$value = explode(',', $value);
				$parsed = array_merge($parsed, array($key => $value));
			} else {
				$parsed = array_merge($parsed, array($v => ''));
			}
		}
		$this->{$var} = $parsed;
	}

	private function _run($args = array(), $var = 'rules') {
		$success = true;
//		print_r($this->{$var});
		foreach ($this->{$var} as $key => $value) {
			$success = ($success && $this->{$key}($value, $args));
			if (!$success) {
				if ($key == 'not') {
					$this->error = lang('error.not.'.$value[0]);
				} else {
					$this->error = lang('error.'.$key);
				}
				break;
			}
		}
		return $success;
	}
	
	private function not($args, $details = array()) {
		if (is_array($args)) {
			$success = false;
			foreach($args as $arg) {
				$success = $success || $this->not($arg, $details);
			}
			return $success;
		}
		if (!method_exists(__CLASS__, $args)) {
			return !$this->has($args, $details);
		}
		return !$this->{$args}(array(), $details);
	}
	
	private function has($args, $details = array()) {
		if (is_array($args)) {
			$success = false;
			foreach($args as $arg) {
				$success = $success || $this->has($arg, $details);
			}
			return $success;
		}
		$status = (array)$this->ci->roles_m->get_status($details['target']);
		return (isset($status[$args]) && $status[$args]);
	}
	
	private function add($args, $details = array()) {
		if (is_array($args)) {
			$success = false;
			foreach($args as $arg) {
				$success = $success || $this->add($arg, $details);
			}
			return $success;
		}
		$status = (array)$this->ci->roles_m->get_status($details['target']);
		$status[$args] = 1;
		echo 'adding status: '.$args;
		return $this->ci->roles_m->add_status($details['target'], $status);
	}
	
	private function remove($args, $details = array()) {
		if (isset($details['target_status']))
			$args = $details['target_status'];
		elseif (is_array($args)) $args = $args[0];
		$status = (array)$this->ci->roles_m->get_status($details['target']);
		$status[$args] = 0;
		return $this->ci->roles_m->add_status($details['target'], $status);
	}
	
	private function maze_not($args, $details = array()) {
		return !$this->maze_is($args, $details);
	}
	
	private function maze_is($args, $details = array()) {
		$coords = explode('-', $details['target']);
		$coords = array('x' => $coords[0],	'y' => $coords[1]);
		unset($coords[0]);
		unset($coords[1]);
		$tile = $this->ci->boards_m->get_tile($coords);
		if (isset($tile) && $tile['type_name'] == $args[0]) return true;
		else return false;
	}
	
	private function occupied($args, $details = array()) {
		$coords = explode('-', $details['target']);
		$coords = array('x' => $coords[0],	'y' => $coords[1]);
		unset($coords[0]);
		unset($coords[1]);
		$tile = $this->ci->boards_m->get_tile($coords);
		if ($tile) return true;
		return false;
	}
	
	private function adj($args, $details = array()) {
		$coords = explode('-', $details['target']);
		$coords = array('x' => $coords[0],	'y' => $coords[1]);
		unset($coords[0]);
		unset($coords[1]);
		
		// Takes the target's adj map
		$adj = str_split($args[0]);
		$adj = array_slice($adj, 1);
		if (isset($details['reversed']) && $details['reversed']) {
			array_push($adj, array_shift($adj));
			array_push($adj, array_shift($adj));
		}
		
		// Takes all neighboring cards
		$neighbors = $this->ci->boards_m->get_adj($coords);
		foreach($neighbors as &$n) {
			if ($n === false) continue;
			
			// If it's neighboring face down goal card, don't count it as adjacent.
			if ($n['place']['face_down']) {
				$n = false;
				continue;
			}
			
			$n_adj = $n['card']['effect']['rules'];
			$n_adj = str_replace('adj[', '', $n_adj);
			$n_adj = str_replace(']', '', $n_adj);
			$n_adj = str_split($n_adj);
			$n_adj = array_slice($n_adj, 1);
			if (isset($n['place']['reversed']) && $n['place']['reversed']) {
				array_push($n_adj, array_shift($n_adj));
				array_push($n_adj, array_shift($n_adj));
			}
			$n = $n_adj;
		}
		
		// Match the adjacency
		$success = false;
		for ($i = 0; $i < count($adj); $i++) {
			if ($neighbors[$i] === false) continue;
			if ($adj[$i] !== $neighbors[$i][($i + 2) % 4]) return false;
			elseif($adj[$i] == 1) $success = true;
		}
		
		return $success;
	}
	
	private function occupy($args, $details = array()) {
		$coords = explode('-', $details['target']);
		$coords = array('x' => $coords[0], 'y' => $coords[1]);
		unset($coords[0]);
		unset($coords[1]);
		return $this->ci->boards_m->set_tile($details['deck_id'], $coords, $details);
	}
	
	private function peek($args, $details = array()) {
		$coords = explode('-', $details['target']);
		$coords = array('x' => $coords[0],	'y' => $coords[1]);
		unset($coords[0]);
		unset($coords[1]);
		$card = $this->ci->boards_m->get_tile($coords);
		$card = $this->ci->cards_m->get($card['card_id']);
		$this->error = base_url().$card['photo'];
		return true;
	}
	
	private function maze_remove($args, $details = array()) {
		$coords = explode('-', $details['target']);
		$coords = array('x' => $coords[0],	'y' => $coords[1]);
		unset($coords[0]);
		unset($coords[1]);
		$card = $this->ci->boards_m->get_tile($coords);
		return $this->discard($args, array('deck_id' => $card['id']));
	}
	
	private function discard($args, $details) {
		$card = (array)$this->ci->boards_m->get($details['deck_id']);
		$card['place'] = array('type' => 'discard');
		return $this->ci->boards_m->update($card['id'], $card);
	}

	public function build_deck() {
		$cards = $this->ci->cards_m->get_cards('action');
		$cards = array_merge($this->ci->cards_m->get_cards('path'), $cards);
		$deck = array();
		foreach($cards as $card) {
			for ($i = 0; $i < $card['quantity']; ++$i) {
				array_push($deck, $card['id']);
			}
		}
		
		shuffle($deck);
		return $deck;
	}

	public function build_role_cards($player_count) {
		$saboteur = $this->distribution[$player_count]['saboteur'];
		$miner = $this->distribution[$player_count]['miner'];
		$rolecards = array();
		for($i = 0; $i < $saboteur; $i++) {
			array_push($rolecards, 'saboteur');
		}
		for ($i = 0; $i < $miner; $i++) {
			array_push($rolecards, 'gold-digger');
		}
		shuffle($rolecards);
		return $rolecards;
	}
	
	public function build_goal_cards() {
		$cards = $this->ci->cards_m->get_cards('start');
		$goals = $this->ci->cards_m->get_cards('goal');
		shuffle($goals);
		$cards = array_merge($cards, $goals);
		$deck = array();
		foreach($cards as $card) {
			for ($i = 0; $i < $card['quantity']; ++$i) {
				array_push($deck, $card);
			}
		}
		return $deck;
	}
}