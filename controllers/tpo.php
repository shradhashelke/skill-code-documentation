<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tpo extends CI_Controller {
	public function Tpo(){
		parent::__construct();
		if($this->session->userdata("userdetails")==NULL){
			redirect(base_url());
		}else{
			$obj=$this->session->userdata("loggedinas");
			if($obj=="student"){
				redirect("student/home");
			}elseif($obj=="teacher"){
				redirect("teacher/home");
			}elseif($obj=="professional"){
				redirect("professional/home");
			}
		}
	}
	function loadDatabase($dbname)
	{
		$config['hostname'] = "localhost";
		$config['username'] = "root";
		$config['password'] = "";
		$config['database'] = $dbname;
		$config['dbdriver'] = "mysql";
		$config['dbprefix'] = "";
		$config['pconnect'] = FALSE;
		$config['db_debug'] = TRUE;
		$config['cache_on'] = FALSE;
		$config['cachedir'] = "";
		$config['char_set'] = "utf8";
		$config['dbcollat'] = "utf8_general_ci";
		return $config;
	}
	public function index()
	{
		//$this->load->view('home/index');
		$this->load->view('home/header');
		$this->load->view('home/index');
		$this->load->view('home/footer');
	}
	function step1()
	{
		$this->load->view("registration/tpo/header");
		$this->load->view("registration/tpo/step1");
		$this->load->view("registration/tpo/footer");
	}
	function savestep1()
	{
		$this->load->model("tpoop");
		$this->tpoop->step1();
	}
	function step2()
	{
		$this->nextstep();		
	}
	function nextstep(){
		$this->load->library('form_validation');
		$this->load->view("registration/tpo/header");
		$this->load->view("registration/tpo/step2");
		$this->load->view("registration/tpo/footer");
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
			$this->nextstep();
		}
		else
		{
			//$this->load->view('formsuccess');
			$this->load->model("tpoop");
			$this->tpoop->step2();
			$this->step3();
		}
	}
	public function step3()
	{
		$this->load->view('registration/tpo/header');
		$this->load->view('registration/tpo/step3');
		$this->load->view('registration/tpo/footer');
	}
	public  function home(){
		$this->load->view('tpo/home/header');
		$this->load->view('tpo/home/index');
		$this->load->view('tpo/home/footer');
	}
	public  function request(){
		$this->load->model("administrator");
		$this->load->view('professional/home/header');
		$this->load->view('professional/home/request');
		$this->load->view('professional/home/footer');
	}
	/*
	 * show profile
	 */
	public function profile(){
		$this->load->view("professional/profile/index");
	}
	/*
	 * request exam module
	 */
	public function requestexam(){
		$this->load->model("professionalop");
		$this->professionalop->acceptexamrequest();
		$this->session->set_flashdata("msg","Your Request has been submitted");
		redirect("professional/request");
	}
	public function updates()
	{
		$this->load->model("tpoop");
		$row=$this->tpoop->getupdates();
		$data['row']=$row;
		$this->load->view('tpo/home/header');
		$this->load->view('tpo/home/updates',$data);
		$this->load->view('tpo/home/footer');		
	}
	/*
	 * show exam stat
	 */
	public function exam()
	{
		$this->load->model("tpoop");
		$this->load->model("administrator");
		$this->load->view('tpo/home/header');
		$this->load->view('tpo/home/exam');
		$this->load->view('tpo/home/footer');		
	}
	public function chat()
	{
		$data['id']=$_GET['id'];
		$config=$this->loadDatabase("skill");
		$db1=$this->load->database($config,TRUE);
		$data['db1']=$db1;
		$this->load->model("administrator");
		$this->load->view('tpo/home/header');
		$this->load->view('tpo/home/chat',$data);
		$this->load->view('tpo/home/footer');		
	}
	public function so()
	{
		$this->load->library("session");
		$this->session->sess_destroy();
		redirect(base_url());
	}
	/*
	 * list dept wise students
	 */
	public function students(){
		$this->load->model("tpoop");
		$data['list']=$this->tpoop->getDeptList();
		$this->load->view('tpo/home/header');
		$this->load->view('tpo/home/students',$data);
		$this->load->view('tpo/home/footer');
	}
	/*
	 * list teachers
	 */
	public function teachers(){
		$this->load->view('tpo/home/header');
		$this->load->view('tpo/home/teachers');
		$this->load->view('tpo/home/footer');
	}
}

/* End of file secure.php */
/* Location: ./application/controllers/secure.php */