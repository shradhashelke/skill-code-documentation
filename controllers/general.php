<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class General extends CI_Controller {
	/*
	 * Load homepage
	 */
	public function index()
	{
		//$this->load->view('home/index');
		$this->load->view('home/header');
		$this->load->view('home/index');
		$this->load->view('home/footer');
	}
	/*
	 * Save professional request
	 */
	function newprofessional()
	{
		$this->load->model("generalop");
		$this->generalop->saveprofessional();
		$this->session->set_flashdata("requested","Request has been submitted. After verification you will be acknowledged");
		redirect("secure");
	}
}

/* End of file secure.php */
/* Location: ./application/controllers/secure.php */