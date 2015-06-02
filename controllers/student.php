<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Student extends CI_Controller {
	/*
	 * Controller Function
	 */
	public function Student(){
		parent::__construct();
		if($this->session->userdata("userdetails")==NULL){
			redirect(base_url());
		}else{
			$obj=$this->session->userdata("loggedinas");
			if($obj=="professional"){
				redirect("professional/home");
			}elseif($obj=="teacher"){
				redirect("teacher/home");
			}elseif($obj=="tpo"){
				redirect("tpo/home");
			}
		}
	}
	/*
	 * Show Profile
	 */
	public function profile()
	{
		$this->load->model("studentop");
		$this->load->model("administrator");
		$this->studentop->updateDetails();
		$this->load->library('ciqrcode');
		$this->load->view("student/profile/header");
		$this->load->view("student/profile/index");
		$this->load->view("student/profile/footer");
	}
	/*
	 * new student registration
	 */
	public function registerstudent()
	{
		$this->load->model("studentop");
		$this->studentop->registerstud();
	}
	public function student_register()
	{
		$this->load->view('registration/student/header');
		$this->load->view('registration/student/index');
		$this->load->view('registration/student/footer');
	}
	public function step2()
	{
		//$this->load->view('home/index');
		$this->load->library('form_validation');
		$this->load->view('registration/student/header');
		$this->load->view('registration/student/step2');
		$this->load->view('registration/student/footer');
	}
	public function step3()
	{
		$this->load->view('registration/student/header');
		$this->load->view('registration/student/step3');
		$this->load->view('registration/student/footer');
	}
	public function savestep2()
	{
		$this->load->helper(array('form', 'url'));
		$this->load->library('form_validation');
		$this->form_validation->set_rules('password', 'Password', 'required|min_length[6]|matches[passconf]');
		$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required');
		$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
		if ($this->form_validation->run() == FALSE)
		{
			//$this->load->view('myform');	
			$this->step2();
		}
		else
		{
			//$this->load->view('formsuccess');
			$this->load->model("studentop");
			$this->studentop->step2();
			$this->step3();
		}
	}
	public function so()
	{
		$this->load->library("session");
		$this->session->sess_destroy();
		redirect(base_url());
	}
	public function home(){
		$this->load->model("studentop");
		$this->studentop->updateDetails();
		$data['rank']=$this->studentop->suggest();
		$data['profiles']=$this->studentop->getProfiledata($data);
		$this->load->view("student/home/header");
		$this->load->view("student/home/index",$data);
		$this->load->view("student/home/footer");
	}
	public function updates()
	{
		$this->load->model("studentop");
		$row=$this->studentop->getupdates();
		$data['row']=$row;
		$this->load->view('student/home/header');
		$this->load->view('student/home/updates',$data);
		$this->load->view('student/home/footer');		
	}
	public function study(){
		$this->load->model("studentop");
		$data['query']=$this->studentop->studyresponse();
		$this->load->view("student/home/header");
		$this->load->view("student/home/study",$data);
		$this->load->view("student/home/footer");
	}
	public function studyrequest()
	{
		$this->load->model("studentop");
		$this->studentop->savestudyrequest();
		redirect("student/study");		
	}
	public function deletestudyrequest()
	{
		$this->load->model("studentop");
		$this->studentop->deletestudyrequest();
		$success="<div class='alert alert-success'>Request Deleted..</div>";
		$this->session->set_flashdata("success",$success);
		redirect("student/study");			
	}
	public function guide(){
		$this->load->model("studentop");
		$data['chat']=$this->studentop->mychats();
		$this->load->view("student/home/header");
		$this->load->view("student/home/guide",$data);
		$this->load->view("student/home/footer");
	}
	public function suggestguide(){
		$this->load->model("studentop");
		$data['rank']=$this->studentop->suggest();
		$data['profiles']=$this->studentop->getProfiledata($data);
		$this->load->view("student/home/header");
		$this->load->view("student/home/suggest",$data);
		$this->load->view("student/home/footer");
	}
	public function newchat($pid=""){
		$pid=$this->encrypt->decode($_GET['pid']);
		$this->load->model("studentop");
		$data['profile']=$this->studentop->newchat($pid);
		$this->load->view("student/home/header");
		$this->load->view("student/home/newchat",$data);
		$this->load->view("student/home/footer");
	}
	public function sendnewchat(){
		$this->load->model("studentop");
		$this->studentop->sendnewchat();
	}
	public function sendmsg(){
		$this->load->model("studentop");
		$this->studentop->sendmsg();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */