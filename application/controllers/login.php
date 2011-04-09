<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
 * Description of login
 *
 * @author Alexander
 * @property CI_Form_validation $form_validation
 */
class Login extends Client_Controller {
  //put your code here
  function __construct() {
    parent::__construct();
		$this->load->library('form_validation');
  }

  function index() {
		if ($_POST) {
			if ($this->_check_login()) {
				if ($this->users_m->login($this->input->post('username'), $this->input->post('password'))) {
					redirect('room');
				}
			} else {
				$this->data['messages'] = $this->form_validation->error_string('<div>', '</div>');
			}
		}
    $this->template
			->append_metadata(css('52/forms.css'))
			->build('login', $this->data);
  }

	function logout() {
		$this->users_m->logout();
		redirect('');
	}

	function _check_login() {
		$rules = array(
			array(
				'field' => 'username',
				'label' => 'Username',
				'rules' => 'required|xss_clean'
			),
			array (
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required|xss_clean'
			)
		);
		$this->form_validation->set_rules($rules);
		return $this->form_validation->run();
	}
}
