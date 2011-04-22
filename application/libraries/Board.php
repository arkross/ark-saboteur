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
	var $deck;
	var $discard;
	var $roles;

	var $players;
	
	public function __construct() {
		$this->ci =& get_instance();

		$this->ci->load->config('board');

		$this->ci->load->library('card');
		$this->ci->load->model('boards_m');
	}

	public function prepare() {
		$this->roles = $this->ci->card->build_role_cards(count($players));
		$this->deck = $this->ci->card->build_deck();
		
	}
}