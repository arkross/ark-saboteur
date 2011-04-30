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
	 * このユーザがログインしたのかい？
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
	 * 現在ログインしているユーザを手に入れる
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
			return $this->rooms_m->enter(1); //lobby
		}
		return FALSE;
	}

	/**
	 * Log this user out
	 * ログアウトする
	 */
	public function logout() {
		$this->rooms_m->quit();
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
	 * サーバーをPingする、このユーザがオンラインがどうか
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
		return FALSE;
	}
	
	public function activate($hash) {
		$user = $this->db
			->where('sha1(`email`)', $hash)
			->get($this->_table)
			->row_array();
		if ($user) {
			$user['active'] = 1;
			return $this->update($user['id'], $user);
		} else {
			return false;
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
		
		$insert = "INSERT INTO `users` (`id`, `username`, `password`, `email`, `avatar`, `treasure`, `active`, `last_seen`) VALUES
		(1, 'arkross', '85ec7356ea22f8b27867e8fc810525103d7942f3', 'nikolas.l.alexander@gmail.com', NULL, 0, 1, 1304157642),
		(2, 'arkyrion', 'c7629f8beed289cb9f40d97a58806a4d9f8efddd', 'nikolas.alexander@live.jp', NULL, 0, 1, 1304157638),
		(3, 'arkyria', '85ec7356ea22f8b27867e8fc810525103d7942f3', 'nikolas_alexander@ymail.com', NULL, NULL, 1, 0);";
		
		return ($this->db->query($query) && $this->db->query($insert));
	}
}