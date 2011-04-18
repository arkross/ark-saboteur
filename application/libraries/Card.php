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
	protected $rules;

	public function __construct() {
		$this->ci =& get_instance();

		$this->ci->load->config('card');
		$this->rules = $this->ci->config->item('default_rules');
		$this->distribution = $this->ci->config->item('distribution');
		$this->ci->load->model('cards_m');
	}

	public function play($card, $options = array()) {
		$this->_parse_rules($card);
	}

	private function _parse_rules($card) {
		$type = $card['type'];
		
		if (array_key_exists($type, $this->rules)) {
			$this->rules = explode('|', $this->rules[$type]);
		}

		$card_rule = explode('|', $card['effect']['rules']);
		$this->rules = array_merge($this->rules, $card_rule);

		foreach ($this->rules as &$rule) {
			if (preg_match('/\[[a-zA-Z0-9]+\]/', $rule, $matches)) {
			  $key = str_replace($matches[0], '', $rule);
				$value = str_replace($key, '', $rule);
				$value = str_replace('[', '', $value);
				$value = str_replace(']', '', $value);
				$rule[$key] = explode(',', $value);
			} else {
				$rule[$value] = '';
			}
		}
	}

	private function _run_rules($args = array()) {
		$success = true;
		foreach ($this->rules as $key => $value) {
			$success = ($success && $this->{$key}($value, $args));
		}
		return $success;
	}

	public function build_deck() {
		$deck = $this->cards_m->get_cards('action');
		$deck = array_merge($this->cards_m->get_cards('path'), $deck);
		foreach($deck as $key => $value) {
			$deck[$key] = $value['id'];
		}
		return shuffle($deck);
	}

	public function build_role_cards($player_count) {
		$saboteur = $this->distribution[$player_count]['saboteur'];
		$miner = $this->distribution[$player_count]['miner'];
		$rolecards = array();
		for($i = 0; $i < $saboteur; $i++) {
			array_push($rolecards, 'Saboteur');
		}
		for ($i = 0; $i < $miner; $i++) {
			array_push($rolecards, 'Gold Digger');
		}
		return shuffle($rolecards);
	}
}