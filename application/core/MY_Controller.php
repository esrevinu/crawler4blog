<?php if (!defined('BASEPATH')) exit('No direct access allowed.');

class Controller extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->assign('BASE_URL',base_url());
	}

	public function assign($key,$val) {
		$this->cismarty->assign($key,$val);
	}

	public function display($html) {
		$this->cismarty->display($html);
	}
}