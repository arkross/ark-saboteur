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
class Sabo_Tree {
	
	var $head;
	var $tiles;
	
	function __construct($data) {
		$this->tiles = array();
		foreach($data as $d) {
			$str_coord = $d['coords'];
			$str_coord = $str_coord['x']. ','.$str_coord['y'];
			$this->tiles[$str_coord] = new Sabo_Tile($d);
		}
		$this->head = $this->tiles['40,40'];
		$this->expand($this->head);
	}
	
	function expand(&$node, $visited = array()) {
		if (in_array($node->str_coord(), $visited)) return;
		array_push($visited, $node->str_coord());
		$dir = array('up', 'right', 'down', 'left');
		
		foreach ($dir as $d) {
			if (array_key_exists($node->str_coord($d), $this->tiles)) {
				$node->up = $this->tiles[$node->str_coord($d)];
				$this->expand($node->up, $visited);
			}
		}
	}
}
