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

jQuery(document).ready(function($) {
	
	$("#player-list").smartupdater({
		url: 'play/ajax_players',
		minTimeout: 5000,
		dataType: 'json'},
		function(data) {
			var str = '';
			$.each(data, function(i, v) {
				str += '<li><span class="player-name">'+v.player+'</span></li>';
			});
			$("#player-list ul").html(str);
		}
	);
	
	$("#leave").click(function(event) {
		event.preventDefault();
		$.get('play/ajax_leave', '', function(data) {
			if (data == '1') {
				window.location = 'room';
			}
		});
	});
	
	var grid_x_count = 11;
	var grid_y_count = 7;
	var grid_width = 42;
	var grid_height = 57;

	for (var i = 0; i < grid_y_count; i++) {
		for (var j = 0; j < grid_x_count; j++) {
			$("#board-game").append('<div class="grid"></div>');
		}
	}

	function setPosition() {
		$("#board-game").children("div").each(function(index, el) {
			var offset = $("#board-game").offset();
			var left = index % grid_x_count;
			var top = (index - left )/ grid_x_count;
			console.log(index + " / " + grid_x_count);
			$(this).css('top', top*(grid_height+1) + offset.top + 2);
			$(this).css('left', left*(grid_width+1) + offset.left	+2);
		});
	}

	$(window).resize(setPosition());
	setPosition();
});