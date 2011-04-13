/*!
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
	function messages(data) {
		if (data != '' && data != undefined) {
			$("#messages").html(data);
			if (!$("messages").is(':visible'))
				$("#messages").slideDown('slow');
		}
	}
	
	/**
	 * Ping the server to indicate that this user is still online
	 * サーバーをPingする、ユザーがオンラインかどうか
	 */
	function ping() {
		$("title").smartupdater({
			url: 'ping',
			minTimeout: 10000
		}, function(data){
			if (data == '0') {
				messages('You were disconnected');
			}
		});
	}
	
	ping();
	messages();
});