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

class Users_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'users';
	}

	public function logged_in() {
		if ($this->session->userdata('user_id')) {
			return TRUE;
		}
		return FALSE;
	}

	public function get_user() {
		if ($user_id = $this->session->userdata('user_id')) {
			return $this->get($user_id);
		}
		return FALSE;
	}

	public function login($username, $password) {
		if (! $user = $this->get_by('username', $username)) {
			return FALSE;
		}
		if ($user->password == sha1($password) && $user->active == '1') {
			$this->session->set_userdata('user_id', $user->id);
			return TRUE;
		}
		return FALSE;
	}

	public function logout() {
		$this->session->unset_userdata('user_id');
	}

	public function _create() {
		$query = "CREATE  TABLE IF NOT EXISTS `users` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`username` VARCHAR(255) NOT NULL ,
			`password` VARCHAR(255) NOT NULL ,
			`email` VARCHAR(255) NOT NULL ,
			`avatar` TEXT NULL ,
			`treasure` INT NULL ,
			`active` TINYINT(1)  NOT NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB
		COMMENT = 'A user is registered via email. Every user has reputation' ";
		return $this->db->query($query);
	}

	public function _drop() {
		return $this->dbforge->drop_table($this->_table);
	}
}