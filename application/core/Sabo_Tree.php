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
	
	var $goal = array();
	
	function __construct($data) {
		$this->tiles = array();
		foreach($data as $d) {
			$str_coord = $d['coords'];
			$str_coord = $str_coord['x']. ','.$str_coord['y'];
			$this->tiles[$str_coord] = new Sabo_Tile($d);
		}
		$this->head = $this->tiles['40,40'];
		$this->expand($this->head);
		unset($this->tiles);
		$this->traverse($this->head);
	}
	
	function expand(Sabo_Tile &$node, $visited = array()) {
		if (in_array($node->str_coord(), $visited)) return;
		array_push($visited, $node->str_coord());
		$dir = $node->allowed_dir();
		
		foreach ($dir as $d) {
			if (array_key_exists($node->str_coord($d), $this->tiles)) {
				$node->{$d} = $this->tiles[$node->str_coord($d)];
				$this->expand($node->{$d}, $visited);
			}
		}
	}
	
	function traverse($node, $visited = array(), $prev = null, $from = '') {
		if ($node == false) return;
		if (in_array($node->str_coord(), $visited)) return;
		if ($node->is_deadend()) return;
		if ($node->is_goal()) {
			if ($node->face_down) {
				$reverse = false;
				if (!$this->is_connected($node, $prev, $from)) {
					$reverse = true;
				}
				array_push($this->goal, array('id' => $node->deck_id, 'reverse' =>$reverse));
				return;
			}
		}
		
		array_push($visited, $node->str_coord());
		$dir = $node->allowed_dir();
		
		foreach ($dir as $d) {
			$this->traverse($node->{$d}, $visited, $node, $d);
		}
	}
	
	function is_connected(Sabo_Tile $node_a, Sabo_Tile $node_b, $from = '') {
		$allowed_a = $node_a->allowed_dir();
		if ($from == 'up' && in_array('down', $allowed_a)) {
			return true;
		}
		if ($from == 'down' && in_array('up', $allowed_a)) {
			return true;
		}
		if ($from == 'left' && in_array('right', $allowed_a)) {
			return true;
		}
		if ($from == 'right' && in_array('left', $allowed_a)) {
			return true;
		}
		return false;
	}
}
