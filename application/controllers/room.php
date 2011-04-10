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
		$this->template->append_metadata(js('room.js'));
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
	
	function ajax_list() {
		$rooms = $this->rooms_m->dropdown();
		echo json_encode($rooms);
	}
	
	function _create($room_name) {
		$this->rooms_m->create($room_name);
	}
	
	function _join($id) {
		$this->rooms_m->enter($id);
	}
}