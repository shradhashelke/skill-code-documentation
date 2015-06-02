<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Error extends CI_Controller {
	/*
	 * Default 404 page error
	 */
	public function index()
	{
		$this->load->view("404/header");
		$this->load->view("404/index");
		$this->load->view("404/footer");			
	}
	
} // end class