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

/**
 * @author Alexander
 */
class MY_Model extends Pyro_Model {
	
	public function MY_Model() {$this->__construct();}
	
	public function __construct() {
	  parent::__construct();
	}
	
	public function insert($data, $skip_validation = FALSE) {
		foreach($data as &$d) {
			if (is_array($d)) $d = serialize($d);
		}
		return parent::insert($data, $skip_validation);
	}
	
	public function update($primary_value, $data, $skip_validation = FALSE) {
		foreach($data as &$d) {
			if (is_array($d)) $d = serialize($d);
		}
		return parent::update($primary_value, $data, $skip_validation);
	}
	
	public function get($primary_value) {
		$res = parent::get($primary_value);
		foreach ($res as &$r) {
			if (is_string($r) && @unserialize($r) !== false) $r = unserialize($r);
		}
		return $res;
	}
}