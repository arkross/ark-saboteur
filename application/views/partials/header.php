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
<div class="sabo-logo col_6 col"></div>
<?php echo $template['partials']['navigation']; ?>
<div id="logged-in" class="right">
	<?php if (isset($user)) : ?>
	<div id="login-avatar" class="right"><?php if ($user->avatar) echo img($user->avatar); ?></div>
	<div id="login-text" class="right">You are Logged in as <a href="#" id="login-name"><?php echo $user->username; ?></a></div><br />
	<div id="logout" class="right"><a href="logout">Logout</a></div>
	<?php else : ?>
	<div id="login-text">You are not logged in</div>
	<?php endif; ?>
</div>