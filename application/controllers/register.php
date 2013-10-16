<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 * @property CI_Form_validation $form_validation
 * @property CI_Email $email
 */
class Register extends Client_Controller {
	public function __construct() {
		parent::__construct();
	}
	
	function index() {
		if (empty($_POST)) redirect('');
		
		$this->load->library('form_validation');
		$rules = array(
			array(
				'field' => 'email',
				'label' => 'E-mail Address',
				'rules' => 'required|valid_email|xss_clean'
			),
			array(
				'field' => 'username',
				'label' => 'Alias / Username',
				'rules' => 'required|min_length[5]|max_length[15]'
			),
			array(
				'field' => 'password',
				'label' => 'Password',
				'rules' => 'required'
			)
		);
		$this->form_validation->set_rules($rules);
		if ($this->form_validation->run()) {
			$data = array(
				'username' => $this->input->post('username'),
				'email' => $this->input->post('email'),
				'password' => sha1($this->input->post('password')),
				'active' => 0,
				'last_seen' => 0
			);
			if ($this->users_m->insert($data)) {
				$config = Array(
					'protocol' => 'smtp',
					'smtp_host' => 'ssl://smtp.googlemail.com',
					'smtp_port' => 465,
					'smtp_user' => 'alex@arkross.com',
					'smtp_pass' => 'darkslayer',
					'mailtype'  => 'html', 
					'charset'   => 'iso-8859-1'
				);
				$this->load->library('email', $config);
				$this->email->from('me@arkross.com', 'Saboteur Administrator');
				$this->email->to($data['email']);
				$this->email->subject('Account Verification');
				$body = 'Hi, '.$data['username']."<br /><br />";
				$body .= 'This email was used to register at '.anchor(base_url(), 'Ark\'s Saboteur').'<br />';
				$body .= 'If you are indeed registered, continue with following this link:<br />';
				$link = base_url().'register/activate/'.sha1($data['email']);
				$body .= anchor($link);
				$body .= '<br /><br />If you receive this email by mistake, please ignore and delete this message.';
				$this->email->message($body);
				if ($this->email->send()) {
					$this->template
						->title('Registration')
						->build('register', $this->data);
				} else {
					$this->template
						->title('Registration Failure')
						->build('register_failed', $this->data);
				}
			}
		} else {
			redirect ('');
		}
	}
	
	function activate($hash) {
		if ($this->users_m->activate($hash)) {
			$this->session->set_flashdata('message', 'Your account is now activated.');
		}
		redirect('');
	}
}