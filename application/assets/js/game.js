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

jQuery.fn.outerHTML = function(s) {
	return (s)
	? this.before(s).remove()
	: jQuery("<p>").append(this.eq(0).clone()).html();
}

jQuery(document).ready(function($) {
	const WAITING = 0;
	const READY = 1;
	const CARD_CHOSEN = 2;
	const TARGET_CHOSEN = 3;
	var state = WAITING;
	
	var disabled = false;
	var is_playing = false;
	
	var playing_card;
	var target_status;
	var card, target;
	var round_count = 0;
	var last_hand;
	
	
	
	
	
	$("#role").tooltip({
		position: 'bottom center',
		effect: 'slide',
		predelay: 1000
	});
	
	$("#confirm-heal li").addClass('ui-widget-content');
	$("#confirm-heal li img").live('click', function(event) {
		event.preventDefault();
		target_status = $(this).attr("title");
		$.post('game/move', {
				'deck_id': card,
				'target': target,
				'target_status': target_status
			}, function(data) {
				if (data.response != true) {
					$("#message").html("Failed to discard!" + data.error);
					$("#message").dialog({modal:true});
				}
			}, 'json');
		$("#confirm-heal").dialog("close");
	});
	
	function showConfirmHeal(slug, target) {
		$("#confirm-heal li").hide();
		var heals = slug.split("-", 2);
		var statuses = new Array();
		$("li#player-"+target+" img").each(function(i, el) {
			if ($(el).attr('title') != "") {
				statuses.push($(el).attr('title').split("_", 1)[0]);
			}
		});
		var inference = new Array();
		$.each(statuses, function(i, v){
			if (heals.indexOf(v) > -1) {
				inference.push(v);
			}
		});
		$.each(inference, function(i, v){
			$("#confirm-heal li img[title=\""+v+"_off\"]").parent("li").show();
		});
		$("#confirm-heal").dialog({
			modal: true
		});
	}

	function get_slug(el) {
		var classes = $(el).attr("class");
		classes = classes.split(" ");
		var slug = classes[2];
		return slug.substr(5);
	}
	
	function update_actions(data) {
		if (data != undefined && data != false) {
		  var cloned = $("#all-cards img.slug-"+data);
			var tooltip = cloned.next('div').clone();
			cloned = cloned.clone();
			$("#role").html("Hover to see your role.");
			$("#actions .tooltip").html(tooltip.html());
		}
	}
	
	function update_board(data) {
	  if (data == undefined) return;
		var min = {x: 100, y: 100};
		var max = {x: 0, y: 0};
		$.each(data, function(i, c){
			if (min.x > c.place.coords.x) {min.x = c.place.coords.x;}
			if (min.y > c.place.coords.y) {min.y = c.place.coords.y;}
			if (max.x < c.place.coords.x) {max.x = c.place.coords.x;}
			if (max.y < c.place.coords.y) {max.y = c.place.coords.y;}
		});
		min.x = min.x -1;
		min.y = min.y -1;
		max.x++;
		max.y++;
		$("#board-game").html('');
		for (var i = min.y; i <= max.y; i++) {
			for (var j = min.x; j <= max.x; j++) {
				$("#board-game").append('<div id="tile-'+j+'-'+i+'" class="grid"></div>');
			}
			$("#board-game").append('<div class="clear-both"></div>');
		}
		$.each(data, function(i, c){
			var img = '';
			if (c.place.face_down == '1') {
				img = $("#card-types img.cardtype-"+c.type_name).clone();
				img = img.attr('class', '');
			} else {
				img = $("#all-cards img.card-"+c.card_id).clone();
			}
			img.css('width', 42);
			img.css('height', 57);
			img.css('margin-bottom', '-2px');
			if (c.place.reversed != undefined && c.place.reversed == '1') {
				$(img).rotate(180);
			}
			$("#tile-"+c.place.coords.x+"-"+c.place.coords.y).html(img);
		});
	}
	
	function update_players(data) {
		var str = '';
		state = WAITING;
		disabled = false;
		$.each(data, function(i, v) {
			str += '<li id="player-'+v.player_id+'"';
			if (v.role.active != undefined && v.role.active == 1 && is_playing == '1') {
				str += ' class="active"';
				if (v.player_id == user_id) {
					state = READY;
				}
			}
			str += '><span class="player-name';
			if (v.role.lag != undefined && v.role.lag == 1) {
				str += ' lag';
			}
			if (v.role.winner != undefined && v.role.winner == '1'){
				str += ' winner';
			}
			if (v.role.role != undefined) {
				str += ' '+v.role.role;
				str += '" title="' + v.role.role;
			}
			str += '">'+v.player+'</span>';
			if (v.role.gold != undefined) {
				str += gold_img + '<span class="gold-count">' + v.role.gold + '</span>';
				if (v.gold_cards != undefined) {
					str += '<div class="tooltip">';
					$.each(v.gold_cards, function(x, y) {
						var cloned = $('#all-cards img.card-'+y.card_id).clone(true);
						console.log($(cloned.get(0)).outerHTML());
						str += $(cloned.get(0)).outerHTML();
					})
					str += '</div>';
				}
			}
			if (v.role != undefined) {
				if (v.role.pick_off == '1') {
					str += pick_off_img;
					if (v.player_id == user_id) disabled = true;
				}
				if (v.role.lantern_off == '1') {
					str += lantern_off_img;
					if (v.player_id == user_id) disabled = true;
				}
				if (v.role.wagon_off == '1') {
					str += wagon_off_img;
					if (v.player_id == user_id) disabled = true;
				}
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
			var hand_ids;
			$.each(data.hand, function(i,v) {
				hand_ids += v.id;
			});
			if (last_hand != undefined && last_hand == hand_ids) return;
			last_hand = hand_ids;
			
			$('#hand-cards').html('');
			$.each(data.hand, function(i, v) {
				var cloned = $('#all-cards img.card-'+v.card_id).clone(true);
				cloned.attr('id', 'deck-' + v.id);
				cloned.appendTo('#hand-cards');
				cloned = $("#all-cards div.tooltip-"+v.card_id).clone();
				cloned.appendTo('#hand-cards');
			});
		}
	}
	
	function update_round(data) {
		if (data != undefined) {
			$("#playing").html(data);
		}
	}
	
	function update_winner(data) {
		if (data == undefined) return;
		$("#message").html(data);
		$("#message").dialog({modal:true});
	}
	
	function update_valid_room(data) {
		if (data == undefined) return;
		if (data != '1') {
			$("#leave").click();
		}
	}

	$("#playing").click(function(event) {
		event.preventDefault();
		if (is_playing == '1') return;
		if (is_creator) {
			if (parseInt(round_count) > 3) {
				$.get('presence/leave', '', function(data) {
					window.location = 'room';
				});
				return;
			}
			$.post('game/start_game', '', function(data){
				if (data.success == "0") {
					$("#message").html(data.error);
					$("#message").dialog({modal:true});
				}
			}, 'json');
		}
	});
	
	// Requests update for the whole game
	$("#deck").smartupdater({
		url: 'game/update',
		minTimeout: 1000,
		httpCache: true,
		dataType: 'json'
	}, function(data){
		is_playing = data.is_playing;
		update_valid_room(data.valid_room);
		update_round(data.round);
		update_actions(data.actions);
		update_players(data.players);
		update_cards(data.cards);
		update_board(data.maze);
		update_winner(data.winner);
		round_count = data.round_count;
		reloadDragDrop();
	});
	
	// Leave link click event
	$("#leave").click(function(event) {
		event.preventDefault();
		$.get('presence/leave', '', function(data) {
			if (data == '1') {
				window.location = 'room';
			}
		});
	});
	
	// Rotate hand cards
	$("#hand-cards img.type-path").live('dblclick', function(event) {
		event.preventDefault();
		if ($(this).attr('reversed') == '1') {
			$(this).rotate({angle:180, animateTo:360});
			$(this).attr('reversed', '0');
		} else {
			$(this).rotate({angle:0, animateTo: 180});
			$(this).attr('reversed', '1');
		}
	});
	
	// Select hand cards
	$("#hand-cards img").live('click', function(event) {
		event.preventDefault();
		if (state == WAITING) return;
		$("#hand-cards img").removeClass('selected');
		$(this).addClass('selected');
		state = CARD_CHOSEN;
		playing_card = $(this).attr('id');
	});
	
	// Targets Discard pile
	$("#discard-cards").live('click', function(event) {
		event.preventDefault();
		if (state != CARD_CHOSEN) return;
		var card = playing_card.substr(5);
		state = TARGET_CHOSEN;
		$.post('game/move', {
			'deck_id': card,
			'target':'discard'
		}, function(data) {
			if (data.response != true) {
				$("#message").html("Failed to discard!" + data.error);
				$("#message").dialog({modal:true});
			}
		}, 'json');
	});
	
	// Targets Maze
	$("#board-game div").live('click', function(event){
		event.preventDefault();
		if (state != CARD_CHOSEN) return;
		if (!$("img#"+playing_card).hasClass('target-maze')) return;
		var slug = get_slug($("img#"+playing_card));
		if (disabled && slug != 'map' && slug != 'road-off') {
			$("#message").html("You are currently disabled.");
			$("#message").dialog({modal: true});
			return;
		}
		var reversed = $("img#"+playing_card).attr("reversed");
		if (reversed == undefined) reversed = 0;
		card = playing_card.substr(5);
		target = $(this).attr('id');
		target = target.substr(5);
		state = TARGET_CHOSEN;
		$.post('game/move', {
			'deck_id': card,
			'target': target,
			'reversed': reversed
		}, function(data) {
			if (data.response == true) {
				if (slug == "map") {
					$("#peek").html('<img src="'+data.error+'" />');
					$("#peek").dialog({modal:true});
				}
			} else {
				state = CARD_CHOSEN;
				$("#message").html("Failed to Move! "+data.error);
				$("#message").dialog({modal:true});
			}
		}, 'json');
	});
	
	// Targets player
	$("#player-list li").live('click', function(event) {
		event.preventDefault();
		if(state != CARD_CHOSEN) return;
		if (!$("img#"+playing_card).hasClass('target-player')) return;
		var slug = get_slug($("img#"+playing_card));
		card = playing_card.substr(5);
		target = $(this).attr('id');
		target = target.substr(7);
		state = TARGET_CHOSEN;
		if (slug == "pick-wagon-on" || 
			slug == "pick-lantern-on" || 
			slug == "wagon-lantern-on") {
			showConfirmHeal(slug, target);
		} else {
			$.post('game/move', {
				'deck_id': card,
				'target': target
			}, function(data) {
				if (data.response != true) {
					state = CARD_CHOSEN;
					$("#message").html("Failed to Disable / Heal! "+data.error);
					$("#message").dialog({modal:true});
				}
			}, 'json');
		}
	});
	
	$("#discard-cards").droppable({
			hoverClass: "discard-hover",
			drop: function(event) {
				$(this).click();
			}
		});
	
	function reloadDragDrop() {
		$("#hand-cards img").tooltip({
			effect: 'slide',
			position: 'top right',
			predelay: 700
		});
		$(".gold-count").tooltip({
			effect: 'slide',
			predelay: 500,
			position: 'bottom center'
		});
		$("#hand-cards img").draggable({
			helper: function(event) {
				var el = $(this).clone().css('width', 42);
				el.css('height', 57);
				return el;
			},
			cursorAt: {cursor: "pointer", top: 28, left: 21},
			opacity: 0.9,
			start: function(event) {
				$(this).click();
			}
		});
		
		$("#board-game div").droppable({
			hoverClass: 'grid-hover',
			accept: '.target-maze',
			drop: function(event) {
				$(this).click();
			}
		});
		$("#player-list li").droppable({
			accept: '.target-player',
			hoverClass: 'player-hover',
			drop: function(event) {
				$(this).click();
			}
		});
	}
});