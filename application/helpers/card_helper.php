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

if (! function_exists('card')) {
	/**
	 * Gets a card record
	 * @param int $id the card ID
	 * @return Array a card record
	 */
	function card($id) {
		$ci =& get_instance();
		$ci->load->model('cards_m');
		$card = (array)$ci->cards_m->get($id);
		return $card;
	}
}

if (! function_exists('all_cards')) {
	/**
	 * Gets all card records
	 * @return Mixed array of card records
	 */
	function all_cards() {
		$ci =& get_instance();
		$ci->load->model('cards_m');
		$cards = (array)$ci->cards_m->get_all();
		return $cards;
	}
}

if (! function_exists('card_types')) {
	function card_types() {
		$ci =& get_instance();
		$ci->load->model('cards_m');
		$cards = (array)$ci->cards_m->card_types();
		return $cards;
	}
}