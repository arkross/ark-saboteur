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

class Roles_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'roles';
	}
	
	public function set_role($role) {
		if ($user_id = $this->session->userdata('user_id')) {
			$user = $this->db->select('*')->get('users')->row();
		}
	}
	
	public function _create() {
		$query = "CREATE TABLE IF NOT EXISTS `roles` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`player_id` INT NOT NULL ,
			`role` VARCHAR(45) NOT NULL ,
			`room_id` INT NOT NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB";
		return $this->db->query($query);
	}
	
	public function _drop() {
		return $this->dbforge->drop_table($this->_table);
	}
}