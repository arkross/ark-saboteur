<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
class Play extends Client_Controller {
	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('room_id')) {
			redirect('room');
		}
		
		$this->data['room'] = $this->rooms_m->get_current();
	}

	function index() {
		$this->template
			->append_metadata(js('general.js'))
			->append_metadata(js('game.js'))
			->build('play', $this->data);
	}
	
	function ajax_leave() {
		$this->rooms_m->quit();
		echo '1';
	}
}