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
	
	function selectGame() {
		$("#room-list option").dblclick(function() {
			$("#room-list").submit();
		});
		$("#room-list a").click(function(event) {
			event.preventDefault();
			$("#room-list").submit();
		});
	}
	
	/**
	 * Refreshes Game room list
	 * ルームのリストを更新する
	 */
	function refreshGameList() {
		$("#room-list select").smartupdater({
			url: 'room/ajax_list',
			dataType: 'json',
			minTimeout: 5000},
			function(data) {
				var str = '';
				$.each(data, function(i, v) {
					str += '<option value="'+i+'">'+v+'</option>';
				});
				$("#room-list select").html(str);
				selectGame();
			}
		);
	}
	
	/**
	 * Refreshes Logged in player list
	 * ログインしているプレヤーのリストを更新する
	 */
	function refreshPlayerList() {
		$("#login-list ul").smartupdater({
			url: 'room/ajax_players',
			dataType: 'json',
			minTimeout: 5000},
			function(data) {
				var str = '';
				$.each(data, function(i, v) {
					str += '<li>'+v.username+'</li>';
				});
				$("#login-list ul").html(str);
				$("#login-list div span").html(data.length);
			}
		);
	}
	
	refreshGameList();
	refreshPlayerList();
});