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
 * @property Comments_m $comments_m
 */
class Comments extends Client_Controller {
	public function __construct() {
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('comments_m');
	}
	
	public function index() {
		$this->_post();
		
		$this->data['comments'] = $this->comments_m->get_all();
		$this->template
			->title('Comments')
			->append_metadata(js('jquery/autoresize.jquery.min.js'))
			->append_metadata(css('52/forms.css'))
			->build('comments', $this->data);
	}
	
	public function _post() {
		if ($_POST) {
			$this->form_validation->set_rules('content', 'Comment Content', 'required|xss_clean');
			if ($this->form_validation->run()) {
				return $this->comments_m->add($this->input->post('content'));
			}
		}
	}
}