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
 * Description of Client_Controller
 *
 * @author Alexander
 */
class Client_Controller extends MY_Controller {
  //put your code here
  function  __construct() {
    parent::__construct();

		$this->template
			->append_metadata(css('52/reset.css'))
			->append_metadata(css('52/css3.css'))
			->append_metadata(css('52/general.css'))
			->append_metadata(css('52/grid.css'))
			->append_metadata(css('52/forms.css'))
			->append_metadata(css('style.css'));

    $this->template
			->append_metadata(js('jquery/jquery.js'))
			->append_metadata(js('52/modernizr-1.7.min.js'))
			->append_metadata(js('52/selectivizr.js'));
			
		$this->template
			->set_partial('header', 'partials/header.php')
			->set_partial('footer', 'partials/footer.php');
  }
}
?>
