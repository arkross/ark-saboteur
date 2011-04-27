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
		$this->_parse($card, 'rules');
		if ($this->_run($options, 'rules')) {
			$this->_parse($card, 'result');
			return $this->_run($options, 'result');
		}
	}

	private function _parse($card, $var = 'rules') {
		$type = $card['type']['name'];
		
		if (array_key_exists($type, $this->$var)) {
			$this->{$var} = explode('|', $this->{$var}[$type]);
		}

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
				$v[$value] = '';
			}
		}
		$this->{$var} = $parsed;
	}

	private function _run($args = array(), $var = 'rules') {
		$success = true;
		print_r($this->{$var});
		foreach ($this->{$var} as $key => $value) {
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
		echo 'adding status: '.$args;
		return $this->ci->roles_m->add_status($details['target'], $status);
	}
	
	private function remove($args, $details = array()) {
		print_r($details);
		if (isset($details['target_status']))
			$args = $details['target_status'];
		elseif (is_array($args)) $args = $args[0];
		$status = (array)$this->ci->roles_m->get_status($details['target']);
		echo 'removing: '.$args;
		$status[$args] = 0;
		return $this->ci->roles_m->add_status($details['target'], $status);
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