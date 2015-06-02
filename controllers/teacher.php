<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Teacher extends CI_Controller {
	/*
	 * Controller
	 */
	public function Teacher(){
		parent::__construct();
		if($this->session->userdata("userdetails")==NULL){
			redirect(base_url());
		}else{
			$obj=$this->session->userdata("loggedinas");
			if($obj=="student"){
				redirect("student/home");
			}elseif($obj=="professional"){
				redirect("professional/home");
			}elseif($obj=="tpo"){
				redirect("tpo/home");
			}
		}
	}
	public function savestep1()
	{
		$this->load->model("teacherop");
		$this->teacherop->step1();
	}
	public function step1()
	{
		$this->load->view('registration/teacher/header');
		$this->load->view('registration/teacher/step1');
		$this->load->view('registration/teacher/footer');
	}
	public function step2()
	{
		$this->load->library('form_validation');
		$this->load->view('registration/teacher/header');
		$this->load->view('registration/teacher/step2');
		$this->load->view('registration/teacher/footer');
	}
	public function step3()
	{														$this->load->helper('captcha');                             
		$this->load->view('registration/teacher/header');
		$this->load->view('registration/teacher/step3');
		$this->load->view('registration/teacher/footer');
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
			$this->load->model("teacherop");
			$this->teacherop->step2();
			$this->step3();
		}
	}
	public function home(){

		$this->load->view("teacher/home/header");
		$this->load->view("teacher/home/index");
		$this->load->view("teacher/home/footer");
	}
	public function request(){
		$this->load->model("administrator");
		$this->load->model("teacherop");
		$this->load->view("teacher/home/header");
		$this->load->view("teacher/home/request");
		$this->load->view("teacher/home/footer");
	}
	public function studyresponse(){
		$this->load->model("administrator");
		$this->load->model("teacherop");
		$this->load->view("teacher/home/header");
		$this->load->view("teacher/home/studyresponse");
		$this->load->view("teacher/home/footer");
	}
	public function profile(){
		$this->load->model("teacherop");
		$this->load->model("administrator");
		$this->teacherop->updateDetails();
		$this->load->library('ciqrcode');
		$this->load->view("teacher/profile/header");
		$this->load->view("teacher/profile/index");
		$this->load->view("teacher/profile/footer");
	}
	public function sendstudyreply()
	{
		$config['upload_path'] = './files/studymaterial/';
		$config['allowed_types'] = 'gif|jpg|png|doc|docx|xls|xlsx|csv|txt|c|c|java|php|py|js|pdf';
		$this->load->library('upload', $config);
		if ( ! $this->upload->do_upload()){
			$error="<div class='alert alert-danger'>".$this->upload->display_errors()."</div>";
			$this->session->set_flashdata("error",$error);
			$id=$_POST['request_id'];
			redirect("teacher/studyresponse?request_id=$id");
		}
		else
		{
			$error="<div class='alert alert-success'>Response Submitted</div>";
			$this->session->set_flashdata("success",$error);
			$this->load->model("teacherop");
			$this->teacherop->savestudydata();
			redirect("teacher/request");
		}
	}
	public function so()
	{
		$this->load->library("session");
		$this->session->sess_destroy();
		redirect(base_url());
	}
	public function guide(){
		$this->load->model("teacherop");
		$data['chat']=$this->teacherop->mychats();
		$this->load->view("teacher/home/header");
		$this->load->view("teacher/home/guide",$data);
		$this->load->view("teacher/home/footer");
	}
	public function sendmsg(){
		$this->load->model("teacherop");
		$this->teacherop->sendmsg();
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */