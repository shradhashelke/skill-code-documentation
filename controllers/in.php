<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class In extends CI_Controller {

	public function p($profile_id)
	{
		$this->load->model("profile");
		$this->load->library('ciqrcode');
		$this->load->model('administrator');
		$data['profile']=$this->profile->getProfile($profile_id);
		$this->load->view('public/header',$data);
		$this->load->view('public/index',$data);
		$this->load->view('public/footer');
	}

}

/* End of file In.php */
/* Location: ./application/controllers/in.php */