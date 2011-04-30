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
if (isset($messages)) echo $messages;
?>
<div class="col">
<form action="" method="POST" class="col_6">
	<fieldset class="">
		<legend>Login</legend>
		<div><label for="usernameinput">Username</label><input type="text" name="username" id="usernameinput" placeholder="Username" autofocus required="required"/></div>
		<div><label for="passwordinput">Password</label><input type="password" name="password" id="passwordinput" placeholder="Password" required="required"/></div>
		<input type="submit" value="Go to Mine Map!" name="loginsubmit" />
	</fieldset>
</form>
<form action="<?php echo base_url().'register'; ?>" method="POST" class="col_6">
	<fieldset class="">
		<legend>A New Miner? Join Us!</legend>
		<div><label for="emailinput">E-mail Address</label><input type="email" name="email" id="emailinput" placeholder="Email Address" required="required" title="E-mail validation will be sent"/></div>
		<div><label for="aliasinput">Alias (5-15 chars)</label><input type="text" name="username" id="aliasinput" placeholder="New Username" required="required" title="5 to 15 characters" /></div>
		<div><label for="newpassinput">New Password</label><input type="password" name="password" id="newpassinput" placeholder="Password" required="required"/></div>
		<input type="submit" value="Join the Miners!" />
	</fieldset>
</form>
</div>
<article class="col_10 col">
	<h2>About Saboteur Card Game</h2>
	<p>Saboteur is a mining-themed card game, designed by Frederic Moyersoen and published in 2004 by Z-Man Games.</p>
	<p>I can't say much about the rules. If you're new to Saboteur Card Game, you can read all about the rules <?php echo anchor(base_url().'Saboteur_US_Rules.pdf', 'here'); ?>.</p>
</article>
<article class="col_10 col">
	<h2>What the players say</h2>
	<?php
if (isset($comments) && count($comments)) :
	foreach($comments as $c) :
?>
<div class="comment-block">
	<p class="comment-prefix"><span class="comment-author"><?php echo $c['username']; ?></span> said:</p>
	<p class="comment-content"><?php echo $c['content']; ?></p>
	<address><?php echo standard_date('DATE_RFC850', $c['created_at']); ?></address>
</div>
<?php
	endforeach;
endif;
?>
</article>
<br class="clear" />
