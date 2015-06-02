<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Secure extends CI_Controller {
	/*
	 * dashboard
	 */
	public function index()
	{
		//$this->load->view('home/index');
		$this->load->view('home/header');
		$this->load->view('home/index');
		$this->load->view('home/footer');
	}
	/*
	 * Login for users
	 */
	public function login()
	{
			$this->load->model("securelogin");
			if($this->securelogin->studlogin()==1)
			{
				redirect("admin/student_register");
			}
			elseif($this->securelogin->teacherlogin()==1)
			{
				 redirect("teacher/step1");
			}
			elseif($this->securelogin->tpologin()==1)
			{
				redirect("tpo/step1");
			}
			elseif($this->securelogin->proflogin()==1)
			{
				redirect("tpo/step1");
			}
			else
			{
				redirect(base_url());
			}
	}
	/*
	 * Professional Verification
	 */
	public function verifyprofessional($token,$email)
	{
		$this->load->model("generalop");
		$this->generalop->validverify($token,$email);
	}
	/*
	 * show admin login menu after valid card is detected
	 */
	public function adminstep2(){
		echo $valid=$this->session->userdata("validcard");
		$valid=1; //This is just done to simulate the swapping the card. Comment it when you have a card and reader.
		if($valid){
			$this->load->view("admin/login/header");
			$this->load->view("admin/login/modallogin");
			$this->load->view("admin/login/footer");
		}
		else{
			redirect(base_url());
		}
	}
	/*
	 * Admin login next step
	 */
	public function nextstep(){
		$this->load->model("securelogin");
		if($this->securelogin->nextstep()){
			echo "Success";
		}
		else{
			$msg="<div class='alert alert-warning col-md-off-1 col-md-10 col-md-offset-1'><i class='icon icon-exclamation'></i> Invalid Credentials<a class='close' data-dismiss='alert' href='#'>&times;</a></div>";
			$this->session->set_flashdata("error",$msg);
			redirect("secure/adminstep2");
		}
	}
}

/* End of file secure.php */
/* Location: ./application/controllers/secure.php */