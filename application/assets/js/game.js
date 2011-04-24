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
	
	function update_actions(data) {
		if (data != undefined && data != false) {
			$("#role").html(data);
		}
	}
	
	function update_board(data) {
		
	}
	
	function update_players(data) {
		var str = '';
		$.each(data, function(i, v) {
			str += '<li';
			if (v.role.active != undefined && v.player.role.active == 1) str += ' class="active"'
			str += '><span class="player-name">'+v.player+'</span>';
			if (v.role.gold != undefined) {
				str += gold_img + '<span class="gold-count">' + v.role.gold + '</span>';
			}
			if (v.role.status != undefined) {
				if (v.player.status.pick_off == 1) str += pick_off_img;
				if (v.player.status.lantern_off == 1) str += lantern_off_img;
				if (v.player.status.wagon_off == 1) str += wagon_off_img;
			}
			str += '</li>';
		});
		$("#player-list ul").html(str);
	}
	
	function update_cards(data) {
		// update deck count
		if (data != undefined && data.deck_count != undefined) {
			$("#deck-count").html(data.deck_count);
		}
		
		// update hand cards
		if (data != undefined && data.hand != undefined) {
			$('#hand-cards').html('');
			$.each(data.hand, function(i, v) {
				$('img#card-'+v.card_id).clone(true).appendTo('#hand-cards');
			});
		}
	}
	
	function update_round(data) {
		if (data != undefined) {
			$("#playing").html(data);
		}
	}

	$("#playing").click(function(event) {
		event.preventDefault();
		$.post('game/start_game', '', function(data){
			$("#board-game").smartupdaterRestart();
		}, 'json');
	});
	
	// Requests update for the whole game
	$("#board-game").smartupdater({
		url: 'game/update',
		minTimeout: 5000,
		httpCache: true,
		dataType: 'json'
	}, function(data){
		update_round(data.round);
		update_actions(data.actions);
		update_players(data.players);
		update_cards(data.cards);
	});
	
	$("#leave").click(function(event) {
		event.preventDefault();
		$.get('presence/leave', '', function(data) {
			if (data == '1') {
				window.location = 'room';
			}
		});
	});
	
	$("#round").smartupdater({
		url: 'presence/validate_room',
		minTimeout: 5000,
		httpCache: true},
		function(data) {
			// room no longer exists
			if (data == '1') {
				$("#leave").click();
			}
		}
	);
	
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
			$(this).css('top', top*(grid_height+1) + offset.top + 2);
			$(this).css('left', left*(grid_width+1) + offset.left	+2);
		});
	}

	$(window).resize(setPosition());
	setPosition();
});