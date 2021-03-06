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
	
	$("#room-list option").live('dblclick', function() {
		$("#room-list").submit();
	});
	$("#room-list a").live('click', function(event) {
		event.preventDefault();
		$("#room-list").submit();
	});
	
	function updatePlayerList(data) {
		if (data == undefined) return;
		var str = '';
		$.each(data, function(i, v) {
			str += '<li';
			if (v.role != undefined
					&& v.role.lag != undefined
					&& v.role.lag == 1) {
				str += ' class="lag"';
			}
			str += '>'+v.player+'</li>';
		});
		$("#login-list ul").html(str);
		$("#login-list div span").html(data.length);
	}
	
	function updateRoomList(data) {
		if (data == undefined) return;
		var str = '';
		$.each(data, function(i, v) {
			str += '<option value="'+i+'">'+v+'</option>';
		});
		$("#room-list select").html(str);	
	}
	
	$("#room-list").smartupdater({
		url: 'presence/lobbyupdate',
		dataType: 'json',
		httpCache: true,
		minTimeout: 1000},
		function(data) {
			updatePlayerList(data.users);
			updateRoomList(data.rooms);
		}
	);
});