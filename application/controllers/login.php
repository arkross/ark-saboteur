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
 * @property Template $template
 */
class Login extends Client_Controller {
  //put your code here
  function __construct() {
    parent::__construct();
  }

  function index() {
    $this->template
			->append_metadata(css('52/forms.css'))
			->build('login');
  }
}
