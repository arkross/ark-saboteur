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

class Install extends MY_Controller {
	public function __construct() {
		parent::__construct();
	}

	function _remap($method) {
		if ($method != 'clean') {
			$this->index($method);
		}
	}
	
	public function index($model = '') {
		if ($model == '') {
			foreach ($this->load->_ci_models as $model) {
				$this->{$model}->_create();
			}
		} else {
			if (!in_array($model, $this->load->_ci_models))
				$this->load->model($model);
			$this->{$model}->_create();
		}
	}

	public function clean() {
		foreach ($this->load->_ci_models as $model) {
			$this->{$model}->_drop();
		}
	}
}