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

$config['default_rules'] = array(
	'path' => 'not[occupied]'
);

$config['default_result'] = array(
	'path' => 'occupy',
	'action' => 'discard'
);

$config['distribution'] = array(
	3 => array(
		'saboteur' => 1,
		'miner' => 3,
		'hand' => 6
	),
	4 => array(
		'saboteur' => 1,
		'miner' => 4,
		'hand' => 6
	),
	5 => array(
		'saboteur' => 2,
		'miner' => 4,
		'hand' => 6
	),
	6 => array(
		'saboteur' => 2,
		'miner' => 5,
		'hand' => 5
	),
	7 => array(
		'saboteur' => 3,
		'miner' => 5,
		'hand' => 5
	),
	8 => array(
		'saboteur' => 3,
		'miner' => 6,
		'hand' => 4
	),
	9 => array(
		'saboteur' => 3,
		'miner' => 7,
		'hand' => 4
	),
	10 => array(
		'saboteur' => 4,
		'miner' => 7,
		'hand' => 4
	)
);