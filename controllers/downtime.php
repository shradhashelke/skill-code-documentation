<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Downtime extends CI_Controller {
	/*
	 * If there is any downtime
	 */
	public function index()
	{
		$this->load->view("downtime/header");
		$this->load->view("downtime/index");
		$this->load->view("downtime/footer");
	}
}

/* End of file downtime.php */
/* Location: ./application/controllers/downtime.php */