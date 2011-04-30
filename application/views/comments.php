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
<h1>Comments</h1>
<p id="brief">Please tell me your comments about this game, the application, any bugs you find, and anything.
Just don't spam here.</p>

<?php if (isset($user)) :?>
<script type="text/javascript">
	jQuery(document).ready(function($) {
		$("textarea").autoResize();
	});
</script>
<form id="comment-form" action="<?php echo base_url().'comments'; ?>" method="POST">
	<fieldset>
		<legend>Post your comment as <span class="comment-author"><?php echo $user->username; ?></span>:</legend>
		<div style="height: auto;"><textarea name="content" cols="50" autofocus required="required"></textarea></div>
		<input type="submit" value="Post comment"/>
	</fieldset>
</form>
<?php else : ?>
<p>You have to <?php echo anchor(base_url(), 'Login'); ?> first before posting a comment.</p>
<?php endif; ?>

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
