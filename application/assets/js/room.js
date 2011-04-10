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
	
	function refreshGameList() {
		$.post('room/ajax_list', '', function(data) {
			console.log(data);
			var str = '';
			$.each(data, function(i, v) {
				str += '<option value="'+i+'">'+v+'</option>';
			});
			$("#room-list select").html(str);
		}, 'json');
	}
	refreshGameList();
	window.setInterval(refreshGameList, 10000);
});