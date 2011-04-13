<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
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
 * @author Alexander
 * @property Template $template
 * @property Users_m $users_m
 * @property Rooms_m $rooms_m
 * @property Roles_m $roles_m
 */
class Client_Controller extends MY_Controller {

	/**
	 * Logged in User
	 * @var Mixed false if no user is logged in.
	 */
	protected $user;

  function  __construct() {
    parent::__construct();

		if ($this->users_m->logged_in()) {
			$this->user = $this->users_m->get_user();
			$this->data['user'] = $this->user;
		}
		
		if ($this->session->userdata('room_id')) {
			$this->data['room'] = $this->rooms_m->get_current();
		}
		
		if (!$this->_check_access()) {
			redirect('');
		}
		
		$this->template
			->append_metadata(css('52/reset.css'))
			->append_metadata(css('52/css3.css'))
			->append_metadata(css('52/general.css'))
			->append_metadata(css('52/grid.css'))
			->append_metadata(css('style.css'));

    $this->template
			->append_metadata(js('jquery/jquery-1.5.2.min.js'))
			->append_metadata(js('52/modernizr-1.7.min.js'))
			->append_metadata(js('52/selectivizr.js'));
			
		$this->template
			->set_partial('navigation', 'partials/navigation.php')
			->set_partial('header', 'partials/header.php')
			->set_partial('footer', 'partials/footer.php');
  }

	function _check_access() {
		$ignored_pages = array('login');

		$current_page = $this->uri->segment(1, 'login');

		if (in_array($current_page, $ignored_pages)) {
			return TRUE;
		}

		if ( ! $this->user) {
			return FALSE;
		}

		return TRUE;
	}
	
	function ping() {
		if ($this->users_m->ping($this->session->userdata('user_id'))) {
			echo '1';
		} else {
			echo '0';
		}
	}
	
	function ajax_players() {
		$users = $this->roles_m->get_current_room_players();
		echo json_encode($users);
	}
}
?>
