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
?>
<div id="messages"><?php if (isset($messages)) echo $messages; ?></div>
<div class="col_7 col">
	<form id="room-list" action="" method="POST">
		<div class="tab-top background-grey col_6"><?php echo lang('rooms.select'); ?></div>
		<select name="room_id" class="col_7" size="15" autofocus></select>
	</form>

	<form id="room-create" action="" method="POST">
		<div class="tab-top background-grey col_5"><?php echo lang('rooms.create'); ?></div>
			<input type="text" name="room_name" placeholder="Give it a name and press Enter" />
	</form>
</div>

<div class="col_4 col" id="login-list">
	<div class="tab-top background-grey"><?php echo lang('players.logged_in'); ?> (<span></span>)</div>
	<ul></ul>
</div>

<div id="chatbox" class="right">
	<nav id="switch">
		<ul>
			<li class="active"><?php echo lang('chat.all'); ?></li>
			<li><?php echo lang('chat.events'); ?></li>
			<li><?php echo lang('chat.chats'); ?></li>
		</ul>
	</nav>

	<div id="chatlog"></div>
	<form action="" method="POST">
		<input id="chat-input" type="text" name="chat" placeholder="Chat here" autofocus />
	</form>
</div>
<br class="clear" />