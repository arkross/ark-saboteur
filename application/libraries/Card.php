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
 */
class Card {
	protected $rules;

	public function __construct() {
		$this->ci =& get_instance();

		$this->ci->load->config();
		$this->rules = $this->ci->config->item('default_rules');

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

		$card_rule = explode('|', $card['effect']['rule']);
		$this->rules = array_merge($this->rules, $card_rule);
	}

	private function _run_rules($args = array()) {
		
	}

	public function build_deck() {
		
	}
}