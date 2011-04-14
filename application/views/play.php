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
<div id="board-game" class="left"></div>
<div id="round" class="left"><?php echo $room->title; ?> - Round <span id="round-count">1</span></div>

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
		<input id="chat-input" type="text" name="chat" placeholder="Chat here" autofocus />
	</form>
</div>

<div id="actions" class="left">
	<div id="role">You are a Saboteur</div>
	<div id="leave">Leave Room</div>
</div>

<br /><br /><br />
<div id="player-list" class="left">
	<ul>
		<li>
			<span class="player-name">Alexander</span>
			<?php echo image('gold.png'); ?>
			<span class="gold-count">0</span>
			<?php echo image('hoeoff.png'); ?>
			<?php echo image('lampoff.png'); ?>
			<?php echo image('cartoff.png'); ?>
			<br class="clear" />
		</li>
		<li class="active">
			<span class="player-name">Alexander</span>
			<?php echo image('gold.png'); ?>
			<span class="gold-count">0</span>
			<?php echo image('hoeoff.png'); ?>
			<?php echo image('lampoff.png'); ?>
			<?php echo image('cartoff.png'); ?>
			<br class="clear" />
		</li>
	</ul>
</div>
<br class="clear"/>
<div id="cards" class="left">
	<div id="hand-cards" class="left">
		<?php echo image('card-path-straight.png'); ?>
		<?php echo image('card-path-straight.png'); ?>
		<?php echo image('card-path-straight.png'); ?>
		<?php echo image('card-path-straight.png'); ?>
		<?php echo image('card-path-straight.png'); ?>
		<?php echo image('card-path-straight.png'); ?>
	</div>
	<div id="discard-pile" class="left">
		<div id="discard-cards"><?php echo image('discard-pile.png'); ?></div>
		<div id="discard-count">1</div>
	</div>
	<div id="deck" class="left">
		<div id="deck-cards"><?php echo image('deck-pile.png'); ?></div>
		<div id="deck-count">20</div>
	</div>
</div>
