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
	var chat_id = 0;
	// Chat switch
	$("#switch li").click(function(event) {
		event.preventDefault();
		$("#switch li").removeClass("active");
		$(this).addClass("active");
		switch ($(this).html()) {
			case "Event":
				$(".chat-entry").hide();
				$(".event-entry").show();
				break;
			case "Chat":
				$(".chat-entry").show();
				$(".event-entry").hide();
				break;
			default:
				$(".event-entry, .chat-entry").show();
		}
	});
	
	function chatbox(box, input) {
		$(input).keydown(function(event) {
			if (event.keyCode == '13') {
				event.preventDefault();
				if ($(this).val() != '') {
					var message = $(this).val();
					$.post('chat/ajax_send', {'message': message});
					$(this).val('');
				}
			}
		});
		
		$(box).smartupdater({
			url: "chat/ajax_chatlog",
			data: {rev: chat_id},
			minTimeout: 2000,
			type: 'POST',
			dataType: 'json'
		}, function(data) {
			var str = '';
			$.each(data, function(i, v){
				str += '<span class="chat-entry"><span class="sender-name">';
				str += v.sender + ':</span>' + v.message + '</span>';
				chat_id = v.id;
				$(box).smartupdaterAlterUrl('chat/ajax_chatlog', {rev: chat_id});
			});
			$(box).append(str);
		});
	}
	
	chatbox("#chatlog", "#chat-input");
});
