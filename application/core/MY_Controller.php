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
 * @property Asset $asset
 * @property CI_Loader $load
 * @property CI_Benchmark $benchmark
 * @property CI_URI $uri
 * @property CI_Input $input
 * @property CI_Output $output
 * @property CI_Session $session
 */
class MY_Controller extends CI_Controller {
	var $data;
	public function __construct() {
		parent::__construct();
	}
}