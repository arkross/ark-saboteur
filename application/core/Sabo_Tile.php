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
class Sabo_Tile {
	private $_up;
	private $_right;
	private $_down;
	private $_left;
	private $_core;
	
	var $up = false;
	var $right = false;
	var $down = false;
	var $left = false;
	
	var $coords = array();
	var $type = '';
	
	function __construct($data) {
		$adj = $data['adj'];
		$this->coords = $data['coords'];
		$this->type = $data['card_type'];
		$this->_core = $adj[0];
		$adj = array_slice($adj, 1);
		if (isset($data['reversed']) && $data['reversed']) {
			array_push($adj, array_shift($adj));
			array_push($adj, array_shift($adj));
		}
		$this->_up = $adj[0];
		$this->_right = $adj[1];
		$this->_down = $adj[2];
		$this->_left = $adj[3];
	}
	
	function str_coord($optional = 'current') {
		if ($optional == 'current')
			return $this->coords['x'].','.$this->coords['y'];
		if ($optional == 'up')
			return $this->coords['x'].','.($this->coords['y']-1);
		if ($optional == 'right')
			return ($this->coords['x']+1).','.$this->coords['y'];
		if ($optional == 'down')
			return $this->coords['x'].','.($this->coords['y']+1);
		if ($optional == 'left')
			return ($this->coords['x']-1).','.$this->coords['y'];
	}
	
//	function __set($name, &$value) {
//		if ($this->_{$name}) {
//			$this->{$name} = $value;
//		}
//	}
}