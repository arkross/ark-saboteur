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
    <title>Chat</title>
    <script src="application/js/jquery.js" type="text/javascript"></script>
    <script src="application/js/chatscript.js" type="text/javascript"></script>
    <link href="application/css/reset.css" type="text/css" rel="stylesheet" />
    <link href="application/css/style.css" type="text/css" rel="stylesheet" />
  </head>
  <body>
    <div id="wrapper">
      <div id="topspacer"></div>
      <div id="container">
        <div id="room_id"><?php echo $room_id; ?></div>
        <div id="sender"><?php echo $sender; ?></div>

        <div id="gamebox">
          <h1>Room <span id="roomname"><?php echo $roomname; ?></span></h1>
          <div id="gamecontent">The game goes here</div>
          <div class="right-align"><button style="display:block;"type="button" value="leave">Leave Room</button></div>
        </div>
        
        <div id="chatbox">
          <h1>Chatbox</h1>
          <div id="last_update">Last Updated At </div>
          <div id="history" readonly></div>
          <input type="text" size="40" id="message" />
        </div>
      </div>
      <div id="footer"></div>
    </div>
  </body>
</html>