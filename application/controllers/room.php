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
class Room extends Client_Controller {
	public function __construct() {
		parent::__construct();
		if ($this->session->userdata('room_id') > 1) {
			redirect('play');
		}
		
		$this->load->model('chat_packets_m');
		
		$this->template
			->append_metadata(js('jquery/smartupdater-3.0.02beta.js'))
			->append_metadata(js('general.js'))
			->append_metadata(js('chat.js'))
			->append_metadata(js('room.js'));
	}

	function index() {
		if ($_POST) {
			if ($this->input->post('room_name')) {
				$this->_create($this->input->post('room_name'));
			} elseif ($this->input->post('room_id')) {
				$this->_join($this->input->post('room_id'));
			}
		}
		$this->template->build('room', $this->data);
	}
	
	function _create($room_name) {
		// Automatically joins the room after creating it
		if ($id = $this->rooms_m->create($room_name)) {
			if (!$this->_join($id)) {
				$this->data['messages'] = 'Failed to join';
			} else {
				redirect('play');
			}
		} else {
			$this->data['messages'] = 'Failed to join';
		}
	}
	
	function _join($id) {
		if($this->rooms_m->enter($id)) {
			redirect('play');
		}
	}
}