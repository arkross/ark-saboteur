/* 
 * DO NOT REMOVE THIS LICENSE
 * 
 * This source code is created by Alexander.
 * You can use and modify this source code freely but
 * you are forbidden to change or remove this license.
 * 
 * Nick    : Alex
 * YM      : arch_angel_lx
 * Email   : konpaku.tomofumi@gmail.com
 * Blog    : http://arkross.co.cc
 * Company : http://mimicreative.net
 */

$(document).ready(function() {
  var room;
  var sender;

  $("#room_id").hide();
  $("#sender").hide();

  $.ajaxSetup({
    timeout: 3000
  });

  $("#curver").hide();
  curver = $("#curver").html();
  room = $("#room_id").html();
  sender = $("#sender").html();
  
  $("#message").focus();

  /* Input Box "Enter" Event */
  $("#message").keydown(function(event) {
    if (event.keyCode == 13) {
      existing = $("#history").html();
      if (existing.charCodeAt(0) != 13) {
        addition = $(this).val();
        $.post('chat_server', {
          "room_id": room,
          "sender": sender,
          "message":addition
        }, function(data) {
          $("#message").val('');
          $("#history").html(existing + "<br />" + data);
        });
      }
    }
  });

  function poll() {
    $.post('chat_server/update', {
      "room": room
    }, function(data) {
      $("#history").html(data);
      d = new Date();
      $("#last_update").html(
        "Last Updated At " + d.getHours() + ":" +
        (d.getMinutes().length == 1 ? "0" : "")+ d.getMinutes() + ":" +
        (d.getSeconds().length == 1 ? "0" : "") + d.getSeconds());
      }
    );
  }

  window.setInterval(poll, 1000);
});
