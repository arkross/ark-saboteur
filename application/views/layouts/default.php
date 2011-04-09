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
<!doctype html>
<html>
	<head>
		<title><?php echo $template['title']; ?></title>
		<script type="text/javascript" src="application/assets/js/jquery/jquery.js"></script>
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