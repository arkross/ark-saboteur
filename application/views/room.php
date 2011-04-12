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
		<div class="tab-top background-grey col_6">Select a mine below and <a href="#">Join The Digging!</a></div>
		<select name="room_id" class="col_7" size="15" autofocus></select>
	</form>

	<form id="room-create" action="" method="POST">
		<div class="tab-top background-grey col_5">or start digging your own mine</div>
			<input type="text" name="room_name" placeholder="Give it a name and press Enter" />
	</form>
</div>

<div class="col_5 col" id="login-list">
	<div class="tab-top background-grey">Logged-in Miners (<span></span>)</div>
	<ul></ul>
</div>

<div id="chatbox" class="right">
	<nav id="switch">
		<ul>
			<li class="active">All</li>
			<li>Event</li>
			<li>Chat</li>
		</ul>
	</nav>

	<div id="chatlog">
		<span class="event-entry">Round 1 started</span>
		<span class="chat-entry"><span class="sender-name">Yudhi:</span> Hello, this is chatbox</span>
		<span class="chat-entry"><span class="sender-name">Alexander:</span> Hello, this is a reply text using 2 line</span>
		<span class="event-entry">Alexander plays a path card</span>
	</div>
	<form action="" method="POST">
		<input id="chat-input" type="text" name="chat" value="This is chat input" placeholder="Press Enter to set focus here" autofocus />
	</form>
</div>
<br class="clear" />