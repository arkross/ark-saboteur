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
	var event_id = 0;
	
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
					$.post('chat/send', {'message': message});
					$(this).val('');
				}
			}
		});
		
		$(box).smartupdater({
			url: "chat",
			data: {chat_rev: chat_id, event_rev: event_id},
			minTimeout: 1000,
			httpCache: true,
			type: 'POST',
			dataType: 'json'
		}, function(data) {
			var str = '';
			$.each(data, function(i, v) {
				if (v.message != undefined) {
					str += '<span class="chat-entry"><span class="sender-name">';
					str += v.sender + ':</span>' + v.message + '</span>';
					if (chat_id < parseInt(v.id)) chat_id = v.id;
				} else {
					str += '<span class="event-entry id-'+v.id+'">'+v.string+"</span>";
					if (event_id < parseInt(v.id)) {
						event_id = v.id
					}
				}
			});
			$(box).smartupdaterAlterUrl('chat', {chat_rev: chat_id, event_rev: event_id});
			$(box).append(str);
			$(box).animate({ scrollTop: $(box).attr("scrollHeight") - $(box).height() });
		});
	}
	
	chatbox("#chatlog", "#chat-input");
});
