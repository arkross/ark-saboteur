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
 * @property Card $card
 */
class Play extends Client_Controller {
	public function __construct() {
		parent::__construct();
		if (!$this->session->userdata('room_id') 
			|| $this->session->userdata('room_id') == 1) {
			redirect('room');
		}
		
		$this->load->model('chat_packets_m');
		$this->load->library('card');
	}

	function index() {
		$this->data['is_creator'] = $this->roles_m->is_creator() ? 'true' : 'false';
		$this->template
			->append_metadata(js('jquery/smartupdater-3.0.02beta.js'))
			->append_metadata(js('jquery/jQueryRotateCompressed.js'))
			->append_metadata(js('chat.js'))
			->append_metadata(js('game.js'))
			->build('play', $this->data);
	}
}