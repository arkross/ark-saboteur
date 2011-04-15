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
	<div id="login-text" class="right"><?php echo sprintf(lang('user.logged_in'), anchor('#', $user->username, 'id="login-name"')); ?></div><br />
	<div id="logout" class="right"><a href="logout"><?php echo lang('user.logout'); ?></a></div>
	<?php else : ?>
	<div id="login-text"><?php echo lang('user.not_logged_in'); ?></div>
	<?php endif; ?>
</div>