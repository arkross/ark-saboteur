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
class Comments_m extends MY_Model {
	public function __construct() {
		parent::__construct();
		$this->_table = 'comments';
	}
	
	public function get_all() {
		$comments = $this->db
			->select('u.username, u.avatar, c.*')
			->join('users u', 'u.id = c.user_id', 'leftt')
			->order_by('created_at', 'desc')
			->get($this->_table.' c')
			->result_array();
		return $comments;
	}
	
	public function get_latest($limit = 3) {
		$comments = $this->db
			->select('u.username, u.avatar, c.*')
			->join('users u', 'u.id = c.user_id', 'leftt')
			->limit($limit)
			->order_by('created_at', 'desc')
			->get($this->_table.' c')
			->result_array();
		return $comments;
	}
	
	public function add($content) {
		$data = array(
			'user_id' => $this->session->userdata('user_id'),
			'content' => $content,
			'created_at' => now()
		);
		return $this->insert($data);
	}
	
	public function _create() {
		$query = "CREATE  TABLE IF NOT EXISTS `comments` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`user_id` INT NOT NULL ,
			`content` TEXT NULL ,
			`created_at` BIGINT NULL ,
			PRIMARY KEY (`id`) )
		ENGINE = InnoDB";
		
		return $this->db->query($query);
	}
}