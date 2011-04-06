<?php
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
?>
<html>
  <head>
    <title>Room List</title>
    <link href="application/css/reset.css" type="text/css" rel="stylesheet" />
    <link href="application/css/style.css" type="text/css" rel="stylesheet" />
    <script src="application/js/jquery.js" type="text/javascript"></script>
    <script type="text/javascript">
      $(document).ready(function() {
        $("#createinput").hide();
        $.ajaxSetup({
          timeout: 5000,
          async: true
        });
        function updateRoom() {
          $.post('chat_server/update_room', {}, function(data){
            curid = $("#roomlist > option:selected").val();
            $("#roomlist").html(data);
            $("#roomlist option[value=\""+curid+"\"]").attr('selected', 'selected');
            //            updateRoom();
          });
        }
        $("#createbutton").click(function(event){
          event.preventDefault();
          $("#createinput").slideToggle('fast', function() {
            $("#createname:visible").focus();
          });
        });

        $("#refresh").click(updateRoom);
        updateRoom();
      });
    </script>
  </head>
  <body>
    <div id="wrapper">
      <div id="topspacer"></div>
      <div id="roombox">
        <h1>Choose a Room</h1>
        <form action="" method="POST">
          <button id="refresh" type="button">Refresh List</button>
          <div id="div-roomlist"><select name="roomlist" id="roomlist" size="5"></select></div>
          <div class="right-align">Select a room from list above and press
          <input type="submit" value="Join Now" name="joinsubmit" id="joinsubmit" /></div>
        </form>
        <hr />
        <div class="left-align">or
        <input type="submit" value="Create your own room" name="create" id="createbutton" />
        </div>
        <div id="createinput">
          <form action="" method="POST">
            <input type="text" name="createname" id="createname" />
            <input type="submit" name="createsubmit" id="createsubmit" value="Create And Join" />
          </form>
        </div>
        <div class="right-align"><button id="logoutbutton" type="button">Logout</button></div>
      </div>
    </div>
  </body>
</html>