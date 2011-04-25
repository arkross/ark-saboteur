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
	var $distribution;

	public function __construct() {
		$this->ci =& get_instance();

		$this->ci->load->config('card');
//		$this->rules = $this->ci->config->item('default_rules');
		$this->distribution = $this->ci->config->item('distribution');
		$this->ci->load->model('cards_m');
		$this->ci->load->model('roles_m');
	}

	public function play($card, $options = array()) {
		$this->_parse_rules($card);
		return $this->_run_rules($options);
	}

	private function _parse_rules($card) {
		$type = $card['type']['name'];
		
		if (array_key_exists($type, $this->rules)) {
			$this->rules = explode('|', $this->rules[$type]);
		}

		$card_rule = explode('|', $card['effect']['rules']);
		$this->rules = array_merge($this->rules, $card_rule);
		$parsed = array();
		foreach ($this->rules as &$rule) {
			if (preg_match('/\[[a-zA-Z0-9_,]+\]/', $rule, $matches)) {
			  $key = str_replace($matches[0], '', $rule);
				$value = str_replace($key, '', $rule);
				$value = str_replace('[', '', $value);
				$value = str_replace(']', '', $value);
				$value = explode(',', $value);
				$parsed = array_merge($parsed, array($key => $value));
			} else {
				$rule[$value] = '';
			}
		}
		$this->rules = $parsed;
	}

	private function _run_rules($args = array()) {
		$success = true;
		foreach ($this->rules as $key => $value) {
			$success = ($success && $this->{$key}($value, $args));
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
		return !$this->{$args}($details);
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
		return $this->ci->roles_m->add_status($details['target'], $status);
	}
	
	private function remove($args) {
		
	}
	
	private function path($args) {
		
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
}