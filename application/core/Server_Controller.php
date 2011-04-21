<?php if (!defined('BASEPATH'))	exit('No direct script access allowed');
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

/**
 * Manages the requests sent by Client Controllers
 * @author Alexander
 * @property Chat_Packets_m $chat_packets_m
 * @property Events_m $events_m
 */
class Server_Controller extends MY_Controller {
  public function __construct() {
		parent::__construct();
		
		// Not an Ajax Request? Just die
		$this->_isAjax() or die(header('HTTP/1.1 403 Forbidden'));
		
		$this->load->model('chat_packets_m');
		$this->load->model('events_m');
	}
	
	private function _isAjax() {
		return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
	}

	protected function _respond($data) {
		$checksum = md5($data);
		header('Content-type: text/html');
		header('ETag:'.$checksum);
		if ($checksum != $_SERVER['HTTP_IF_NONE_MATCH']) {
			echo $data;
		} else {
			if ($this->agent->browser() != 'Firefox')
				header('HTTP/1.1 304 Not Modified');
			else 
				header('HTTP/1.1 204 No Content');
		}
	}
}
?>
