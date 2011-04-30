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
<!DOCTYPE html>
<html lang="<?php echo config_item('language'); ?>">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="description" content="A web-based Saboteur card game." />
		<meta name="keywords" content="saboteur,frederic moyersoen,arkross,alexander,card,game" />
		<title><?php echo 'Saboteur | '.$template['title']; ?></title>
		<link href="http://www.arkross.com" rel="author" />
		<link rel="shortcut icon" href="<?php echo base_url().'application/assets/img/favicon.png'; ?>" />
		<script type="text/javascript">
			var room_id = <?php if(isset($room->id)) echo $room->id; else echo '0'; ?>;
			var user_id = <?php if(isset($user->id)) echo $user->id; else echo '0'; ?>;
		</script>
		<?php echo $template['metadata']; ?>
	</head>
	<body>
		
		<div id="header" class="box-shadow">
			<header class="row">
				<?php echo $template['partials']['header']; ?>
			</header>
		</div>
		<div id="wrapper" class="row">
			<section>
				<?php echo $template['body']; ?>
			</section>

			<footer>
				<?php echo $template['partials']['footer']; ?>
			</footer>
		</div>
	</body>
</html>