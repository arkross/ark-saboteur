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
<script type="text/javascript">
	var gold_img = '<?php echo image('gold.png'); ?>';
	var pick_off_img = '<?php echo image('hoeoff.png', null, array('title' => 'pick_off')); ?>';
	var lantern_off_img = '<?php echo image('lampoff.png', null, array('title' => 'lantern_off')); ?>';
	var wagon_off_img = '<?php echo image('cartoff.png', null, array('title' => 'wagon_off')); ?>';
</script>
<div id="all-cards" class="hide">
	<?php 
	$cards = all_cards();
	foreach($cards as $card) {
		echo '<img src="'.base_url().$card['photo'].'" class="card-'.$card['id'];
		echo ' target-'.$card['effect']['target'];
		echo ' slug-'.$card['slug'];
		echo ' type-'.$card['type_name'];
		echo '" title="'.$card['name'].'" />';
	}
	unset($cards);
	?>
</div>
<div id="card-types" class="hide">
	<?php
	$cards = card_types();
	foreach($cards as $card) {
		echo '<img src="'.base_url().$card['photo'].'" class="cardtype-'.$card['name'].'" />';
	}
	?>
</div>
<div id="confirm-heal" class="hide" title="Select the status you want to heal">
	<ul>
		<li><?php echo image('hoeoff.png', null, array('title' => 'pick_off')); ?></li>
		<li><?php echo image('lampoff.png', null, array('title' => 'lantern_off')); ?></li>
		<li><?php echo image('cartoff.png', null, array('title' => 'wagon_off')); ?></li>
	</ul>
</div>
<div id="board-game" class="left"></div>
<div id="round" class="left"><?php echo $room->title; ?> - <span id="playing">Start Game</span></div>

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

<div id="actions" class="left">
	<div id="role"></div>
	<div id="leave"><?php echo lang('game.leave'); ?></div>
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
	<div id="hand-cards" class="left"></div>
	<div id="discard-pile" class="left">
		<div id="discard-cards"><?php echo image('discard-pile.png'); ?></div>
	</div>
	<div id="deck" class="left">
		<div id="deck-cards"><?php echo image('deck-pile.png'); ?></div>
		<div id="deck-count">0</div>
	</div>
</div>
<div id="peek" class="hide" title="Peek"></div>
<div id="message" class="hide" title="Attention"></div>
