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
 */
class Users_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'users';
	}

	/**
	 * Check whether this user is logged in
	 * このユザーがログインしたのかい？
	 * @return boolean True if logged in
	 */
	public function logged_in() {
		if ($this->session->userdata('user_id')) {
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Gets current user
	 * 現在ログインしているユザーを手に入れる
	 * @return Mixed current user
	 */
	public function get_user() {
		if ($user_id = $this->session->userdata('user_id')) {
			return $this->get($user_id);
		}
		return FALSE;
	}

	/**
	 * Log a user in
	 * ログインする
	 * @param String $username
	 * @param String $password
	 * @return boolean true if successful
	 */
	public function login($username, $password) {
		if (! $user = $this->get_by('username', $username)) {
			return FALSE;
		}
		if ($user->password == sha1($password) && $user->active == '1') {
			$this->session->set_userdata('user_id', $user->id);
			$this->session->set_userdata('room_id', 1); //lobby
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Log this user out
	 * ログアウトする
	 */
	public function logout() {
		$this->session->unset_userdata('user_id');
	}
	
	/**
	 * Gets currently online players based on the last_seen
	 * 現在オンラインのプレヤーを手に入れる
	 * @return Mixed array of records
	 */
	public function get_online_players() {
		$this->db->where('last_seen >= ', now() - 10);
		$this->db->select('username, last_seen');
		return $this->db->get('users')->result_array();
	}
	
	/**
	 * Ping the server to indicate that this user is still online
	 * サーバーをPingする、このユザーがオンラインがどうか
	 * @param type $id
	 * @return type 
	 */
	public function ping($id) {
		if (! $user = $this->db->where('id', $id)->get($this->_table)->row_array()) {
			return FALSE;
		} else {
			$user['last_seen'] = now();
			if ($this->update($user['id'], $user)) {
				return TRUE;
			}
		}
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
			`last_seen` INT NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB
		COMMENT = 'A user is registered via email. Every user has reputation, represented by treasure.'";
		return $this->db->query($query);
	}
}