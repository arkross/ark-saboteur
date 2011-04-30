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
?>
<nav id="menu" class="left">
	<ul>
		<li><?php echo anchor(base_url(), lang('menu.home'), array('rel'=>'index')); ?></li>
		<li><?php echo anchor(base_url().'comments', lang('menu.comments'), array('target' => '_blank', 'rel' => 'archives')); ?></li>
		<li><?php echo anchor('http://fredericmoyersoen.blogspot.com/', lang('menu.author'), array('target' => '_blank', 'rel' => 'external')); ?></li>
		<li><?php echo anchor('http://www.arkross.com', lang('menu.developer'), array('target' => '_blank', 'rel' => 'author')); ?></li>
		<li><?php echo anchor(base_url().'Saboteur_US_Rules.pdf', lang('menu.help'), array('target' => '_blank', 'rel' => 'external')); ?></li>
	</ul>
</nav>